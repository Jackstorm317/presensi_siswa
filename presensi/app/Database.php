<?php
/**
 * Class Database: membungkus koneksi PDO (ERRMODE_EXCEPTION).
 * Koneksi dibuat di dalam constructor.
 */
class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            // samakan zona waktu database dengan WITA (Samarinda)
            $this->pdo->exec("SET time_zone = '+08:00'");
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Koneksi database gagal. Pastikan MySQL/MariaDB berjalan dan database sudah di-import.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
