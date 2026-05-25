<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $errors[] = 'Въведете потребителско име.';
    }

    if ($password === '') {
        $errors[] = 'Въведете парола.';
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $defaultAdminLogin = $user
            && $user['username'] === 'admin'
            && $user['password_hash'] === DEFAULT_ADMIN_HASH
            && $password === 'admin123';
        $defaultUserLogin = $user
            && $user['username'] === 'user'
            && $user['password_hash'] === DEFAULT_USER_HASH
            && $password === 'user123';

        if ($user && (password_verify($password, $user['password_hash']) || $defaultAdminLogin || $defaultUserLogin)) {
            if (($defaultAdminLogin || $defaultUserLogin) && !password_verify($password, $user['password_hash'])) {
                $update = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                $update->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }

            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            redirect('index.php');
        }

        $errors[] = 'Невалидно потребителско име или парола.';
    }
}

$pageTitle = 'Вход';
require __DIR__ . '/header.php';
?>
<section class="auth-panel">
    <h1>Вход в системата</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?= h($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form" data-validate>
        <label>
            Потребителско име
            <input type="text" name="username" required minlength="3" value="<?= h($_POST['username'] ?? '') ?>">
        </label>
        <label>
            Парола
            <input type="password" name="password" required minlength="4">
        </label>
        <button type="submit">Вход</button>
    </form>
</section>
<?php require __DIR__ . '/footer.php'; ?>
