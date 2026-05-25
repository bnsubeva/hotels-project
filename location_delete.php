<?php
require_once __DIR__ . '/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('locations.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = db()->prepare('SELECT COUNT(*) FROM hotels WHERE location_id = ?');
    $stmt->execute([$id]);

    if ((int) $stmt->fetchColumn() > 0) {
        flash('Не може да изтриете населено място, към което има хотели.');
    } else {
        $delete = db()->prepare('DELETE FROM locations WHERE id = ?');
        $delete->execute([$id]);
        flash('Населеното място е изтрито успешно.');
    }
}

redirect('locations.php');
