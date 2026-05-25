<?php
declare(strict_types=1);

session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'hotels_app';
const DB_USER = 'root';
const DB_PASS = '';
const DEFAULT_ADMIN_HASH = '$2y$10$k1F7l98IPg7UpOQVyxu1x.3x8.KwzkEmU50x1PO6toOhvWQt8H1oa';
const DEFAULT_USER_HASH = '$2y$10$RSAs37WEBWOoLyreO8C2M.aZekkKL/WYpSDVMFU/V8RYs5QR5bfcC';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function currentUserRole(): string
{
    return $_SESSION['role'] ?? 'guest';
}

function isAdmin(): bool
{
    return currentUserRole() === 'admin';
}

function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {
        flash('Нямате права за тази операция.');
        redirect('index.php');
    }
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function textLength(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }

    $current = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $current;
}
