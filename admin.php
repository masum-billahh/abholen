<?php
session_start();
require __DIR__ . '/api/_storage.php';
$config = require __DIR__ . '/config.php';
$adminPassword = $config['admin']['password'];

$error = '';

if (isset($_POST['password'])) {
    if (hash_equals($adminPassword, $_POST['password'])) {
        $_SESSION['sp_admin_ok'] = true;
    } else {
        $error = 'Wrong password.';
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['sp_admin_ok']);
}

$loggedIn = !empty($_SESSION['sp_admin_ok']);

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pickup orders</title>
<style>
  body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; max-width: 960px; margin: 40px auto; padding: 0 20px; color: #1a1a1a; }
  h1 { font-size: 22px; margin-bottom: 24px; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
  th { color: #6b6b68; font-weight: 600; }
  .status-created_not_approved { color: #d8232a; }
  .status-APPROVED, .status-created { color: #1b7a3d; }
  a.track { color: #1a1a1a; }
  form.login { max-width: 320px; margin-top: 60px; }
  form.login input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #d9d9d6; }
  form.login button { padding: 10px 20px; background: #1a1a1a; color: #fff; border: none; cursor: pointer; }
  .error { color: #d8232a; font-size: 13px; }
  .top { display: flex; justify-content: space-between; align-items: center; }
  .top a { font-size: 13px; color: #6b6b68; }
</style>
</head>
<body>

<?php if (!$loggedIn): ?>
  <form class="login" method="post">
    <h1>Pickup orders</h1>
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
    <input type="password" name="password" placeholder="Password" autofocus>
    <button type="submit">Log in</button>
  </form>
<?php else: ?>
  <div class="top">
    <h1>Pickup orders</h1>
    <a href="?logout=1">Log out</a>
  </div>
  <?php $orders = sp_read_pickup_orders(); ?>
  <?php if (!$orders): ?>
    <p>No pickup orders logged yet.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Logged</th>
          <th>Reference</th>
          <th>Name</th>
          <th>Address</th>
          <th>Pickup</th>
          <th>Size</th>
          <th>Status</th>
          <th>Tracking</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= h($o['logged_at'] ?? '') ?></td>
            <td><?= h($o['reference'] ?? '') ?></td>
            <td><?= h(trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? ''))) ?><br><?= h($o['email'] ?? '') ?></td>
            <td><?= h($o['address'] ?? '') ?></td>
            <td><?= h($o['pickup_date'] ?? '') ?> — <?= h($o['pickup_place'] ?? '') ?></td>
            <td><?= h($o['parcel_size'] ?? '') ?></td>
            <td class="status-<?= h($o['status'] ?? '') ?>"><?= h($o['status'] ?? '') ?></td>
            <td><?php if (!empty($o['tracking_url'])): ?><a class="track" href="<?= h($o['tracking_url']) ?>" target="_blank">Track</a><?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
