<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';

const PER_PAGE = 6;

// Сұрыптау нұсқалары (ақ тізім): SQL-ге тек осы мәндер түседі
const SORTS = [
    'price_asc'  => ['Бағасы: арзаннан қымбатқа', 'c.price_per_day ASC'],
    'price_desc' => ['Бағасы: қымбаттан арзанға', 'c.price_per_day DESC'],
    'year_desc'  => ['Жаңасынан ескіге (жылы)', 'c.year DESC'],
    'name_asc'   => ['Аты бойынша (A–Z)', 'c.brand ASC, c.model ASC'],
];

// Параметрлер $_GET арқылы келеді
$q       = get_str('q');
$cat     = (int) get_str('category');
$sort    = get_str('sort');
$maxRaw  = get_str('maxPrice');
$fromRaw = get_str('date_from');
$toRaw   = get_str('date_to');

if (!isset(SORTS[$sort])) {
    $sort = 'price_asc';
}
$max = filter_var($maxRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

// Күн бойынша сүзгі: екі күн де дұрыс болса ғана қолданылады
$dateError = '';
$useDates  = false;
if ($fromRaw !== '' || $toRaw !== '') {
    $d1 = DateTime::createFromFormat('!Y-m-d', $fromRaw);
    $d2 = DateTime::createFromFormat('!Y-m-d', $toRaw);
    if (!$d1 || $d1->format('Y-m-d') !== $fromRaw || !$d2 || $d2->format('Y-m-d') !== $toRaw) {
        $dateError = 'Бос көліктерді іздеу үшін екі күнді де таңдаңыз';
    } elseif ($d2 <= $d1) {
        $dateError = 'Қайтару күні алу күнінен кейін болуы керек';
    } else {
        $useDates = true;
    }
}

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
if ($useDates) {
    // Тек таңдалған күндерде брондалмаған көліктер
    $where[]  = "NOT EXISTS (
        SELECT 1 FROM bookings b
        WHERE b.car_id = c.id AND b.status <> 'cancelled'
          AND b.start_date < ? AND b.end_date > ?
    )";
    $params[] = $toRaw;
    $params[] = $fromRaw;
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

// Барлық нәтиже саны (беттеу үшін)
$stmt = $pdo->prepare('SELECT COUNT(*) FROM cars c' . $whereSql);
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$pages  = max(1, (int) ceil($total / PER_PAGE));
$page   = min(max(1, (int) get_str('page')), $pages);
$offset = ($page - 1) * PER_PAGE;

$sql = 'SELECT c.*, cat.name AS category
        FROM cars c
        JOIN categories cat ON cat.id = c.category_id'
     . $whereSql
     . ' ORDER BY ' . SORTS[$sort][1] . ', c.id ASC'
     . ' LIMIT ' . PER_PAGE . ' OFFSET ' . $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

// Беттеу сілтемелері сүзгілерді сақтап қалады
$keep = array_filter([
    'q'         => $q,
    'category'  => $cat > 0 ? (string) $cat : '',
    'maxPrice'  => $maxRaw,
    'date_from' => $useDates ? $fromRaw : '',
    'date_to'   => $useDates ? $toRaw : '',
    'sort'      => $sort !== 'price_asc' ? $sort : '',
], fn($v) => $v !== '');
$pageUrl = fn(int $p) => 'index.php?' . http_build_query($keep + ['page' => $p]);

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

      <div>
        <label for="search">Іздеу</label>
        <input id="search" name="q" type="text"
               placeholder="Марка, модель" value="<?= e($q) ?>">
      </div>

      <div>
        <label for="category">Санат</label>
        <select id="category" name="category">
          <option value="0">Барлығы</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>>
              <?= e($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="maxPrice">Баға шегі (₸)</label>
        <input id="maxPrice" name="maxPrice" type="number" min="0"
               placeholder="Мысалы: 30000" value="<?= e($maxRaw) ?>">
      </div>

      <div>
        <label for="sort">Сұрыптау</label>
        <select id="sort" name="sort">
          <?php foreach (SORTS as $key => $s): ?>
            <option value="<?= e($key) ?>" <?= $sort === $key ? 'selected' : '' ?>><?= e($s[0]) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="date_from">Алу күні (бос көліктер)</label>
        <input id="date_from" name="date_from" type="date" value="<?= e($fromRaw) ?>">
      </div>

      <div>
        <label for="date_to">Қайтару күні</label>
        <input id="date_to" name="date_to" type="date" value="<?= e($toRaw) ?>">
      </div>

      <button class="search-btn" type="submit">Іздеу</button>

    </form>

    <?php if ($dateError !== ''): ?>
      <p class="alert-error"><?= e($dateError) ?></p>
    <?php endif; ?>

    <?php if ($keep): ?>
      <a class="reset-link" href="index.php">× Сүзгілерді тазалау</a>
    <?php endif; ?>

    <p id="count">
      Табылған көлік саны: <?= $total ?>
      <?= $pages > 1 ? ' · Бет ' . $page . ' / ' . $pages : '' ?>
    </p>

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

    <?php if (!$cars): ?>
      <p class="details">Ештеңе табылмады. Сүзгілерді өзгертіп көріңіз.</p>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Беттер">
        <?php if ($page > 1): ?>
          <a href="<?= e($pageUrl($page - 1)) ?>">‹</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a href="<?= e($pageUrl($i)) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a href="<?= e($pageUrl($page + 1)) ?>">›</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>