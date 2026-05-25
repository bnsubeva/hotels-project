<?php
require_once __DIR__ . '/config.php';
requireLogin();

$amenities = [
    'has_restaurant' => 'Ресторант',
    'has_spa' => 'Спа център',
    'has_pool' => 'Басейн',
    'has_discotheque' => 'Дискотека',
];

$search = trim($_GET['search'] ?? '');
$locationId = (int) ($_GET['location_id'] ?? 0);
$amenity = $_GET['amenity'] ?? '';

$locations = db()->query('SELECT * FROM locations ORDER BY country, name')->fetchAll();
$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(h.name LIKE ? OR l.name LIKE ? OR l.country LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($locationId > 0) {
    $where[] = 'h.location_id = ?';
    $params[] = $locationId;
}

if (array_key_exists($amenity, $amenities)) {
    $where[] = "h.$amenity = 1";
}

$sql = 'SELECT h.*, l.name AS location_name, l.country
        FROM hotels h
        JOIN locations l ON l.id = h.location_id';

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY h.created_at DESC, h.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

function hotelImage(?string $url): string
{
    $url = trim((string) $url);

    if ($url !== '') {
        return $url;
    }

    return 'assets/cities/default.svg';
}

function hotelAmenities(array $hotel): array
{
    return array_filter([
        $hotel['has_restaurant'] ? 'Ресторант' : '',
        $hotel['has_spa'] ? 'Спа' : '',
        $hotel['has_pool'] ? 'Басейн' : '',
        $hotel['has_discotheque'] ? 'Дискотека' : '',
    ]);
}

$pageTitle = 'Хотели';
require __DIR__ . '/header.php';
?>
<section class="booking-hero">
    <div class="hero-copy">
        <span>Hotels App</span>
        <h1>Намерете подходящ хотел</h1>
        <p>Преглеждайте хотели по населено място, удобства и тип настаняване.</p>
    </div>

    <form method="get" class="search-panel">
        <label>
            Търсене
            <input type="search" name="search" placeholder="Хотел, град или държава" value="<?= h($search) ?>">
        </label>
        <label>
            Населено място
            <select name="location_id">
                <option value="0">Всички</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= (int) $location['id'] ?>" <?= $locationId === (int) $location['id'] ? 'selected' : '' ?>>
                        <?= h($location['name'] . ', ' . $location['country']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Удобство
            <select name="amenity">
                <option value="">Всички</option>
                <?php foreach ($amenities as $field => $label): ?>
                    <option value="<?= h($field) ?>" <?= $amenity === $field ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Търси</button>
    </form>
</section>

<section class="catalog-head">
    <div>
        <h2><?= count($hotels) ?> намерени хотела</h2>
        <p><?= isAdmin() ? 'Управление на хотелите в базата данни.' : 'Преглед на наличните хотели в системата.' ?></p>
    </div>
    <?php if (isAdmin()): ?>
        <a class="button" href="hotel_form.php">Добави хотел</a>
    <?php endif; ?>
</section>

<section class="hotel-list">
    <?php foreach ($hotels as $hotel): ?>
        <?php $availableAmenities = hotelAmenities($hotel); ?>
        <article class="hotel-card <?= isAdmin() ? '' : 'read-only' ?>">
            <div class="hotel-image">
                <img
                    src="<?= h(hotelImage($hotel['image_url'] ?? '')) ?>"
                    alt="<?= h($hotel['name']) ?>"
                    data-fallback-image="assets/cities/default.svg"
                >
            </div>
            <div class="hotel-main">
                <div class="hotel-title-row">
                    <div>
                        <h3><?= h($hotel['name']) ?></h3>
                        <p><?= h($hotel['location_name'] . ', ' . $hotel['country']) ?></p>
                    </div>
                    <span class="rating">8.<?= ((int) $hotel['id'] % 8) + 1 ?></span>
                </div>

                <div class="amenity-row">
                    <?php foreach ($availableAmenities as $item): ?>
                        <span><?= h($item) ?></span>
                    <?php endforeach; ?>
                    <?php if (!$availableAmenities): ?>
                        <span>Без допълнителни удобства</span>
                    <?php endif; ?>
                </div>

                <div class="hotel-stats">
                    <span><strong><?= (int) $hotel['apartments'] ?></strong> апартамента</span>
                    <span><strong><?= (int) $hotel['studios'] ?></strong> студия</span>
                    <span><strong><?= (int) $hotel['offices'] ?></strong> офиса</span>
                </div>
            </div>
            <?php if (isAdmin()): ?>
                <div class="hotel-side">
                    <a class="button secondary" href="hotel_form.php?id=<?= (int) $hotel['id'] ?>">Редакция</a>
                    <form method="post" action="hotel_delete.php" data-confirm="Да изтрия ли този хотел?">
                        <input type="hidden" name="id" value="<?= (int) $hotel['id'] ?>">
                        <button type="submit" class="danger-button">Изтриване</button>
                    </form>
                </div>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>

    <?php if (!$hotels): ?>
        <div class="empty-card">
            <h3>Няма намерени хотели</h3>
            <p>Променете филтрите или добавете нов хотел.</p>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/footer.php'; ?>
