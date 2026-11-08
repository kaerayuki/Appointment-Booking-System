<?php
require_once 'includes/functions.php';

$serviceList = $conn->query("SELECT * FROM services ORDER BY service_name")->fetch_all(MYSQLI_ASSOC);

$a = array(
    'client_name' => '', 'email' => '', 'phone' => '',
    'service_id' => isset($_GET['service']) ? (int) $_GET['service'] : 0,
    'appointment_date' => '', 'appointment_time' => '', 'status' => 'Pending', 'notes' => ''
);
$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $a = getFormData();
    $a['status'] = 'Pending';
    $errors = validateAppointment($conn, $a);

    if (count($errors) == 0) {
        $code = makeReference($conn);
        $time = substr($a['appointment_time'], 0, 5) . ':00';
        $sql = "INSERT INTO appointments (reference_code, client_name, email, phone, service_id, appointment_date, appointment_time, status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
        run($conn, $sql, 'ssssisss', array($code, $a['client_name'], $a['email'], $a['phone'],
            $a['service_id'], $a['appointment_date'], $time, $a['notes']));
        header('Location: confirmation.php?ref=' . $code);
        exit;
    }
}

$pageTitle = 'Book Appointment';
$extraScript = 'booking.js';
include 'includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card p-4">
      <h4 class="mb-1"><i class="bi bi-calendar-plus text-primary"></i> Book an Appointment</h4>
      <p class="text-muted">Fill in your details and choose a free time slot.</p>
      <form method="post">
        <?php include 'includes/appointment_fields.php'; ?>
        <div class="mt-4 d-grid d-md-block">
          <button type="submit" class="btn btn-primary btn-lg px-5">Confirm Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
