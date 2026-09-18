<?php

$supportedLangs = ['de', 'en', 'fr'];
$lang = $_GET['lang'] ?? 'de';
if (!in_array($lang, $supportedLangs, true)) {
    $lang = 'en';
}

$stringsPath = __DIR__ . "/lang/{$lang}.json";
$t = json_decode(file_get_contents($stringsPath), true);

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function lang_url(string $code): string
{
    $params = $_GET;
    $params['lang'] = $code;
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($t['page_title']) ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="top-bar">
  <div class="top-bar-side"></div>
  <div class="logo-placeholder" aria-hidden="true"><img style="width: 100%; height: 100%;" src="https://repan.ch/wp-content/uploads/2025/10/Logog.webp"/></div>
  <nav class="lang-switch top-bar-side" aria-label="Language">
    <?php foreach ($supportedLangs as $code): ?>
      <a href="<?= h(lang_url($code)) ?>" class="<?= $code === $lang ? 'active' : '' ?>">
        <?= h(strtoupper($code)) ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>

<div class="page">
  <div class="mark"></div>
  <h1><?= h($t['heading']) ?></h1>
  <p class="subheading"><?= h($t['subheading']) ?></p>
  <p class="weight-warning"><?= h($t['weight_warning']) ?></p>

  <ol class="steps" id="steps">
    <li class="step active" data-step="1"><?= h($t['step_address']) ?></li>
    <li class="step" data-step="2"><?= h($t['step_pickup']) ?></li>
    <li class="step" data-step="3"><?= h($t['step_contact']) ?></li>
  </ol>

  <form id="pickup-form" novalidate>

    <!-- Step 1: Address -->
    <section class="panel active" data-panel="1">
      <div class="row">
        <div class="field">
          <label for="first_name"><?= h($t['first_name']) ?></label>
          <input type="text" id="first_name" name="first_name" required autocomplete="given-name">
        </div>
        <div class="field">
          <label for="last_name"><?= h($t['last_name']) ?></label>
          <input type="text" id="last_name" name="last_name" required autocomplete="family-name">
        </div>
      </div>

      <div class="field">
        <label for="company">
          <?= h($t['company']) ?> <span class="tag">(<?= h($t['optional']) ?>)</span>
        </label>
        <input type="text" id="company" name="company" autocomplete="organization">
      </div>

      <div class="field" id="address-field" autocomplete="off">
        <label for="address"><?= h($t['address']) ?></label>
        <span class="status-icon" id="address-status-icon">
          <svg data-icon="valid" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <svg data-icon="invalid" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          <svg data-icon="loading" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="9" stroke-dasharray="42" stroke-dashoffset="14"></circle></svg>
        </span>
        <input type="text" id="address" name="address" required
               placeholder="<?= h($t['address_placeholder']) ?>" autocomplete="off">
        <div class="suggestions" id="suggestions" role="listbox"></div>
        <p class="field-hint"><?= h($t['address_hint']) ?></p>
        <p class="status-message" id="address-status-message"></p>

        <input type="hidden" id="hidden-street" name="street">
        <input type="hidden" id="hidden-house-number" name="house_number">
        <input type="hidden" id="hidden-zip" name="zip">
        <input type="hidden" id="hidden-city" name="city">
        <input type="hidden" id="hidden-country" name="country" value="CH">
        <input type="hidden" id="hidden-house-key" name="house_key">
      </div>
    </section>

    <!-- Step 2: Pickup date & place -->
    <section class="panel" data-panel="2">
      <p class="field-hint" id="pickup-options-loading"><?= h($t['loading_pickup_options']) ?></p>
      <p class="status-message invalid" id="pickup-options-error" style="display:none;"><?= h($t['pickup_options_error']) ?></p>

      <div class="field">
        <label for="pickup_date"><?= h($t['pickup_date']) ?></label>
        <select id="pickup_date" name="pickup_date" required>
          <option value=""><?= h($t['choose_date']) ?></option>
        </select>
      </div>

      <div class="field">
        <label for="pickup_place"><?= h($t['pickup_place']) ?></label>
        <select id="pickup_place" name="pickup_place" required>
          <option value=""><?= h($t['choose_place']) ?></option>
        </select>
      </div>
    </section>

    <!-- Step 3: Contact -->
    <section class="panel" data-panel="3">
      <div class="field">
        <label for="email"><?= h($t['email']) ?></label>
        <input type="email" id="email" name="email" required autocomplete="email">
      </div>
      <div class="field">
        <label for="phone"><?= h($t['phone']) ?></label>
        <input type="tel" id="phone" name="phone" required autocomplete="tel">
      </div>
    </section>

    <div class="actions">
      <button type="button" class="prev" id="prev-btn"><?= h($t['previous']) ?></button>
      <button type="button" class="next" id="next-btn"><?= h($t['next']) ?></button>
      <button type="submit" class="submit" id="submit-btn" style="display:none;"><?= h($t['submit']) ?></button>
      <span class="form-error" id="form-error"></span>
    </div>
  </form>

  <div class="result" id="result" style="display:none;"></div>
</div>

<script>
  window.APP_STRINGS = <?= json_encode($t) ?>;
  window.APP_LANG = <?= json_encode($lang) ?>;
</script>
<script src="assets/script.js"></script>
</body>
</html>
