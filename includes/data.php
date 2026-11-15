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