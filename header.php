<!DOCTYPE html>
<html lang="kk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> — AutoRent</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header class="navbar">
    <h2>Auto<span>Rent</span></h2>

    <nav>
      <a href="index.php">Көліктер</a>
      <?php if (is_logged_in()): ?>
        <?php if (is_admin()): ?>
         <a href="manage_cars.php">Басқару</a>
        <?php endif; ?>
        <a href="my_bookings.php">Брондауларым</a>
        <a href="profile.php"><?= e($_SESSION['user_name']) ?></a>
        <button onclick="location.href='logout.php'">Шығу</button>
      <?php else: ?>
        <button onclick="location.href='login.php'">Кіру</button>
        <button onclick="location.href='register.php'">Тіркелу</button>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">