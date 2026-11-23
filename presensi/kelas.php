<?php
require_once __DIR__ . '/auth.php';

$kelas = new Kelas($db);

// ---- Hapus kelas (POST) ----
if (isPost() && ($_POST['aksi'] ?? '') === 'hapus') {
    $id = (int) ($_POST['id'] ?? 0);
    $data = $kelas->find($id);
    if (!$data) {
        flash('error', 'Kelas tidak ditemukan.');
    } else {
        $jumlah = $kelas->countSiswa($id);
        if ($jumlah > 0) {
            flash('error', 'Kelas ' . $data['nama_kelas'] . ' tidak dapat dihapus karena masih memiliki '
                . $jumlah . ' siswa. Pindahkan atau hapus siswanya terlebih dahulu.');
        } else {
            try {
                $kelas->delete($id);
                flash('success', 'Kelas ' . $data['nama_kelas'] . ' berhasil dihapus.');
            } catch (PDOException $e) {
                error_log($e->getMessage());
                flash('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data lain.');
            }
        }
    }
    redirect('kelas.php');
}

$daftar = $kelas->getAll();

$pageTitle = 'Kelola Kelas';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card">
    <div class="card-head">
        <h2>Data Kelas</h2>
        <a href="kelas_form.php" class="btn">+ Tambah Kelas</a>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>No</th><th>Nama Kelas</th><th>Jurusan</th><th>Jumlah Siswa</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$daftar): ?>
            <tr><td colspan="5" class="center">Belum ada kelas.</td></tr>
        <?php endif; ?>
        <?php foreach ($daftar as $i => $k): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($k['nama_kelas']) ?></td>
                <td><?= e($k['jurusan']) ?></td>
                <td><?= (int) $k['jumlah_siswa'] ?></td>
                <td class="aksi">
                    <a href="kelas_form.php?id=<?= (int) $k['id_kelas'] ?>" class="btn btn-light btn-sm">Ubah</a>
                    <form method="post" onsubmit="return confirm('Hapus kelas ini?');">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= (int) $k['id_kelas'] ?>">
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
