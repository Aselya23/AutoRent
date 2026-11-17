<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';
require_admin();

// $_GET арқылы көлік нөмірін аламыз: edit_car.php?id=2
$id = (int) get_str('id');
$stmt = $pdo->prepare('SELECT * FROM cars WHERE id = ?');
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    http_response_code(404);
    $title = 'Табылмады';
    require __DIR__ . '/includes/header.php';
    echo '<p>Көлік табылмады. <a href="manage_cars.php">Тізімге оралу</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$categories  = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$categoryIds = array_map('intval', array_column($categories, 'id'));

// Форма дерекқордағы қазіргі мәндермен ашылады
$v = [
    'category_id'   => (int) $car['category_id'],
    'brand'         => $car['brand'],
    'model'         => $car['model'],
    'year'          => (string) $car['year'],
    'price_per_day' => (string) (int) $car['price_per_day'],
    'description'   => (string) $car['description'],
    'status'        => $car['status'],
    'transmission'  => $car['transmission'],
    'fuel'          => $car['fuel'],
    'seats'         => (string) $car['seats'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$v, $errors] = car_input($categoryIds);

    if (!$errors) {
        $stmt = $pdo->prepare(
            'UPDATE cars
             SET category_id = ?, brand = ?, model = ?, year = ?, price_per_day = ?,
                 description = ?, status = ?, transmission = ?, fuel = ?, seats = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $v['category_id'], $v['brand'], $v['model'], (int) $v['year'], (int) $v['price_per_day'],
            $v['description'], $v['status'], $v['transmission'], $v['fuel'], (int) $v['seats'],
            $id,
        ]);

        $_SESSION['flash'] = 'Көлік жаңартылды: ' . $v['brand'] . ' ' . $v['model'];
        header('Location: manage_cars.php');
        exit;
    }
}

$title       = 'Көлікті өңдеу';
$formTitle   = 'Көлікті өңдеу';
$submitLabel = 'Сақтау';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/car_form.php';
require __DIR__ . '/includes/footer.php';