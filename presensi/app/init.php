<?php
session_start();
date_default_timezone_set('Asia/Makassar');

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Kelas.php';
require_once __DIR__ . '/Siswa.php';
require_once __DIR__ . '/Presensi.php';
require_once __DIR__ . '/User.php';

$config = require __DIR__ . '/../config/database.php';
$db = new Database($config);
