<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';
require_login();

$message = '';
$error   = '';

if (isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// Бас тарту: тек өз брондауынан және күні әлі басталмаған болса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post_str('action') === 'cancel') {
    $stmt = $pdo->prepare(
        "UPDATE bookings SET status = 'cancelled'
         WHERE id = ? AND user_id = ? AND status <> 'cancelled' AND start_date > CURDATE()"
    );
    $stmt->execute([(int) post_str('id'), $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $message = 'Брондаудан бас тартылды.';
    } else {
        $error = 'Бұл брондаудан бас тартуға болмайды (күні басталған немесе бұрын бас тартылған).';
    }
}

$stmt = $pdo->prepare(
    'SELECT b.id, b.start_date, b.end_date, b.total_price, b.status, c.brand, c.model
     FROM bookings b
     JOIN cars c ON c.id = b.car_id
     WHERE b.user_id = ?
     ORDER BY b.id DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

$title = 'Менің брондауларым';
require __DIR__ . '/includes/header.php';
?>

    <div class="page-head">
      <h1>Менің брондауларым</h1>
      <a class="btn-link" href="index.php">Көліктерге өту</a>
    </div>

    <?php if ($message !== ''): ?>
      <p class="success" style="margin-top:0;margin-bottom:16px"><?= e($message) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="alert-error"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if (!$bookings): ?>
      <p class="details">Сізде әлі брондау жоқ.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>№</th>
              <th>Көлік</th>
              <th>Алу күні</th>
              <th>Қайтару күні</th>
              <th>Сома</th>
              <th>Күйі</th>
              <th>Әрекет</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td><?= (int) $b['id'] ?></td>
                <td><?= e($b['brand'] . ' ' . $b['model']) ?></td>
                <td><?= e(date('d.m.Y', strtotime($b['start_date']))) ?></td>
                <td><?= e(date('d.m.Y', strtotime($b['end_date']))) ?></td>
                <td><?= money($b['total_price']) ?></td>
                <td><?= e(booking_label($b['status'])) ?></td>
                <td>
                  <?php if ($b['status'] !== 'cancelled' && $b['start_date'] > date('Y-m-d')): ?>
                    <form method="post" onsubmit="return confirm('Брондаудан бас тартасыз ба?')">
                      <input type="hidden" name="action" value="cancel">
                      <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                      <button class="btn-danger" type="submit">Бас тарту</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>