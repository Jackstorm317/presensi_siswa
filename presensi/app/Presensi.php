<?php
class Presensi extends BaseModel
{
    private ?int $id;
    private string $nisn;
    private ?string $tanggalWaktu;   // diisi otomatis oleh database

    public function __construct(Database $db, ?int $id = null, string $nisn = '', ?string $tanggalWaktu = null)
    {
        parent::__construct($db);
        $this->id           = $id;
        $this->nisn         = trim($nisn);
        $this->tanggalWaktu = $tanggalWaktu;
    }

    // ---- getter & setter ----
    public function getId(): ?int { return $this->id; }
    public function setId(?int $v): void { $this->id = $v; }
    public function getNisn(): string { return $this->nisn; }
    public function setNisn(string $v): void { $this->nisn = trim($v); }
    public function getTanggalWaktu(): ?string { return $this->tanggalWaktu; }
    public function setTanggalWaktu(?string $v): void { $this->tanggalWaktu = $v; }

    /** Rekap hari ini (default) */
    public function getAll(): array
    {
        return $this->getRekap(date('Y-m-d'));
    }

    /** Rekap dengan INNER JOIN tiga tabel, filter tanggal + kelas + cari nama/NISN */
    public function getRekap(string $tanggal, string $keyword = '', int $idKelas = 0): array
    {
        $sql = 'SELECT p.id, s.nisn, s.nama, k.nama_kelas, p.tanggal_waktu
                FROM presensi p
                INNER JOIN siswa s ON p.nisn = s.nisn
                INNER JOIN kelas k ON s.id_kelas = k.id_kelas
                WHERE DATE(p.tanggal_waktu) = :tgl';
        $params = ['tgl' => $tanggal];
        if ($keyword !== '') {
            $sql .= ' AND (s.nama LIKE :kw1 OR s.nisn LIKE :kw2)';
            $like = '%' . addcslashes($keyword, '%_\\') . '%';
            $params['kw1'] = $like;
            $params['kw2'] = $like;
        }
        if ($idKelas > 0) {
            $sql .= ' AND k.id_kelas = :kelas';
            $params['kelas'] = $idKelas;
        }
        $sql .= ' ORDER BY p.tanggal_waktu DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Validasi NISN untuk simulasi tap kartu. Return pesan error atau null. */
    public function validateTap(): ?string
    {
        if ($this->nisn === '') {
            return 'NISN tidak boleh kosong.';
        }
        if (!preg_match('/^\d{10}$/', $this->nisn)) {
            return 'NISN harus tepat 10 digit angka.';
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $this->nisn]);
        if ((int) $stmt->fetchColumn() === 0) {
            return 'Kartu ditolak: NISN ' . $this->nisn . ' tidak terdaftar di sistem.';
        }
        return null;
    }

    /** Simpan satu baris presensi. id dan tanggal_waktu diisi otomatis oleh database. */
    public function create(): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO presensi (nisn) VALUES (:nisn)');
        $stmt->execute(['nisn' => $this->nisn]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM presensi WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
