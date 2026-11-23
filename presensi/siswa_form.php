<?php
require_once __DIR__ . '/auth.php';

$nisnParam = trim($_GET['nisn'] ?? '');
$isNew = ($nisnParam === '');
$errors = [];
$v = ['nisn' => '', 'nama' => '', 'alamat' => '', 'id_kelas' => 0];

$daftarKelas = (new Kelas($db))->getAll();

if (!$isNew) {
    $data = (new Siswa($db))->find($nisnParam);
    if (!$data) {
        flash('error', 'Siswa tidak ditemukan.');
        redirect('siswa.php');
    }
    $v = [
        'nisn'     => $data['nisn'],
        'nama'     => $data['nama'],
        'alamat'   => $data['alamat'] ?? '',
        'id_kelas' => (int) $data['id_kelas'],
    ];
}

if (isPost()) {
    // NISN saat ubah diambil dari URL (primary key tidak boleh diganti)
    $nisn = $isNew ? ($_POST['nisn'] ?? '') : $nisnParam;
    $siswa = new Siswa($db, $nisn, $_POST['nama'] ?? '', $_POST['alamat'] ?? '', (int) ($_POST['id_kelas'] ?? 0));

    $v = [
        'nisn'     => $siswa->getNisn(),
        'nama'     => $siswa->getNama(),
        'alamat'   => $siswa->getAlamat(),
        'id_kelas' => $siswa->getIdKelas(),
    ];
    $errors = $siswa->validate($isNew);

    if (!$errors) {
        if ($isNew) {
            $siswa->create();
            flash('success', 'Siswa berhasil ditambahkan.');
        } else {
            $siswa->update();
            flash('success', 'Data siswa berhasil diubah.');
        }
        redirect('siswa.php');
    }
}

$pageTitle = $isNew ? 'Tambah Siswa' : 'Ubah Siswa';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card card-narrow">
    <h2><?= e($pageTitle) ?></h2>
    <form method="post" autocomplete="off">
        <label for="nisn">NISN (10 digit angka)</label>
        <input type="text" id="nisn" name="nisn" maxlength="10" inputmode="numeric"
               value="<?= e($v['nisn']) ?>" <?= $isNew ? '' : 'readonly' ?>>
        <?= fieldError($errors, 'nisn') ?>

        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" value="<?= e($v['nama']) ?>">
        <?= fieldError($errors, 'nama') ?>

        <label for="alamat">Alamat (boleh kosong)</label>
        <textarea id="alamat" name="alamat" rows="3"><?= e($v['alamat']) ?></textarea>
        <?= fieldError($errors, 'alamat') ?>

        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas">
            <option value="0">-- Pilih kelas --</option>
            <?php foreach ($daftarKelas as $k): ?>
                <option value="<?= (int) $k['id_kelas'] ?>" <?= $v['id_kelas'] === (int) $k['id_kelas'] ? 'selected' : '' ?>>
                    <?= e($k['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= fieldError($errors, 'id_kelas') ?>

        <button type="submit" class="btn">Simpan</button>
        <a href="siswa.php" class="btn btn-light">Batal</a>
    </form>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
