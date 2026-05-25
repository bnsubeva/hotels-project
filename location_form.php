<?php
require_once __DIR__ . '/config.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];
$location = ['name' => '', 'country' => ''];

if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM locations WHERE id = ?');
    $stmt->execute([$id]);
    $location = $stmt->fetch();

    if (!$location) {
        flash('Населеното място не е намерено.');
        redirect('locations.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = [
        'name' => trim($_POST['name'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
    ];

    if (textLength($location['name']) < 2) {
        $errors[] = 'Името на населеното място трябва да е поне 2 символа.';
    }

    if (textLength($location['country']) < 2) {
        $errors[] = 'Името на държавата трябва да е поне 2 символа.';
    }

    if (!$errors) {
        try {
            if ($isEdit) {
                $stmt = db()->prepare('UPDATE locations SET name = ?, country = ? WHERE id = ?');
                $stmt->execute([$location['name'], $location['country'], $id]);
                flash('Населеното място е обновено успешно.');
            } else {
                $stmt = db()->prepare('INSERT INTO locations (name, country) VALUES (?, ?)');
                $stmt->execute([$location['name'], $location['country']]);
                flash('Населеното място е добавено успешно.');
            }

            redirect('locations.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'Такова населено място вече съществува.';
            } else {
                throw $exception;
            }
        }
    }
}

$pageTitle = $isEdit ? 'Редакция на място' : 'Ново място';
require __DIR__ . '/header.php';
?>
<section class="page-head">
    <div>
        <h1><?= $isEdit ? 'Редакция на населено място' : 'Добавяне на населено място' ?></h1>
        <p>Въведете име на населено място и държава.</p>
    </div>
    <a class="button secondary" href="locations.php">Назад</a>
</section>

<?php if ($errors): ?>
    <div class="errors">
        <?php foreach ($errors as $error): ?>
            <p><?= h($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" class="form panel" data-validate>
    <div class="grid two">
        <label>
            Населено място
            <input type="text" name="name" required minlength="2" value="<?= h($location['name']) ?>">
        </label>
        <label>
            Държава
            <input type="text" name="country" required minlength="2" value="<?= h($location['country']) ?>">
        </label>
    </div>
    <button type="submit">Запази</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
