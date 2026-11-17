<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';
require_admin();

$categories  = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$categoryIds = array_map('intval', array_column($categories, 'id'));

// Форма бос күйінде ашылғандағы әдепкі мәндер
$v = [
    'category_id'   => 0,
    'brand'         => '',
    'model'         => '',
    'year'          => date('Y'),
    'price_per_day' => '',
    'description'   => '',
    'status'        => 'available',
    'transmission'  => 'Автомат',
    'fuel'          => 'Бензин',
    'seats'         => '5',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$v, $errors] = car_input($categoryIds);

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO cars (category_id, brand, model, year, price_per_day, description, status, transmission, fuel, seats)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $v['category_id'], $v['brand'], $v['model'], (int) $v['year'], (int) $v['price_per_day'],
            $v['description'], $v['status'], $v['transmission'], $v['fuel'], (int) $v['seats'],
        ]);

        $_SESSION['flash'] = 'Көлік қосылды: ' . $v['brand'] . ' ' . $v['model'];
        header('Location: manage_cars.php');
        exit;
    }
}

$title       = 'Көлік қосу';
$formTitle   = 'Көлік қосу';
$submitLabel = 'Қосу';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/car_form.php';
require __DIR__ . '/includes/footer.php';