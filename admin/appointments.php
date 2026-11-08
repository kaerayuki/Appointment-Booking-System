<?php
require_once '../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'delete') {
        run($conn, "DELETE FROM appointments WHERE appointment_id = ?", 'i', array($id));
        setFlash('success', 'Appointment deleted.');
    } elseif ($action == 'status') {
        $newStatus = $_POST['status'];
        if (in_array($newStatus, array('Pending', 'Confirmed', 'Completed', 'Cancelled'))) {
            run($conn, "UPDATE appointments SET status = ? WHERE appointment_id = ?", 'si', array($newStatus, $id));
            setFlash('success', 'Status changed to ' . $newStatus . '.');
        }
    }

    $back = 'appointments.php';
    if (!empty($_POST['return'])) {
        $back .= '?' . $_POST['return'];
    }
    header('Location: ' . $back);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT a.*, s.service_name FROM appointments a
        JOIN services s ON a.service_id = s.service_id WHERE 1=1";
$types = '';
$params = array();

if ($search != '') {
    $sql .= " AND (a.client_name LIKE ? OR a.email LIKE ? OR a.reference_code LIKE ? OR s.service_name LIKE ?)";
    $like = '%' . $search . '%';
    $types .= 'ssss';
    array_push($params, $like, $like, $like, $like);
}
if ($status != '') {
    $sql .= " AND a.status = ?";
    $types .= 's';
    $params[] = $status;
}
if ($date != '') {
    $sql .= " AND a.appointment_date = ?";
    $types .= 's';
    $params[] = $date;
}
$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$result = run($conn, $sql, $types, $params)->get_result();
$returnQuery = $_SERVER['QUERY_STRING'];

$base = '../';
$isAdminPage = true;
$pageTitle = 'Appointments';
include '../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h4 class="mb-0">Appointments</h4>
  <a href="appointment_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Appointment</a>
</div>

<div class="card p-3">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search name, email, reference or service" value="<?= clean($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">All statuses</option>
        <?php foreach (array('Pending', 'Confirmed', 'Completed', 'Cancelled') as $st): ?>
          <option <?= $status == $st ? 'selected' : '' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="date" class="form-control" value="<?= clean($date) ?>">
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button class="btn btn-outline-primary flex-fill">Filter</button>
      <a href="appointments.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>Ref</th><th>Client</th><th>Service</th><th>Date</th><th>Time</th><th>Status</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows == 0): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No appointments found.</td></tr>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="small fw-semibold"><?= clean($row['reference_code']) ?></td>
            <td>
              <div class="fw-semibold"><?= clean($row['client_name']) ?></div>
              <div class="small text-muted"><?= clean($row['email']) ?> | <?= clean($row['phone']) ?></div>
            </td>
            <td><?= clean($row['service_name']) ?></td>
            <td><?= formatDate($row['appointment_date']) ?></td>
            <td><?= formatTime($row['appointment_time']) ?></td>
            <td><?= statusBadge($row['status']) ?></td>
            <td class="text-end text-nowrap">
              <form method="post" class="d-inline">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="id" value="<?= $row['appointment_id'] ?>">
                <input type="hidden" name="return" value="<?= clean($returnQuery) ?>">
                <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                  <?php foreach (array('Pending', 'Confirmed', 'Completed', 'Cancelled') as $st): ?>
                    <option <?= $row['status'] == $st ? 'selected' : '' ?>><?= $st ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
              <a href="appointment_form.php?id=<?= $row['appointment_id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete this appointment permanently?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $row['appointment_id'] ?>">
                <input type="hidden" name="return" value="<?= clean($returnQuery) ?>">
                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
