<?php
require_once __DIR__ . '/auth.php';

$presensi = new Presensi($db);

// ---- Hapus log presensi (POST) ----
if (isPost() && ($_POST['aksi'] ?? '') === 'hapus') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $presensi->delete($id);
        flash('success', 'Log presensi berhasil dihapus.');
    }
    $back = http_build_query([
        'tanggal' => $_POST['tanggal'] ?? '',
        'q'       => $_POST['q'] ?? '',
        'kelas'   => $_POST['kelas'] ?? '',
    ]);
    redirect('index.php?' . $back);
}

// ---- Filter: tanggal (default hari ini) + pencarian nama/NISN ----
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$d = DateTime::createFromFormat('Y-m-d', $tanggal);
if (!$d || $d->format('Y-m-d') !== $tanggal) {
    $tanggal = date('Y-m-d');
}
$q = trim($_GET['q'] ?? '');
$idKelas = (int) ($_GET['kelas'] ?? 0);
$daftarKelas = (new Kelas($db))->getAll();

$rekap = $presensi->getRekap($tanggal, $q, $idKelas);
$hariIni = ($tanggal === date('Y-m-d'));

$pageTitle = 'Rekap Presensi';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card">
    <h2>Rekap Presensi <?= $hariIni ? 'Hari Ini' : '' ?></h2>
    <p class="muted">Tanggal: <?= e(date('d-m-Y', strtotime($tanggal))) ?> &middot; <?= count($rekap) ?> siswa tap kartu</p>

    <form method="get" class="filter">
        <input type="date" name="tanggal" value="<?= e($tanggal) ?>">
        <select name="kelas">
            <option value="0">Semua kelas</option>
            <?php foreach ($daftarKelas as $k): ?>
                <option value="<?= (int) $k['id_kelas'] ?>" <?= $idKelas === (int) $k['id_kelas'] ? 'selected' : '' ?>><?= e($k['nama_kelas']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau NISN">
        <button type="submit" class="btn">Cari / Filter</button>
        <a href="index.php" class="btn btn-light">Reset</a>
    </form>

    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Waktu</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$rekap): ?>
            <tr><td colspan="6" class="center">Belum ada data presensi.</td></tr>
        <?php endif; ?>
        <?php foreach ($rekap as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($r['nisn']) ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['nama_kelas']) ?></td>
                <td><?= e(date('H:i:s', strtotime($r['tanggal_waktu']))) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Hapus log presensi ini?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                        <input type="hidden" name="q" value="<?= e($q) ?>">
                        <input type="hidden" name="kelas" value="<?= $idKelas ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
