<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';
require_admin();

$message = '';
$error   = '';

// Басқа беттен (қосу, өңдеу) келген хабарлама
if (isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Жою: $_POST арқылы, prepared statement-пен
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post_str('action') === 'delete') {
    $id = (int) post_str('id');
    try {
        $stmt = $pdo->prepare('DELETE FROM cars WHERE id = ?');
        $stmt->execute([$id]);
        $message = $stmt->rowCount() > 0 ? 'Көлік өшірілді.' : 'Көлік табылмады.';
    } catch (PDOException $e) {
        // 23000: көлікке брондау байланған (сыртқы кілт)
        if ($e->getCode() === '23000') {
            $error = 'Бұл көлікке брондау байланған, оны өшіруге болмайды.';
        } else {
            throw $e;
        }
    }
}

$cars = $pdo->query(
    'SELECT c.id, c.brand, c.model, c.year, c.price_per_day, c.status, cat.name AS category
     FROM cars c
     JOIN categories cat ON cat.id = c.category_id
     ORDER BY c.id DESC'
)->fetchAll();

$title = 'Көліктерді басқару';
require __DIR__ . '/includes/header.php';
?>

    <div class="page-head">
      <h1>Көліктерді басқару</h1>
      <a class="btn-link" href="add_car.php">+ Көлік қосу</a>
    </div>

    <?php if ($message !== ''): ?>
      <p class="success" style="margin-top:0;margin-bottom:16px"><?= e($message) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="alert-error"><?= e($error) ?></p>
    <?php endif; ?>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Көлік</th>
            <th>Санат</th>
            <th>Жылы</th>
            <th>Бағасы / тәулік</th>
            <th>Күйі</th>
            <th>Әрекет</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cars as $car): ?>
            <tr>
              <td><?= (int) $car['id'] ?></td>
              <td><?= e($car['brand'] . ' ' . $car['model']) ?></td>
              <td><?= e($car['category']) ?></td>
              <td><?= (int) $car['year'] ?></td>
              <td><?= money($car['price_per_day']) ?></td>
              <td><?= e(status_label($car['status'])) ?></td>
              <td>
                <div class="actions">
                  <a class="btn-link" href="edit_car.php?id=<?= (int) $car['id'] ?>">Өңдеу</a>
                  <form method="post" onsubmit="return confirm('Көлікті өшіресіз бе?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $car['id'] ?>">
                    <button class="btn-danger" type="submit">Жою</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

<?php require __DIR__ . '/includes/footer.php'; ?>