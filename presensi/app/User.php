<?php
class User extends BaseModel
{
    private string $username;

    public function __construct(Database $db, string $username = '')
    {
        parent::__construct($db);
        $this->username = trim($username);
    }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $v): void { $this->username = trim($v); }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT id_user, username FROM users')->fetchAll();
    }

    /** Cek login. Return data user atau null jika gagal. */
    public function login(string $password): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id_user, username, password FROM users WHERE username = :u');
        $stmt->execute(['u' => $this->username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
