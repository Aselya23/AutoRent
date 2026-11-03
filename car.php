<?php
require __DIR__ . '/includes/data.php';

// $_GET арқылы көлік нөмірін аламыз: car.php?id=2
$car = find_car($cars, (int) get_str('id'));

if (!$car) {
    http_response_code(404);
    $title = 'Табылмады';
    require __DIR__ . '/includes/header.php';
    echo '<p>Көлік табылмады. <a href="index.php">Каталогқа оралу</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$from = post_str('date_from');
$to   = post_str('date_to');
$errors = [];
$days = 0;
$total = null;

// Брондау формасы $_POST арқылы келеді
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d1 = DateTime::createFromFormat('!Y-m-d', $from);
    $d2 = DateTime::createFromFormat('!Y-m-d', $to);
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
        $days = $d1->diff($d2)->days;
        $total = $days * $car['price'];
    }
}

$title = $car['name'];
require __DIR__ . '/includes/header.php';
?>

    <p class="breadcrumb"><a href="index.php">Көліктер</a> / <?= e($car['name']) ?></p>

    <section class="detail">

      <div class="panel">
        <div class="car-image">🚘</div>
        <span class="badge"><?= e($car['category']) ?></span>
        <h1><?= e($car['name']) ?></h1>
        <p class="details"><?= $car['year'] ?> ж. шыққан. Таза, күтімі жақсы көлік қалалық және қалааралық сапарларға.</p>

        <div class="specs">
          <div><small>Жылы</small><b><?= $car['year'] ?></b></div>
          <div><small>Беріліс қорабы</small><b><?= e($car['transmission']) ?></b></div>
          <div><small>Отын</small><b><?= e($car['fuel']) ?></b></div>
          <div><small>Санат</small><b><?= e($car['category']) ?></b></div>
        </div>

        <p>Кондиционер, Bluetooth, артқы көріністі камерасы. Көлікті алған кезде бак толы болады, қайтарғанда да толы қайтарылуы керек.</p>
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
          <div><span>Тәулік бағасы</span><span><?= money($car['price']) ?></span></div>
          <div><span>Күн саны</span><span><?= $total !== null ? $days : '—' ?></span></div>
          <div class="total"><span>Жалпы сома</span><strong><?= $total !== null ? money($total) : '—' ?></strong></div>
        </div>

        <button class="form-button" type="submit" style="width:100%">Брондау</button>

        <?php if ($total !== null): ?>
          <p class="success">Брондау есептелді. Дерекқорға сақтау 12-аптада қосылады.</p>
        <?php endif; ?>
      </form>

    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>