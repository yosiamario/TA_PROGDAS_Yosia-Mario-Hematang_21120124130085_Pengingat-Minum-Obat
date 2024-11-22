<?php
session_start();

if (!isset($_SESSION['obat_list'])) {
    $_SESSION['obat_list'] = [];
}


if (isset($_GET['hapus'])) {
    $index = filter_var($_GET['hapus'], FILTER_VALIDATE_INT);
    if ($index !== false && isset($_SESSION['obat_list'][$index])) {
        unset($_SESSION['obat_list'][$index]);
        $_SESSION['obat_list'] = array_values($_SESSION['obat_list']);
    }
    header('Location: daftar_obat.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_dosis'])) {
    foreach ($_SESSION['obat_list'] as $index => $obat) {
        foreach ($obat['waktu_dosis'] as $key => $waktu_dosis) {
            if (isset($_POST['status_dosis'][$index][$key])) {
                $_SESSION['obat_list'][$index]['status_dosis'][$key] = true;
            }
        }
    }
}

if (isset($_GET['hapus_semua']) && $_GET['hapus_semua'] == 'true') {
    unset($_SESSION['obat_list']);

    header('Location: daftar_obat.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Obat</title>
    <script>

function checkReminder() {
    const now = new Date();
    const currentTime = now.getHours() * 60 + now.getMinutes();
    console.log("Waktu Sekarang: " + currentTime);

    const obatList = <?php echo json_encode($_SESSION['obat_list']); ?>;

    obatList.forEach(obat => {
        const [hours, minutes] = obat.waktu_minum.split(":");
        const reminderTime = parseInt(hours) * 60 + parseInt(minutes);

        console.log("Waktu Minum Obat: " + reminderTime);

        if (currentTime === reminderTime) {

        alert('Ingat! Minum obat yah ');
        }
    });
}

setInterval(checkReminder, 20000);
</script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="container">
        <h1>Daftar Obat yang Perlu Diminum</h1>
        <P> 
        <a href="daftar_obat.php?hapus_semua=true"
        onclick="return confirm('apakah anda yakin?')"
class="delete-all-btn">Hapus Semua</a>
    </p>
    <ul>
        <form method="POST" action="daftar_obat.php">
            <ul class="obat-list">
                <?php if (count($_SESSION['obat_list']) > 0): ?>
                    <?php foreach ($_SESSION['obat_list'] as $index => $obat): ?>
                        <li class="obat-item">
                            <strong><?php echo htmlspecialchars($obat['nama']); ?></strong> - Waktu Minum:
                            <ul class="waktu-list">
                                <?php foreach ($obat['waktu_dosis'] as $key => $waktu_dosis) :?>
                                    <li>
                                        <?php echo $waktu_dosis; ?>
                                        <input type="checkbox" name="status_dosis[<?php echo $index; ?>][<?php echo $key; ?>]"
                                            <?php echo $obat['status_dosis'][$key] ? 'checked' : ''; ?>>
                                        <label>Obat sudah diminum</label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="daftar_obat.php?hapus=<?php echo $index; ?>" class="delete-btn">Hapus</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Tidak ada obat yang ditambahkan.</li>
                <?php endif; ?>
            </ul>
        </form>

        <a href="index.php" class="back-link">Kembali ke halaman utama</a>
    </div>
</body>
</html>
