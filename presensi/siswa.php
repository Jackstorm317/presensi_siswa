<?php
require_once __DIR__ . '/auth.php';

$siswa = new Siswa($db);

// ---- Hapus siswa (POST) ----
if (isPost() && ($_POST['aksi'] ?? '') === 'hapus') {
    $nisn = trim($_POST['nisn'] ?? '');
    $data = $siswa->find($nisn);
    if (!$data) {
        flash('error', 'Siswa tidak ditemukan.');
    } elseif ($siswa->hasPresensi($nisn)) {
        flash('error', 'Siswa ' . $data['nama'] . ' tidak dapat dihapus karena sudah memiliki log presensi. Hapus log presensinya terlebih dahulu.');
    } else {
        $siswa->delete($nisn);
        flash('success', 'Siswa ' . $data['nama'] . ' berhasil dihapus.');
    }
    redirect('siswa.php');
}

$q = trim($_GET['q'] ?? '');
$daftar = $siswa->getAll($q);

$pageTitle = 'Kelola Siswa';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card">
    <div class="card-head">
        <h2>Data Siswa</h2>
        <a href="siswa_form.php" class="btn">+ Tambah Siswa</a>
    </div>

    <form method="get" class="filter">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau NISN">
        <button type="submit" class="btn">Cari</button>
        <a href="siswa.php" class="btn btn-light">Reset</a>
    </form>

    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Alamat</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$daftar): ?>
            <tr><td colspan="6" class="center">Data siswa tidak ditemukan.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $i => $s): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($s['nisn']) ?></td>
                <td><?= e($s['nama']) ?></td>
                <td><?= e($s['nama_kelas']) ?></td>
                <td><?= e($s['alamat'] ?? '-') ?></td>
                <td class="aksi">
                    <a href="siswa_form.php?nisn=<?= e($s['nisn']) ?>" class="btn btn-light btn-sm">Ubah</a>
                    <form method="post" onsubmit="return confirm('Hapus siswa ini?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="nisn" value="<?= e($s['nisn']) ?>">
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
