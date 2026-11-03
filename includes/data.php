<?php
// Уақытша деректер. 11-аптада MySQL-ге ауысады.
$cars = [
  ['id'=>1,'name'=>'Toyota Land Cruiser Prado','category'=>'Кроссовер','year'=>2021,'transmission'=>'Автомат','fuel'=>'Дизель','price'=>38000],
  ['id'=>2,'name'=>'Toyota Camry','category'=>'Седан','year'=>2022,'transmission'=>'Автомат','fuel'=>'Бензин','price'=>18000],
  ['id'=>3,'name'=>'Toyota Corolla','category'=>'Седан','year'=>2021,'transmission'=>'Автомат','fuel'=>'Бензин','price'=>16000],
];

// XSS-тен қорғау: экранға шығарар алдында әрқашан e() қолданамыз
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function money(int $n): string {
    return number_format($n, 0, '', ' ') . ' ₸';
}

function find_car(array $cars, int $id): ?array {
    foreach ($cars as $c) {
        if ($c['id'] === $id) return $c;
    }
    return null;
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