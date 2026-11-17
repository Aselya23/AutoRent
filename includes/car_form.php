<form class="form-box" method="post" style="max-width:560px">
  <h1><?= e($formTitle) ?></h1>

  <label for="category_id">Санат</label>
  <select id="category_id" name="category_id">
    <option value="0">Таңдаңыз</option>
    <?php foreach ($categories as $c): ?>
      <option value="<?= (int) $c['id'] ?>" <?= (int) $v['category_id'] === (int) $c['id'] ? 'selected' : '' ?>>
        <?= e($c['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <span class="error"><?= e($errors['category_id'] ?? '') ?></span>

  <label for="brand">Марка</label>
  <input id="brand" name="brand" value="<?= e($v['brand']) ?>" placeholder="Toyota">
  <span class="error"><?= e($errors['brand'] ?? '') ?></span>

  <label for="model">Модель</label>
  <input id="model" name="model" value="<?= e($v['model']) ?>" placeholder="Camry">
  <span class="error"><?= e($errors['model'] ?? '') ?></span>

  <label for="year">Шыққан жылы</label>
  <input id="year" name="year" type="number" value="<?= e($v['year']) ?>">
  <span class="error"><?= e($errors['year'] ?? '') ?></span>

  <label for="price_per_day">Бағасы (₸ / тәулік)</label>
  <input id="price_per_day" name="price_per_day" type="number" value="<?= e($v['price_per_day']) ?>" placeholder="20000">
  <span class="error"><?= e($errors['price_per_day'] ?? '') ?></span>

  <label for="transmission">Беріліс қорабы</label>
  <select id="transmission" name="transmission">
    <?php foreach (CAR_TRANSMISSIONS as $t): ?>
      <option <?= $v['transmission'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
    <?php endforeach; ?>
  </select>
  <span class="error"><?= e($errors['transmission'] ?? '') ?></span>

  <label for="fuel">Отын түрі</label>
  <select id="fuel" name="fuel">
    <?php foreach (CAR_FUELS as $f): ?>
      <option <?= $v['fuel'] === $f ? 'selected' : '' ?>><?= e($f) ?></option>
    <?php endforeach; ?>
  </select>
  <span class="error"><?= e($errors['fuel'] ?? '') ?></span>

  <label for="seats">Орын саны</label>
  <input id="seats" name="seats" type="number" value="<?= e($v['seats']) ?>">
  <span class="error"><?= e($errors['seats'] ?? '') ?></span>

  <label for="status">Күйі</label>
  <select id="status" name="status">
    <?php foreach (CAR_STATUSES as $key => $label): ?>
      <option value="<?= e($key) ?>" <?= $v['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
    <?php endforeach; ?>
  </select>
  <span class="error"><?= e($errors['status'] ?? '') ?></span>

  <label for="description">Сипаттама</label>
  <textarea id="description" name="description"><?= e($v['description']) ?></textarea>
  <span class="error"><?= e($errors['description'] ?? '') ?></span>

  <button class="form-button" type="submit"><?= e($submitLabel) ?></button>
  <p class="alt"><a href="manage_cars.php">← Көліктер тізіміне оралу</a></p>
</form>