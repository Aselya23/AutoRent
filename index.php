<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';

// Іздеу параметрлері $_GET арқылы келеді
$q      = get_str('q');
$cat    = (int) get_str('category');
$sort   = get_str('sort');
$maxRaw = get_str('maxPrice');
$max    = filter_var($maxRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

$where  = [];
$params = [];

if ($q !== '') {
    $where[]  = "CONCAT(c.brand, ' ', c.model) LIKE ?";
    $params[] = '%' . $q . '%';
}
if ($cat > 0) {
    $where[]  = 'c.category_id = ?';
    $params[] = $cat;
}
if ($max !== false) {
    $where[]  = 'c.price_per_day <= ?';
    $params[] = $max;
}

$order = ($sort === 'desc') ? 'DESC' : 'ASC';

$sql = 'SELECT c.*, cat.name AS category
        FROM cars c
        JOIN categories cat ON cat.id = c.category_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . " ORDER BY c.price_per_day $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$title = 'Көліктер';
require __DIR__ . '/includes/header.php';
?>

    <section class="hero">
      <h1>Көлікті бірнеше минутта жалға алыңыз</h1>
      <p>
        Қажетті көлікті таңдап, күндерді көрсетіп,
        онлайн брондаңыз. Бағасы бірден есептеледі.
      </p>
    </section>

    <form class="filters" method="get" action="index.php">

      <input id="search" name="q" type="text"
             placeholder="Іздеу (марка, модель)" value="<?= e($q) ?>">

      <select id="category" name="category">
        <option value="0">Барлығы</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>>
            <?= e($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input id="maxPrice" name="maxPrice" type="number"
             placeholder="Баға шегі" value="<?= e($maxRaw) ?>">

      <select id="sort" name="sort">
        <option value="asc">Бағасы: арзаннан қымбатқа</option>
        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Бағасы: қымбаттан арзанға</option>
      </select>

      <button type="submit">Іздеу</button>

    </form>

    <p id="count">Табылған көлік саны: <?= count($cars) ?></p>

    <section id="cars" class="cars">
      <?php foreach ($cars as $car): ?>
        <article class="car-card">
          <div class="car-image">🚘</div>
          <div class="car-info">
            <span class="badge"><?= e($car['category']) ?></span>
            <h3><?= e($car['brand'] . ' ' . $car['model']) ?></h3>
            <p class="details">
              <?= (int) $car['year'] ?> ж. • <?= e($car['transmission']) ?> • <?= e($car['fuel']) ?>
            </p>
            <div class="car-bottom">
              <strong>
                <?= money($car['price_per_day']) ?>
                <span class="details">/ тәулік</span>
              </strong>
              <form action="car.php" method="get">
                <input type="hidden" name="id" value="<?= (int) $car['id'] ?>">
                <button type="submit">Толығырақ</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>