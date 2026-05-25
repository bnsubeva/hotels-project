<?php
$pageTitle = $pageTitle ?? 'Хотели';
$message = flash();
?>
<!doctype html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> | Hotels App</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">Hotels App</a>
    <?php if (isLoggedIn()): ?>
        <nav class="nav">
            <a href="index.php">Хотели</a>
            <?php if (isAdmin()): ?>
                <a href="locations.php">Населени места</a>
            <?php endif; ?>
            <a href="reports.php">Справки</a>
            <span class="role-badge"><?= isAdmin() ? 'Администратор' : 'Потребител' ?></span>
            <a href="logout.php">Изход</a>
        </nav>
    <?php endif; ?>
</header>
<main class="shell">
    <?php if ($message): ?>
        <div class="flash"><?= h($message) ?></div>
    <?php endif; ?>
