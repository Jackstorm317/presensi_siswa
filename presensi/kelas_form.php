<?php
require_once __DIR__ . '/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$isNew = ($id === 0);
$errors = [];
$nama = '';
$jurusan = '';

if (!$isNew) {
    $data = (new Kelas($db))->find($id);
    if (!$data) {
        flash('error', 'Kelas tidak ditemukan.');
        redirect('kelas.php');
    }
    $nama = $data['nama_kelas'];
    $jurusan = $data['jurusan'];
}

if (isPost()) {
    $kelas = new Kelas($db, $isNew ? null : $id, $_POST['nama_kelas'] ?? '', $_POST['jurusan'] ?? '');
    $nama = $kelas->getNamaKelas();
    $jurusan = $kelas->getJurusan();
    $errors = $kelas->validate();

    if (!$errors) {
        if ($isNew) {
            $kelas->create();
            flash('success', 'Kelas berhasil ditambahkan.');
        } else {
            $kelas->update();
            flash('success', 'Kelas berhasil diubah.');
        }
        redirect('kelas.php');
    }
}

$pageTitle = $isNew ? 'Tambah Kelas' : 'Ubah Kelas';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card card-narrow">
    <h2><?= e($pageTitle) ?></h2>
    <form method="post" autocomplete="off">
        <label for="nama_kelas">Nama Kelas</label>
        <input type="text" id="nama_kelas" name="nama_kelas" value="<?= e($nama) ?>" placeholder="Contoh: XI PPLG 1">
        <?= fieldError($errors, 'nama_kelas') ?>

        <label for="jurusan">Jurusan</label>
        <input type="text" id="jurusan" name="jurusan" value="<?= e($jurusan) ?>">
        <?= fieldError($errors, 'jurusan') ?>

        <button type="submit" class="btn">Simpan</button>
        <a href="kelas.php" class="btn btn-light">Batal</a>
    </form>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
