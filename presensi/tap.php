<?php
require_once __DIR__ . '/auth.php';

$error = '';

if (isPost()) {
    $presensi = new Presensi($db);
    $presensi->setNisn($_POST['nisn'] ?? '');

    $error = $presensi->validateTap() ?? '';
    if ($error === '') {
        $presensi->create();
        $siswa = (new Siswa($db))->find($presensi->getNisn());
        flash('success', 'Presensi berhasil: ' . $siswa['nama'] . ' (' . $presensi->getNisn() . ') tercatat pukul ' . date('H:i:s') . '.');
        redirect('tap.php');
    }
}

$pageTitle = 'Simulasi Tap Kartu';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card card-narrow">
    <h2>Simulasi Tap Kartu</h2>
    <p class="muted">Form ini mewakili mesin RFID: hanya mengirim NISN. ID dan waktu diisi otomatis oleh database.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <label for="nisn">NISN</label>
        <input type="text" id="nisn" name="nisn" maxlength="10" inputmode="numeric"
               placeholder="10 digit angka" value="<?= e($_POST['nisn'] ?? '') ?>" autofocus>
        <button type="submit" class="btn">Tap Kartu</button>
        <a href="index.php" class="btn btn-light">Lihat Rekap</a>
    </form>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
