<?php
/**
 * BaseModel: induk semua model. Menyimpan koneksi PDO (protected)
 * dan mewajibkan setiap anak mengisi method getAll().
 */
abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getConnection();
    }

    abstract public function getAll(): array;
}
