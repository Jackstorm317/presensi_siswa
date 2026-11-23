<?php
// Auth guard: di-require paling atas setiap halaman terproteksi
require_once __DIR__ . '/app/init.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}
