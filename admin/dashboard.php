<?php
require_once '../includes/functions.php';
requireAdmin();

$counts = array('Pending' => 0, 'Confirmed' => 0, 'Completed' => 0, 'Cancelled' => 0);
$result = $conn->query("SELECT status, COUNT(*) AS total FROM appointments GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $counts[$row['status']] = $row['total'];
}

$today = date('Y-m-d');
$todayCount = run($conn, "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = ? AND status <> 'Cancelled'", 's', array($today))->get_result()->fetch_assoc()['total'];
$serviceCount = $conn->query("SELECT COUNT(*) AS total FROM services")->fetch_assoc()['total'];

$sql = "SELECT a.*, s.service_name FROM appointments a
        JOIN services s ON a.service_id = s.service_id
        WHERE a.appointment_date >= ? AND a.status IN ('Pending', 'Confirmed')
        ORDER BY a.appointment_date, a.appointment_time LIMIT 8";
$upcoming = run($conn, $sql, 's', array($today))->get_result();

$base = '../';
$isAdminPage = true;
$pageTitle = 'Dashboard';
include '../includes/header.php';
?>
<h4 class="mb-1">Welcome, <?= clean($_SESSION['admin_name']) ?></h4>
<p class="text-muted">Here is what is happening with your appointments.</p>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted small">Today</div><div class="stat-number text-primary"><?= $todayCount ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted small">Pending</div><div class="stat-number text-warning"><?= $counts['Pending'] ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted small">Confirmed</div><div class="stat-number text-primary"><?= $counts['Confirmed'] ?></div></div></div>
  <div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted small">Completed</div><div class="stat-number text-success"><?= $counts['Completed'] ?></div></div></div>
</div>

<div class="card p-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h5 class="mb-0">Upcoming Appointments</h5>
    <div>
      <a href="appointment_form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> New Appointment</a>
      <a href="appointments.php" class="btn btn-outline-primary btn-sm">View All</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Ref</th><th>Client</th><th>Service</th><th>Date</th><th>Time</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php if ($upcoming->num_rows == 0): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No upcoming appointments.</td></tr>
        <?php endif; ?>
        <?php while ($row = $upcoming->fetch_assoc()): ?>
          <tr>
            <td><a href="appointment_form.php?id=<?= $row['appointment_id'] ?>"><?= clean($row['reference_code']) ?></a></td>
            <td><?= clean($row['client_name']) ?></td>
            <td><?= clean($row['service_name']) ?></td>
            <td><?= formatDate($row['appointment_date']) ?></td>
            <td><?= formatTime($row['appointment_time']) ?></td>
            <td><?= statusBadge($row['status']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<p class="text-muted small mt-3 mb-0"><?= $serviceCount ?> services available &middot; <?= $counts['Cancelled'] ?> cancelled in total</p>
<?php include '../includes/footer.php'; ?>
