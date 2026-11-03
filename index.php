<?php
require __DIR__ . '/includes/data.php';

// Іздеу параметрлері $_GET арқылы келеді
$q      = get_str('q');
$cat    = get_str('category');
$sort   = get_str('sort');
$maxRaw = get_str('maxPrice');
$max    = filter_var($maxRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

$result = array_filter($cars, function ($c) use ($q, $cat, $max) {
    if ($q !== '' && mb_stripos($c['name'], $q) === false) return false;
    if ($cat !== '' && $cat !== 'all' && $c['category'] !== $cat) return false;
    if ($max !== false && $c['price'] > $max) return false;
    return true;
});

if ($sort === 'desc') {
    usort($result, fn($a, $b) => $b['price'] <=> $a['price']);
} else {
    usort($result, fn($a, $b) => $a['price'] <=> $b['price']);
}

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
        <option value="all">Барлығы</option>
        <option value="Седан" <?= $cat === 'Седан' ? 'selected' : '' ?>>Седан</option>
        <option value="Кроссовер" <?= $cat === 'Кроссовер' ? 'selected' : '' ?>>Кроссовер</option>
      </select>

      <input id="maxPrice" name="maxPrice" type="number"
             placeholder="Баға шегі" value="<?= e($maxRaw) ?>">

      <select id="sort" name="sort">
        <option value="asc">Бағасы: арзаннан қымбатқа</option>
        <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Бағасы: қымбаттан арзанға</option>
      </select>

      <button type="submit">Іздеу</button>

    </form>

    <p id="count">Табылған көлік саны: <?= count($result) ?></p>

    <section id="cars" class="cars">
      <?php foreach ($result as $car): ?>
        <article class="car-card">
          <div class="car-image">🚘</div>
          <div class="car-info">
            <span class="badge"><?= e($car['category']) ?></span>
            <h3><?= e($car['name']) ?></h3>
            <p class="details">
              <?= $car['year'] ?> ж. • <?= e($car['transmission']) ?> • <?= e($car['fuel']) ?>
            </p>
            <div class="car-bottom">
              <strong>
                <?= money($car['price']) ?>
                <span class="details">/ тәулік</span>
              </strong>
              <form action="car.php" method="get">
                <input type="hidden" name="id" value="<?= $car['id'] ?>">
                <button type="submit">Толығырақ</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <div class="pagination">
      <button class="active">1</button>
      <button>2</button>
      <button>3</button>
    </div>

<?php require __DIR__ . '/includes/footer.php'; ?>