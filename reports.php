<?php
require_once __DIR__ . '/config.php';
requireLogin();

$countries = db()->query('SELECT DISTINCT country FROM locations ORDER BY country')->fetchAll();
$selectedCountry = trim($_GET['country'] ?? '');
$selectedAmenity = $_GET['amenity'] ?? '';

$countryHotels = [];
if ($selectedCountry !== '') {
    $stmt = db()->prepare(
        'SELECT h.*, l.name AS location_name, l.country
         FROM hotels h
         JOIN locations l ON l.id = h.location_id
         WHERE l.country = ?
         ORDER BY l.name, h.name'
    );
    $stmt->execute([$selectedCountry]);
    $countryHotels = $stmt->fetchAll();
}

$amenities = [
    'has_restaurant' => 'Ресторант',
    'has_spa' => 'Спа център',
    'has_pool' => 'Басейн',
    'has_discotheque' => 'Дискотека',
];

$amenityHotels = [];
if (array_key_exists($selectedAmenity, $amenities)) {
    $stmt = db()->prepare(
        "SELECT h.*, l.name AS location_name, l.country
         FROM hotels h
         JOIN locations l ON l.id = h.location_id
         WHERE h.$selectedAmenity = 1
         ORDER BY h.name"
    );
    $stmt->execute();
    $amenityHotels = $stmt->fetchAll();
}

function renderHotelRows(array $hotels): void
{
    foreach ($hotels as $hotel): ?>
        <tr>
            <td><?= h($hotel['name']) ?></td>
            <td><?= h($hotel['location_name'] . ', ' . $hotel['country']) ?></td>
            <td><?= (int) $hotel['apartments'] ?></td>
            <td><?= (int) $hotel['studios'] ?></td>
            <td><?= (int) $hotel['offices'] ?></td>
        </tr>
    <?php endforeach;
}

$pageTitle = 'Справки';
require __DIR__ . '/header.php';
?>
<section class="page-head">
    <div>
        <h1>Справки</h1>
        <p>Филтриране на въведените хотели по различни критерии.</p>
    </div>
</section>

<section class="report-grid">
    <article class="panel">
        <h2>Справка по държава</h2>
        <form method="get" class="form compact">
            <label>
                Държава
                <select name="country" required>
                    <option value="">Изберете...</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= h($country['country']) ?>" <?= $selectedCountry === $country['country'] ? 'selected' : '' ?>>
                            <?= h($country['country']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if ($selectedAmenity !== ''): ?>
                <input type="hidden" name="amenity" value="<?= h($selectedAmenity) ?>">
            <?php endif; ?>
            <button type="submit">Покажи</button>
        </form>

        <?php if ($selectedCountry !== ''): ?>
            <div class="table-wrap small">
                <table>
                    <thead>
                    <tr>
                        <th>Хотел</th>
                        <th>Място</th>
                        <th>Ап.</th>
                        <th>Студия</th>
                        <th>Офиси</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php renderHotelRows($countryHotels); ?>
                    <?php if (!$countryHotels): ?>
                        <tr><td colspan="5" class="empty">Няма хотели за избраната държава.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="panel">
        <h2>Справка по удобство</h2>
        <form method="get" class="form compact">
            <?php if ($selectedCountry !== ''): ?>
                <input type="hidden" name="country" value="<?= h($selectedCountry) ?>">
            <?php endif; ?>
            <label>
                Удобство
                <select name="amenity" required>
                    <option value="">Изберете...</option>
                    <?php foreach ($amenities as $field => $label): ?>
                        <option value="<?= h($field) ?>" <?= $selectedAmenity === $field ? 'selected' : '' ?>>
                            <?= h($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Покажи</button>
        </form>

        <?php if (array_key_exists($selectedAmenity, $amenities)): ?>
            <div class="table-wrap small">
                <table>
                    <thead>
                    <tr>
                        <th>Хотел</th>
                        <th>Място</th>
                        <th>Ап.</th>
                        <th>Студия</th>
                        <th>Офиси</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php renderHotelRows($amenityHotels); ?>
                    <?php if (!$amenityHotels): ?>
                        <tr><td colspan="5" class="empty">Няма хотели с избраното удобство.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/footer.php'; ?>
