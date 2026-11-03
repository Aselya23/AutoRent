<?php
require __DIR__ . '/includes/data.php';

$email = '';
$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = post_str('email');
    $pass  = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Электрондық пошта дұрыс емес';
    }
    if (strlen($pass) < 6) {
        $errors['password'] = 'Кемінде 6 таңба енгізіңіз';
    }

    $ok = !$errors;
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
      <input id="password" name="password" type="password" placeholder="Кемінде 6 таңба">
      <span class="error"><?= e($errors['password'] ?? '') ?></span>

      <button class="form-button" type="submit">Кіру</button>

      <?php if ($ok): ?>
        <p class="success">Деректер дұрыс. Нақты кіру 11-аптада (сессиялармен) қосылады.</p>
      <?php endif; ?>

      <p class="alt">Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p>
    </form>

<?php require __DIR__ . '/includes/footer.php'; ?>