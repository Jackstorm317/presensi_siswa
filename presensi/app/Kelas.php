<?php
class Kelas extends BaseModel
{
    private ?int $idKelas;
    private string $namaKelas;
    private string $jurusan;

    public function __construct(Database $db, ?int $idKelas = null, string $namaKelas = '', string $jurusan = '')
    {
        parent::__construct($db);
        $this->idKelas   = $idKelas;
        $this->namaKelas = trim($namaKelas);
        $this->jurusan   = trim($jurusan);
    }

    // ---- getter & setter ----
    public function getIdKelas(): ?int      { return $this->idKelas; }
    public function setIdKelas(?int $v): void { $this->idKelas = $v; }
    public function getNamaKelas(): string  { return $this->namaKelas; }
    public function setNamaKelas(string $v): void { $this->namaKelas = trim($v); }
    public function getJurusan(): string    { return $this->jurusan; }
    public function setJurusan(string $v): void { $this->jurusan = trim($v); }

    /** Semua kelas + jumlah siswanya */
    public function getAll(): array
    {
        $sql = 'SELECT k.id_kelas, k.nama_kelas, k.jurusan, COUNT(s.nisn) AS jumlah_siswa
                FROM kelas k
                LEFT JOIN siswa s ON s.id_kelas = k.id_kelas
                GROUP BY k.id_kelas, k.nama_kelas, k.jurusan
                ORDER BY k.nama_kelas';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Validasi sisi server. Return array error per kolom. */
    public function validate(): array
    {
        $errors = [];
        if ($this->namaKelas === '') {
            $errors['nama_kelas'] = 'Nama kelas wajib diisi.';
        } elseif (mb_strlen($this->namaKelas) > 20) {
            $errors['nama_kelas'] = 'Nama kelas maksimal 20 karakter.';
        } else {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM kelas WHERE nama_kelas = :n AND id_kelas <> :id'
            );
            $stmt->execute(['n' => $this->namaKelas, 'id' => $this->idKelas ?? 0]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors['nama_kelas'] = 'Nama kelas sudah ada, gunakan nama lain.';
            }
        }
        if ($this->jurusan === '') {
            $errors['jurusan'] = 'Jurusan wajib diisi.';
        } elseif (mb_strlen($this->jurusan) > 50) {
            $errors['jurusan'] = 'Jurusan maksimal 50 karakter.';
        }
        return $errors;
    }

    public function create(): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO kelas (nama_kelas, jurusan) VALUES (:n, :j)');
        $stmt->execute(['n' => $this->namaKelas, 'j' => $this->jurusan]);
    }

    public function update(): void
    {
        $stmt = $this->pdo->prepare('UPDATE kelas SET nama_kelas = :n, jurusan = :j WHERE id_kelas = :id');
        $stmt->execute(['n' => $this->namaKelas, 'j' => $this->jurusan, 'id' => $this->idKelas]);
    }

    public function countSiswa(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM siswa WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
    }
}
