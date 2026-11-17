<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';

// $_GET арқылы көлік нөмірін аламыз: car.php?id=2
$stmt = $pdo->prepare(
    'SELECT c.*, cat.name AS category
     FROM cars c
     JOIN categories cat ON cat.id = c.category_id
     WHERE c.id = ?'
);
$stmt->execute([(int) get_str('id')]);
$car = $stmt->fetch();

if (!$car) {
    http_response_code(404);
    $title = 'Табылмады';
    require __DIR__ . '/includes/header.php';
    echo '<p>Көлік табылмады. <a href="index.php">Каталогқа оралу</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$name   = $car['brand'] . ' ' . $car['model'];
$logged = is_logged_in();
$from   = post_str('date_from');
$to     = post_str('date_to');
$errors = [];
$days   = 0;
$total  = null;

// Брондау формасы $_POST арқылы келеді (тек кірген адам үшін)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $logged) {
    $d1    = DateTime::createFromFormat('!Y-m-d', $from);
    $d2    = DateTime::createFromFormat('!Y-m-d', $to);
    $today = new DateTime('today');

    if (!$d1 || $d1->format('Y-m-d') !== $from) {
        $errors['from'] = 'Күнді дұрыс таңдаңыз';
    } elseif ($d1 < $today) {
        $errors['from'] = 'Өткен күнді таңдауға болмайды';
    }

    if (!$d2 || $d2->format('Y-m-d') !== $to) {
        $errors['to'] = 'Күнді дұрыс таңдаңыз';
    } elseif (!isset($errors['from']) && $d2 <= $d1) {
        $errors['to'] = 'Қайтару күні алу күнінен кейін болуы керек';
    }

    if (!$errors) {
        $days  = $d1->diff($d2)->days;
        $total = $days * (float) $car['price_per_day'];

        // Күндер қиылыса ма? Қайтару күні келесі брондаудың алу күні бола алады
        $chk = $pdo->prepare(
            "SELECT COUNT(*) FROM bookings
             WHERE car_id = ? AND status <> 'cancelled' AND start_date < ? AND end_date > ?"
        );
        $chk->execute([(int) $car['id'], $to, $from]);

        if ((int) $chk->fetchColumn() > 0) {
            $errors['from'] = 'Бұл күндерге көлік брондалған. Басқа күндерді таңдаңыз';
            $total = null;
        } else {
            $ins = $pdo->prepare(
                'INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $ins->execute([$_SESSION['user_id'], (int) $car['id'], $from, $to, $total]);

            $_SESSION['flash'] = 'Брондау сақталды: ' . $name . ', ' . $days . ' тәулік, ' . money($total);
            header('Location: my_bookings.php');
            exit;
        }
    }
}

$title = $name;
require __DIR__ . '/includes/header.php';
?>

    <p class="breadcrumb"><a href="index.php">Көліктер</a> / <?= e($name) ?></p>

    <section class="detail">

      <div class="panel">
        <div class="car-image">🚘</div>
        <span class="badge"><?= e($car['category']) ?></span>
        <h1><?= e($name) ?></h1>
        <p class="details"><?= e((string) $car['description']) ?></p>

        <div class="specs">
          <div><small>Жылы</small><b><?= (int) $car['year'] ?></b></div>
          <div><small>Беріліс қорабы</small><b><?= e($car['transmission']) ?></b></div>
          <div><small>Отын</small><b><?= e($car['fuel']) ?></b></div>
          <div><small>Орын саны</small><b><?= (int) $car['seats'] ?></b></div>
        </div>
      </div>

      <form class="panel" method="post">
        <h3>Брондау</h3>

        <label for="date_from">Алу күні</label>
        <input id="date_from" name="date_from" type="date" value="<?= e($from) ?>">
        <span class="error"><?= e($errors['from'] ?? '') ?></span>

        <label for="date_to">Қайтару күні</label>
        <input id="date_to" name="date_to" type="date" value="<?= e($to) ?>">
        <span class="error"><?= e($errors['to'] ?? '') ?></span>

        <div class="summary">
          <div><span>Тәулік бағасы</span><span><?= money($car['price_per_day']) ?></span></div>
          <div><span>Күн саны</span><span><?= $total !== null ? $days : '—' ?></span></div>
          <div class="total"><span>Жалпы сома</span><strong><?= $total !== null ? money($total) : '—' ?></strong></div>
        </div>

        <?php if ($logged): ?>
          <button class="form-button" type="submit" style="width:100%">Брондау</button>
        <?php else: ?>
          <p class="details">Брондау үшін <a href="login.php">жүйеге кіріңіз</a>.</p>
        <?php endif; ?>
      </form>

    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>