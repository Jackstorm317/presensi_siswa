<?php
require_once __DIR__ . '/app/init.php';

if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';
$username = '';

if (isPost()) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $user = (new User($db, $username))->login($password);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            redirect('index.php');
        }
        // pesan umum: tidak menyebutkan bagian mana yang salah
        $error = 'Login gagal: username atau password salah.';
    }
}

$pageTitle = 'Login';
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/navbar.php';
?>
<div class="card card-narrow">
    <h2>Login Administrator</h2>
    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <button type="submit" class="btn">Login</button>
    </form>
</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
