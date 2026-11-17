<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// XSS-тен қорғау: экранға шығарар алдында әрқашан e() қолданамыз
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function money($n): string {
    return number_format((float) $n, 0, '', ' ') . ' ₸';
}

// $_GET және $_POST мәнін қауіпсіз мәтін ретінде алу
function get_str(string $key): string {
    $v = $_GET[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

function post_str(string $key): string {
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

// Жабық бет: кірмеген адамды кіру бетіне жібереді
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function is_admin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Тек админге арналған бет
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('Бұл бетке кіруге рұқсат жоқ.');
    }
}

// Көлік күйлері: дерекқордағы мән => экранда көрінетін атау
const CAR_STATUSES = [
    'available' => 'Бос',
    'rented'    => 'Жалға берілген',
];

function status_label(string $s): string {
    return CAR_STATUSES[$s] ?? $s;
}

const CAR_TRANSMISSIONS = ['Автомат', 'Механика'];
const CAR_FUELS = ['Бензин', 'Дизель', 'Гибрид', 'Электро'];

// Көлік формасының деректерін оқып, сервер жағында тексереді
function car_input(array $categoryIds): array {
    $v = [
        'category_id'   => (int) post_str('category_id'),
        'brand'         => post_str('brand'),
        'model'         => post_str('model'),
        'year'          => post_str('year'),
        'price_per_day' => post_str('price_per_day'),
        'description'   => post_str('description'),
        'status'        => post_str('status'),
        'transmission'  => post_str('transmission'),
        'fuel'          => post_str('fuel'),
        'seats'         => post_str('seats'),
    ];
    $errors = [];
    $maxYear = (int) date('Y') + 1;

    if (!in_array($v['category_id'], $categoryIds, true)) {
        $errors['category_id'] = 'Санатты таңдаңыз';
    }
    $len = mb_strlen($v['brand']);
    if ($len < 2 || $len > 50) {
        $errors['brand'] = 'Марка 2 және 50 таңба аралығында болуы керек';
    }
    $len = mb_strlen($v['model']);
    if ($len < 1 || $len > 50) {
        $errors['model'] = 'Модель 1 және 50 таңба аралығында болуы керек';
    }
    if (filter_var($v['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1990, 'max_range' => $maxYear]]) === false) {
        $errors['year'] = "Жылы 1990 және $maxYear аралығында болуы керек";
    }
    if (filter_var($v['price_per_day'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1000, 'max_range' => 10000000]]) === false) {
        $errors['price_per_day'] = 'Баға 1000 мен 10000000 аралығындағы бүтін сан болуы керек';
    }
    if (mb_strlen($v['description']) > 1000) {
        $errors['description'] = 'Сипаттама 1000 таңбадан аспауы керек';
    }
    if (!isset(CAR_STATUSES[$v['status']])) {
        $errors['status'] = 'Күйді таңдаңыз';
    }
    if (!in_array($v['transmission'], CAR_TRANSMISSIONS, true)) {
        $errors['transmission'] = 'Беріліс қорабын таңдаңыз';
    }
    if (!in_array($v['fuel'], CAR_FUELS, true)) {
        $errors['fuel'] = 'Отын түрін таңдаңыз';
    }
    if (filter_var($v['seats'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2, 'max_range' => 20]]) === false) {
        $errors['seats'] = 'Орын саны 2 мен 20 аралығында болуы керек';
    }

    return [$v, $errors];
}

const BOOKING_STATUSES = [
    'pending'   => 'Күтуде',
    'cancelled' => 'Бас тартылды',
];

function booking_label(string $s): string {
    return BOOKING_STATUSES[$s] ?? $s;
}