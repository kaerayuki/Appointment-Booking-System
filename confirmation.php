<?php
require_once 'includes/functions.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$sql = "SELECT a.*, s.service_name, s.price FROM appointments a
        JOIN services s ON a.service_id = s.service_id WHERE a.reference_code = ?";
$appt = run($conn, $sql, 's', array($ref))->get_result()->fetch_assoc();

$pageTitle = 'Booking Confirmed';
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-6">
    <?php if (!$appt): ?>
      <div class="alert alert-warning">We could not find that booking. <a href="track.php">Try tracking it</a>.</div>
    <?php else: ?>
      <div class="card p-4 text-center">
        <div class="text-success fs-1"><i class="bi bi-check-circle-fill"></i></div>
        <h4>Booking received!</h4>
        <p class="text-muted">Your appointment is now <strong>pending</strong>. Save your reference code.</p>
        <div class="ref-code mb-3"><?= clean($appt['reference_code']) ?></div>
        <ul class="list-group list-group-flush text-start mb-3">
          <li class="list-group-item d-flex justify-content-between"><span>Name</span><strong><?= clean($appt['client_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Service</span><strong><?= clean($appt['service_name']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Date</span><strong><?= formatDate($appt['appointment_date']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Time</span><strong><?= formatTime($appt['appointment_time']) ?></strong></li>
          <li class="list-group-item d-flex justify-content-between"><span>Status</span><?= statusBadge($appt['status']) ?></li>
        </ul>
        <div class="d-flex gap-2 justify-content-center">
          <a href="track.php" class="btn btn-outline-primary">Track Appointment</a>
          <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
