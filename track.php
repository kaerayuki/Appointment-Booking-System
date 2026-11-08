<?php
require_once 'includes/functions.php';

$ref = '';
$email = '';
$appt = null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ref = strtoupper(trim($_POST['reference_code']));
    $email = trim($_POST['email']);
    $action = isset($_POST['action']) ? $_POST['action'] : 'find';

    $sql = "SELECT a.*, s.service_name, s.price FROM appointments a
            JOIN services s ON a.service_id = s.service_id
            WHERE a.reference_code = ? AND a.email = ?";
    $appt = run($conn, $sql, 'ss', array($ref, $email))->get_result()->fetch_assoc();

    if (!$appt) {
        $message = 'No appointment matches that reference code and email.';
    } elseif ($action == 'cancel') {
        if ($appt['status'] == 'Pending' || $appt['status'] == 'Confirmed') {
            run($conn, "UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ?", 'i', array($appt['appointment_id']));
            setFlash('success', 'Your appointment has been cancelled.');
            header('Location: track.php');
            exit;
        }
    }
}

$pageTitle = 'Track Appointment';
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card p-4 mb-4">
      <h4 class="mb-1"><i class="bi bi-search text-primary"></i> Track Your Appointment</h4>
      <p class="text-muted">Enter the reference code you received when you booked.</p>
      <?php if ($message != ''): ?>
        <div class="alert alert-danger"><?= clean($message) ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="action" value="find">
        <div class="mb-3">
          <label class="form-label" for="reference_code">Reference Code</label>
          <input type="text" id="reference_code" name="reference_code" class="form-control" placeholder="APT123ABC" value="<?= clean($ref) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="email">Email used when booking</label>
          <input type="email" id="email" name="email" class="form-control" value="<?= clean($email) ?>" required>
        </div>
        <button class="btn btn-primary">Find Appointment</button>
      </form>
    </div>

    <?php if ($appt): ?>
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0"><?= clean($appt['reference_code']) ?></h5>
          <?= statusBadge($appt['status']) ?>
        </div>
        <ul class="list-group list-group-flush mb-3">
          <li class="list-group-item d-flex justify-content-between"><span>Name</span><strong><?= clean($appt['client_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Service</span><strong><?= clean($appt['service_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Date</span><strong><?= formatDate($appt['appointment_date']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Time</span><strong><?= formatTime($appt['appointment_time']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Price</span><strong>&#8369;<?= number_format($appt['price'], 2) ?></strong></li>
        </ul>
        <?php if ($appt['status'] == 'Pending' || $appt['status'] == 'Confirmed'): ?>
          <form method="post" onsubmit="return confirm('Cancel this appointment?');">
            <input type="hidden" name="action" value="cancel">
            <input type="hidden" name="reference_code" value="<?= clean($appt['reference_code']) ?>">
            <input type="hidden" name="email" value="<?= clean($appt['email']) ?>">
            <button class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Cancel Appointment</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
