<?php
$statuses = array('Pending', 'Confirmed', 'Completed', 'Cancelled');
$showStatus = isset($showStatus) ? $showStatus : false;
$minDate = isset($minDate) ? $minDate : date('Y-m-d');
$maxDate = isset($maxDate) ? $maxDate : date('Y-m-d', strtotime('+90 days'));
?>
<?php if (count($errors) > 0): ?>
  <div class="alert alert-danger">
    <strong>Please fix the following:</strong>
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= clean($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label" for="client_name">Full Name</label>
    <input type="text" id="client_name" name="client_name" class="form-control" maxlength="100" value="<?= clean($a['client_name']) ?>" placeholder="Juan Dela Cruz" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-control" maxlength="100" value="<?= clean($a['email']) ?>" placeholder="you@example.com" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" class="form-control" maxlength="20" value="<?= clean($a['phone']) ?>" placeholder="09123456789" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="service_id">Service</label>
    <select id="service_id" name="service_id" class="form-select" required>
      <option value="">Choose a service</option>
      <?php foreach ($serviceList as $s): ?>
        <option value="<?= $s['service_id'] ?>" <?= $a['service_id'] == $s['service_id'] ? 'selected' : '' ?>>
          <?= clean($s['service_name']) ?> - &#8369;<?= number_format($s['price'], 2) ?> (<?= $s['duration_minutes'] ?> min)
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-5">
    <label class="form-label" for="appointment_date">Date</label>
    <input type="date" id="appointment_date" name="appointment_date" class="form-control"
           value="<?= clean($a['appointment_date']) ?>"
           <?= $showStatus ? '' : 'min="' . $minDate . '" max="' . $maxDate . '"' ?> required>
    <div class="form-text">Closed on Sundays.</div>
  </div>

  <div class="col-md-7">
    <label class="form-label d-block">Time</label>
    <div id="slotBox" data-selected="<?= clean(substr($a['appointment_time'], 0, 5)) ?>" data-ignore="<?= isset($ignoreId) ? (int) $ignoreId : 0 ?>" data-base="<?= isset($base) ? $base : '' ?>">
      <div class="text-muted small">Pick a date first to see the available times.</div>
    </div>
  </div>

  <?php if ($showStatus): ?>
  <div class="col-md-4">
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-select">
      <?php foreach ($statuses as $st): ?>
        <option <?= $a['status'] == $st ? 'selected' : '' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php endif; ?>

  <div class="col-12">
    <label class="form-label" for="notes">Notes (optional)</label>
    <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Anything we should know?"><?= clean($a['notes']) ?></textarea>
  </div>
</div>
