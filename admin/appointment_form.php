<?php
require_once '../includes/functions.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$serviceList = $conn->query("SELECT * FROM services ORDER BY service_name")->fetch_all(MYSQLI_ASSOC);

$a = array(
    'client_name' => '', 'email' => '', 'phone' => '', 'service_id' => 0,
    'appointment_date' => '', 'appointment_time' => '', 'status' => 'Confirmed', 'notes' => ''
);

if ($id > 0) {
    $found = run($conn, "SELECT * FROM appointments WHERE appointment_id = ?", 'i', array($id))->get_result()->fetch_assoc();
    if (!$found) {
        setFlash('danger', 'Appointment not found.');
        header('Location: appointments.php');
        exit;
    }
    $a = $found;
}

$errors = array();
$refCode = isset($a['reference_code']) ? $a['reference_code'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $a = getFormData();
    $errors = validateAppointment($conn, $a, $id, true);

    if (count($errors) == 0) {
        $time = substr($a['appointment_time'], 0, 5) . ':00';

        if ($id > 0) {
            $sql = "UPDATE appointments SET client_name = ?, email = ?, phone = ?, service_id = ?,
                    appointment_date = ?, appointment_time = ?, status = ?, notes = ? WHERE appointment_id = ?";
            run($conn, $sql, 'sssissssi', array($a['client_name'], $a['email'], $a['phone'], $a['service_id'],
                $a['appointment_date'], $time, $a['status'], $a['notes'], $id));
            setFlash('success', 'Appointment updated.');
        } else {
            $code = makeReference($conn);
            $sql = "INSERT INTO appointments (reference_code, client_name, email, phone, service_id, appointment_date, appointment_time, status, notes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            run($conn, $sql, 'ssssissss', array($code, $a['client_name'], $a['email'], $a['phone'], $a['service_id'],
                $a['appointment_date'], $time, $a['status'], $a['notes']));
            setFlash('success', 'Appointment created. Reference code: ' . $code);
        }
        header('Location: appointments.php');
        exit;
    }
}

$base = '../';
$isAdminPage = true;
$showStatus = true;
$ignoreId = $id;
$extraScript = 'booking.js';
$pageTitle = $id > 0 ? 'Edit Appointment' : 'New Appointment';
include '../includes/header.php';
?>
<div class="card p-4">
  <h4 class="mb-3">
    <i class="bi bi-<?= $id > 0 ? 'pencil-square' : 'plus-circle' ?> text-primary"></i>
    <?= $id > 0 ? 'Edit Appointment' : 'New Appointment' ?>
    <?php if ($id > 0): ?><small class="text-muted fs-6"><?= clean($refCode) ?></small><?php endif; ?>
  </h4>
  <form method="post">
    <?php include '../includes/appointment_fields.php'; ?>
    <div class="mt-4">
      <button type="submit" class="btn btn-primary"><?= $id > 0 ? 'Update Appointment' : 'Save Appointment' ?></button>
      <a href="appointments.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
