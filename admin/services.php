<?php
require_once '../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete') {
    $id = (int) $_POST['id'];
    $used = run($conn, "SELECT COUNT(*) AS total FROM appointments WHERE service_id = ?", 'i', array($id))->get_result()->fetch_assoc()['total'];

    if ($used > 0) {
        setFlash('danger', 'This service cannot be deleted because it has ' . $used . ' appointment(s).');
    } else {
        run($conn, "DELETE FROM services WHERE service_id = ?", 'i', array($id));
        setFlash('success', 'Service deleted.');
    }
    header('Location: services.php');
    exit;
}

$services = $conn->query("SELECT s.*, (SELECT COUNT(*) FROM appointments a WHERE a.service_id = s.service_id) AS booked
                          FROM services s ORDER BY s.service_name");

$base = '../';
$isAdminPage = true;
$pageTitle = 'Services';
include '../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h4 class="mb-0">Services</h4>
  <a href="service_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Service</a>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Service</th><th>Description</th><th>Duration</th><th>Price</th><th>Bookings</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($services->num_rows == 0): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No services yet. Add one first.</td></tr>
        <?php endif; ?>
        <?php while ($s = $services->fetch_assoc()): ?>
          <tr>
            <td class="fw-semibold"><?= clean($s['service_name']) ?></td>
            <td class="text-muted small"><?= clean($s['description']) ?></td>
            <td><?= $s['duration_minutes'] ?> min</td>
            <td>&#8369;<?= number_format($s['price'], 2) ?></td>
            <td><?= $s['booked'] ?></td>
            <td class="text-end text-nowrap">
              <a href="service_form.php?id=<?= $s['service_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="post" class="d-inline" onsubmit="return confirm('Delete this service?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['service_id'] ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
