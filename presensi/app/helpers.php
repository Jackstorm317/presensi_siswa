<?php
/** Escape output (cegah XSS) */
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/** Pesan satu kali tampil (flash) */
function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/** Tampilkan pesan error di bawah kolom form */
function fieldError(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<small class="error">' . e($errors[$key]) . '</small>' : '';
}
