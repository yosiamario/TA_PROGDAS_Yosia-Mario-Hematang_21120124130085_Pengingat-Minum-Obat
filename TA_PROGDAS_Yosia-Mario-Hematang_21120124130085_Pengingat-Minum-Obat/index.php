<?php
session_start();

if (!isset($_SESSION['obat_list'])) {
    $_SESSION['obat_list'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_obat']) && isset($_POST['waktu_minum']) && isset($_POST['frekuensi'])) {
    $nama_obat = (string)$_POST['nama_obat'];
    $waktu_minum = $_POST['waktu_minum'];
    $frekuensi = (int)$_POST['frekuensi'];

    list($jam, $menit) = explode(":", $waktu_minum);
    $waktu_minum_menit = ($jam * 60) + $menit;

    $obat = [
        'nama' => $nama_obat,
        'waktu_minum' => $waktu_minum,
        'frekuensi' => $frekuensi,
        'waktu_dosis' => [],
        'status_dosis' => []
    ];

    $sisa_waktu = 1440 - $waktu_minum_menit; 

    if ($frekuensi > 1) {
        $interval = ceil($sisa_waktu / $frekuensi);
        for ($i = 1; $i < $frekuensi; $i++) {
            $waktu_dosis_menit = $waktu_minum_menit + ($interval * $i);
            $jam_dosis = floor($waktu_dosis_menit / 60);
            $menit_dosis = $waktu_dosis_menit % 60;
            $obat['waktu_dosis'][] = sprintf("%02d:%02d", $jam_dosis, $menit_dosis);
            $obat['status_dosis'][] = false; 
        }
    }

    array_unshift($obat['waktu_dosis'], $waktu_minum);
    array_unshift($obat['status_dosis'], false);

    $_SESSION['obat_list'][] = $obat;

    header('Location: daftar_obat.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Minum Obat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Pengingat Minum Obat</h1>
        
        <form method="POST" action="index.php" class="form-container">
            <div class="input-group">
                <label for="nama_obat">Nama Obat:</label>
                <input type="text" id="nama_obat" name="nama_obat" placeholder="Masukkan Nama Obat"
                pattern="^[A-Za-z\s]+$" title="Nama obat hanya boleh berisi huruf dan spasi" required>
            </div>
            <div class="input-group">
                <label for="waktu_minum">Waktu Minum:</label>
                <input type="time" id="waktu_minum" name="waktu_minum" required>
            </div>
            <div class="input-group">
                <label for="frekuensi">Dosis (1-3 kali):</label>
                <input type="number" id="frekuensi" name="frekuensi" min="1" max="3" required>
            </div>
            <button type="submit" class="submit-btn">Tambah Obat</button>
        </form>

    </div>
    <script>
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notifikasi diaktifkan!');
                } else {
                    console.log('Notifikasi ditolak.');
                }
            });
        }

        function kirimNotifikasi(namaObat) {
            if (Notification.permission === 'granted') {
                navigator.serviceWorker.ready.then(function(registration) {
                    registration.showNotification('Pengingat Minum Obat', {
                        body: `Waktunya minum obat: ${namaObat}`,
                    });
                });
            }
        }
    </script>

</body>
</html>
