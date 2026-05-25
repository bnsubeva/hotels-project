<?php
require_once __DIR__ . '/config.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

$locations = db()->query('SELECT * FROM locations ORDER BY country, name')->fetchAll();

$hotel = [
    'name' => '',
    'image_url' => '',
    'apartments' => 0,
    'studios' => 0,
    'offices' => 0,
    'has_restaurant' => 0,
    'has_spa' => 0,
    'has_pool' => 0,
    'has_discotheque' => 0,
    'location_id' => '',
];

if ($isEdit) {
    $stmt = db()->prepare('SELECT * FROM hotels WHERE id = ?');
    $stmt->execute([$id]);
    $hotel = $stmt->fetch();

    if (!$hotel) {
        flash('Хотелът не е намерен.');
        redirect('index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel = [
        'name' => trim($_POST['name'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'apartments' => (int) ($_POST['apartments'] ?? 0),
        'studios' => (int) ($_POST['studios'] ?? 0),
        'offices' => (int) ($_POST['offices'] ?? 0),
        'has_restaurant' => isset($_POST['has_restaurant']) ? 1 : 0,
        'has_spa' => isset($_POST['has_spa']) ? 1 : 0,
        'has_pool' => isset($_POST['has_pool']) ? 1 : 0,
        'has_discotheque' => isset($_POST['has_discotheque']) ? 1 : 0,
        'location_id' => (int) ($_POST['location_id'] ?? 0),
    ];

    if (textLength($hotel['name']) < 2) {
        $errors[] = 'Името на хотела трябва да е поне 2 символа.';
    }

    if ($hotel['image_url'] !== '' && !filter_var($hotel['image_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'URL адресът на снимката не е валиден.';
    }

    foreach (['apartments' => 'апартаменти', 'studios' => 'студия', 'offices' => 'офиси'] as $field => $label) {
        if ($hotel[$field] < 0) {
            $errors[] = 'Броят ' . $label . ' не може да бъде отрицателен.';
        }
    }

    $locationIds = array_map(static fn(array $location): int => (int) $location['id'], $locations);
    if (!in_array((int) $hotel['location_id'], $locationIds, true)) {
        $errors[] = 'Изберете валидно населено място.';
    }

    if (!$errors) {
        if ($isEdit) {
            $stmt = db()->prepare(
                'UPDATE hotels
                 SET name = ?, image_url = ?, apartments = ?, studios = ?, offices = ?,
                     has_restaurant = ?, has_spa = ?, has_pool = ?, has_discotheque = ?, location_id = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                $hotel['name'],
                $hotel['image_url'],
                $hotel['apartments'],
                $hotel['studios'],
                $hotel['offices'],
                $hotel['has_restaurant'],
                $hotel['has_spa'],
                $hotel['has_pool'],
                $hotel['has_discotheque'],
                $hotel['location_id'],
                $id,
            ]);
            flash('Хотелът е обновен успешно.');
        } else {
            $stmt = db()->prepare(
                'INSERT INTO hotels
                 (name, image_url, apartments, studios, offices, has_restaurant, has_spa, has_pool, has_discotheque, location_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $hotel['name'],
                $hotel['image_url'],
                $hotel['apartments'],
                $hotel['studios'],
                $hotel['offices'],
                $hotel['has_restaurant'],
                $hotel['has_spa'],
                $hotel['has_pool'],
                $hotel['has_discotheque'],
                $hotel['location_id'],
            ]);
            flash('Хотелът е добавен успешно.');
        }

        redirect('index.php');
    }
}

$pageTitle = $isEdit ? 'Редакция на хотел' : 'Нов хотел';
require __DIR__ . '/header.php';
?>
<section class="page-head">
    <div>
        <h1><?= $isEdit ? 'Редакция на хотел' : 'Добавяне на хотел' ?></h1>
        <p>Попълнете данните за хотела и наличните удобства.</p>
    </div>
    <a class="button secondary" href="index.php">Назад</a>
</section>

<?php if (!$locations): ?>
    <div class="errors">
        <p>Първо добавете поне едно населено място.</p>
    </div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="errors">
        <?php foreach ($errors as $error): ?>
            <p><?= h($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" class="form panel" data-validate>
    <label>
        Име на хотел
        <input type="text" name="name" required minlength="2" value="<?= h($hotel['name']) ?>">
    </label>

    <div class="image-field">
        <label>
            URL на снимка
            <input type="url" name="image_url" data-image-preview-input placeholder="https://..." value="<?= h($hotel['image_url'] ?? '') ?>">
        </label>
        <div class="image-preview">
            <img
                data-image-preview
                src="<?= h(($hotel['image_url'] ?? '') !== '' ? $hotel['image_url'] : 'assets/cities/default.svg') ?>"
                alt="Преглед на снимката"
                data-fallback-image="assets/cities/default.svg"
            >
        </div>
    </div>

    <div class="grid three">
        <label>
            Брой апартаменти
            <input type="number" name="apartments" min="0" required value="<?= (int) $hotel['apartments'] ?>">
        </label>
        <label>
            Брой студия
            <input type="number" name="studios" min="0" required value="<?= (int) $hotel['studios'] ?>">
        </label>
        <label>
            Брой офиси
            <input type="number" name="offices" min="0" required value="<?= (int) $hotel['offices'] ?>">
        </label>
    </div>

    <label>
        Населено място
        <select name="location_id" required>
            <option value="">Изберете...</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?= (int) $location['id'] ?>" <?= (int) $hotel['location_id'] === (int) $location['id'] ? 'selected' : '' ?>>
                    <?= h($location['name'] . ', ' . $location['country']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <fieldset class="checks">
        <legend>Удобства</legend>
        <label><input type="checkbox" name="has_restaurant" <?= $hotel['has_restaurant'] ? 'checked' : '' ?>> Ресторант</label>
        <label><input type="checkbox" name="has_spa" <?= $hotel['has_spa'] ? 'checked' : '' ?>> Спа център</label>
        <label><input type="checkbox" name="has_pool" <?= $hotel['has_pool'] ? 'checked' : '' ?>> Басейн</label>
        <label><input type="checkbox" name="has_discotheque" <?= $hotel['has_discotheque'] ? 'checked' : '' ?>> Дискотека</label>
    </fieldset>

    <button type="submit" <?= !$locations ? 'disabled' : '' ?>>Запази</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
