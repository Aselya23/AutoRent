<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/db.php';

// Кірген адам кіру бетін көрмейді
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(post_str('email'));
    $pass  = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Электрондық пошта дұрыс емес';
    }
    if ($pass === '') {
        $errors['password'] = 'Құпиясөзді енгізіңіз';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, full_name, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // password_verify() кіргізілген құпиясөзді шифрланған мәтінмен салыстырады
        if ($user && password_verify($pass, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            header('Location: index.php');
            exit;
        }
        // Қайсысы қате екенін айтпаймыз (қауіпсіздік үшін)
        $errors['password'] = 'Email немесе құпиясөз қате';
    }
}

$title = 'Кіру';
require __DIR__ . '/includes/header.php';
?>

    <form class="form-box" method="post">
      <h1>Кіру</h1>
      <p class="details">Аккаунтыңызға кіріп, брондауларыңызды басқарыңыз.</p>

      <label for="email">Электрондық пошта</label>
      <input id="email" name="email" type="email" value="<?= e($email) ?>" placeholder="name@example.com">
      <span class="error"><?= e($errors['email'] ?? '') ?></span>

      <label for="password">Құпиясөз</label>
      <input id="password" name="password" type="password" placeholder="Құпиясөзіңіз">
      <span class="error"><?= e($errors['password'] ?? '') ?></span>

      <button class="form-button" type="submit">Кіру</button>

      <p class="alt">Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p>
    </form>

<?php require __DIR__ . '/includes/footer.php'; ?>