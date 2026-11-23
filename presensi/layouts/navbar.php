<nav class="navbar">
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="index.php">Rekap Presensi</a>
    <a href="tap.php">Simulasi Tap</a>
    <a href="siswa.php">Siswa</a>
    <a href="kelas.php">Kelas</a>
    <span class="spacer"></span>
    <span class="who">Halo, <?= e($_SESSION['username']) ?></span>
    <a href="logout.php" class="logout">Logout</a>
<?php else: ?>
    <span class="who">Silakan login sebagai administrator</span>
<?php endif; ?>
</nav>
<main class="container">
<?php foreach (getFlash() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
<?php endforeach; ?>
