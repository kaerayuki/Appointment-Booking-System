<?php
require_once '../includes/functions.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$s = array('service_name' => '', 'description' => '', 'duration_minutes' => 30, 'price' => '');

if ($id > 0) {
    $found = run($conn, "SELECT * FROM services WHERE service_id = ?", 'i', array($id))->get_result()->fetch_assoc();
    if (!$found) {
        setFlash('danger', 'Service not found.');
        header('Location: services.php');
        exit;
    }
    $s = $found;
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $s['service_name'] = trim($_POST['service_name']);
    $s['description'] = trim($_POST['description']);
    $s['duration_minutes'] = (int) $_POST['duration_minutes'];
    $s['price'] = $_POST['price'];

    if ($s['service_name'] == '') {
        $errors[] = 'Service name is required.';
    }
    if ($s['duration_minutes'] < 5) {
        $errors[] = 'Duration must be at least 5 minutes.';
    }
    if (!is_numeric($s['price']) || $s['price'] < 0) {
        $errors[] = 'Please enter a valid price.';
    }

    if (count($errors) == 0) {
        if ($id > 0) {
            run($conn, "UPDATE services SET service_name = ?, description = ?, duration_minutes = ?, price = ? WHERE service_id = ?",
                'ssidi', array($s['service_name'], $s['description'], $s['duration_minutes'], $s['price'], $id));
            setFlash('success', 'Service updated.');
        } else {
            run($conn, "INSERT INTO services (service_name, description, duration_minutes, price) VALUES (?, ?, ?, ?)",
                'ssid', array($s['service_name'], $s['description'], $s['duration_minutes'], $s['price']));
            setFlash('success', 'Service added.');
        }
        header('Location: services.php');
        exit;
    }
}

$base = '../';
$isAdminPage = true;
$pageTitle = $id > 0 ? 'Edit Service' : 'Add Service';
include '../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card p-4">
      <h4 class="mb-3"><?= $id > 0 ? 'Edit Service' : 'Add Service' ?></h4>
      <?php if (count($errors) > 0): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?><li><?= clean($error) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="service_name">Service Name</label>
          <input type="text" id="service_name" name="service_name" class="form-control" maxlength="100" value="<?= clean($s['service_name']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="description">Description</label>
          <input type="text" id="description" name="description" class="form-control" maxlength="255" value="<?= clean($s['description']) ?>">
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="duration_minutes">Duration (minutes)</label>
            <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" min="5" value="<?= clean($s['duration_minutes']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="price">Price (&#8369;)</label>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" value="<?= clean($s['price']) ?>" required>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-primary">Save Service</button>
          <a href="services.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
