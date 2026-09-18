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
<title>Delete parcel (test)</title>
<style>
  body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; max-width: 960px; margin: 40px auto; padding: 0 20px; color: #1a1a1a; }
  h1 { font-size: 22px; margin-bottom: 4px; }
  p.note { color: #6b6b68; font-size: 13px; margin-top: 0; margin-bottom: 24px; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
  th { color: #6b6b68; font-weight: 600; }
  .status-cancelled { color: #6b6b68; text-decoration: line-through; }
  .status-created_not_approved { color: #d8232a; }
  button.delete {
    padding: 6px 14px; background: #d8232a; color: #fff; border: none; cursor: pointer; font-size: 12px;
  }
  button.delete:disabled { background: #ccc; cursor: not-allowed; }
  form.login { max-width: 320px; margin-top: 60px; }
  form.login input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #d9d9d6; }
  form.login button { padding: 10px 20px; background: #1a1a1a; color: #fff; border: none; cursor: pointer; }
  .error { color: #d8232a; font-size: 13px; }
  .top { display: flex; justify-content: space-between; align-items: center; }
  .top a { font-size: 13px; color: #6b6b68; }
  .row-msg { font-size: 12px; margin-top: 4px; }
</style>
</head>
<body>

<?php if (!$loggedIn): ?>
  <form class="login" method="post">
    <h1>Delete parcel</h1>
    <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
    <input type="password" name="password" placeholder="Password" autofocus>
    <button type="submit">Log in</button>
  </form>
<?php else: ?>
  <div class="top">
    <h1>Delete parcel (test)</h1>
    <a href="?logout=1">Log out</a>
  </div>
  <p class="note">Deletes a parcel from its Swiss Post pickup order. This calls the live API — only use it on test orders.</p>

  <?php $orders = sp_read_pickup_orders(); ?>
  <?php if (!$orders): ?>
    <p>No pickup orders logged yet.</p>
  <?php else: ?>
    <table id="orders-table">
      <thead>
        <tr>
          <th>Logged</th>
          <th>Reference</th>
          <th>Name</th>
          <th>Pickup</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <?php
            $reference = $o['reference'] ?? '';
            $status = $o['status'] ?? '';
            $canDelete = $status !== 'cancelled' && !empty($o['order_key']) && !empty($o['parcel_key']);
          ?>
          <tr data-reference="<?= h($reference) ?>">
            <td><?= h($o['logged_at'] ?? '') ?></td>
            <td><?= h($reference) ?></td>
            <td><?= h(trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? ''))) ?></td>
            <td><?= h($o['pickup_date'] ?? '') ?> — <?= h($o['pickup_place'] ?? '') ?></td>
            <td class="status-cell status-<?= h($status) ?>"><?= h($status) ?></td>
            <td>
              <?php if ($canDelete): ?>
                <button class="delete" type="button">Delete parcel</button>
              <?php endif; ?>
              <div class="row-msg"></div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <script>
    document.querySelectorAll('button.delete').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var row = btn.closest('tr');
        var reference = row.dataset.reference;
        var msg = row.querySelector('.row-msg');

        if (!confirm('Delete this parcel? This calls the live Swiss Post API.')) {
          return;
        }

        btn.disabled = true;
        msg.textContent = 'Deleting…';

        fetch('api/delete-parcel.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ reference: reference })
        })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.success) {
              row.querySelector('.status-cell').textContent = 'cancelled';
              row.querySelector('.status-cell').className = 'status-cell status-cancelled';
              btn.remove();
              msg.textContent = '';
            } else {
              msg.textContent = 'Failed: ' + (data.message || 'unknown error');
              btn.disabled = false;
            }
          })
          .catch(function () {
            msg.textContent = 'Request failed.';
            btn.disabled = false;
          });
      });
    });
  </script>
<?php endif; ?>

</body>
</html>