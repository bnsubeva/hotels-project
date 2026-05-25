<?php
require_once __DIR__ . '/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM hotels WHERE id = ?');
    $stmt->execute([$id]);
    flash('Хотелът е изтрит успешно.');
}

redirect('index.php');
