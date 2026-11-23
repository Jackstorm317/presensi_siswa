<?php
class Siswa extends BaseModel
{
    private string $nisn;      // selalu string agar angka 0 di depan tidak hilang
    private string $nama;
    private string $alamat;
    private int $idKelas;

    public function __construct(Database $db, string $nisn = '', string $nama = '', string $alamat = '', int $idKelas = 0)
    {
        parent::__construct($db);
        $this->nisn    = trim($nisn);
        $this->nama    = trim($nama);
        $this->alamat  = trim($alamat);
        $this->idKelas = $idKelas;
    }

    // ---- getter & setter ----
    public function getNisn(): string   { return $this->nisn; }
    public function setNisn(string $v): void { $this->nisn = trim($v); }
    public function getNama(): string   { return $this->nama; }
    public function setNama(string $v): void { $this->nama = trim($v); }
    public function getAlamat(): string { return $this->alamat; }
    public function setAlamat(string $v): void { $this->alamat = trim($v); }
    public function getIdKelas(): int   { return $this->idKelas; }
    public function setIdKelas(int $v): void { $this->idKelas = $v; }

    /** Daftar siswa + nama kelas (INNER JOIN), bisa dicari nama / NISN */
    public function getAll(string $keyword = ''): array
    {
        $sql = 'SELECT s.nisn, s.nama, s.alamat, k.nama_kelas, k.jurusan
                FROM siswa s
                INNER JOIN kelas k ON s.id_kelas = k.id_kelas';
        $params = [];
        if ($keyword !== '') {
            $sql .= ' WHERE s.nama LIKE :kw1 OR s.nisn LIKE :kw2';
            $like = '%' . addcslashes($keyword, '%_\\') . '%';
            $params = ['kw1' => $like, 'kw2' => $like];
        }
        $sql .= ' ORDER BY k.nama_kelas, s.nama';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(string $nisn): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function validate(bool $isNew): array
    {
        $errors = [];

        if ($this->nisn === '') {
            $errors['nisn'] = 'NISN tidak boleh kosong.';
        } elseif (!preg_match('/^\d{10}$/', $this->nisn)) {
            $errors['nisn'] = 'NISN harus tepat 10 digit angka.';
        } elseif ($isNew && $this->find($this->nisn) !== null) {
            $errors['nisn'] = 'NISN sudah terdaftar.';
        }

        if ($this->nama === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($this->nama) > 100) {
            $errors['nama'] = 'Nama maksimal 100 karakter.';
        }

        if (mb_strlen($this->alamat) > 255) {
            $errors['alamat'] = 'Alamat maksimal 255 karakter.';
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $this->idKelas]);
        if ((int) $stmt->fetchColumn() === 0) {
            $errors['id_kelas'] = 'Kelas wajib dipilih dari daftar kelas yang ada.';
        }
        return $errors;
    }

    public function create(): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES (:nisn, :nama, :alamat, :kelas)'
        );
        $stmt->execute([
            'nisn'   => $this->nisn,
            'nama'   => $this->nama,
            'alamat' => $this->alamat === '' ? null : $this->alamat,
            'kelas'  => $this->idKelas,
        ]);
    }

    public function update(): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE siswa SET nama = :nama, alamat = :alamat, id_kelas = :kelas WHERE nisn = :nisn'
        );
        $stmt->execute([
            'nama'   => $this->nama,
            'alamat' => $this->alamat === '' ? null : $this->alamat,
            'kelas'  => $this->idKelas,
            'nisn'   => $this->nisn,
        ]);
    }

    public function hasPresensi(string $nisn): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM presensi WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function delete(string $nisn): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
    }
}
