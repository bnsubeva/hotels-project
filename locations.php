<?php
require_once __DIR__ . '/config.php';
requireAdmin();

$stmt = db()->query(
    'SELECT l.*, COUNT(h.id) AS hotels_count
     FROM locations l
     LEFT JOIN hotels h ON h.location_id = l.id
     GROUP BY l.id
     ORDER BY l.country, l.name'
);
$locations = $stmt->fetchAll();

$pageTitle = 'Населени места';
require __DIR__ . '/header.php';
?>
<section class="page-head">
    <div>
        <h1>Населени места</h1>
        <p>Поддържа се списък с градове и държави, използван от хотелите.</p>
    </div>
    <a class="button" href="location_form.php">Добави място</a>
</section>

<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>Име</th>
            <th>Държава</th>
            <th>Брой хотели</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($locations as $location): ?>
            <tr>
                <td><?= h($location['name']) ?></td>
                <td><?= h($location['country']) ?></td>
                <td><?= (int) $location['hotels_count'] ?></td>
                <td class="actions">
                    <a href="location_form.php?id=<?= (int) $location['id'] ?>">Редакция</a>
                    <form method="post" action="location_delete.php" data-confirm="Да изтрия ли това населено място?">
                        <input type="hidden" name="id" value="<?= (int) $location['id'] ?>">
                        <button type="submit" class="link-danger" <?= (int) $location['hotels_count'] > 0 ? 'disabled title="Има хотели към това място"' : '' ?>>
                            Изтриване
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$locations): ?>
            <tr>
                <td colspan="4" class="empty">Все още няма въведени населени места.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/footer.php'; ?>
