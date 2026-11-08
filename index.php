<?php
require_once 'includes/functions.php';
$pageTitle = 'Home';
$services = $conn->query("SELECT * FROM services ORDER BY service_name");
include 'includes/header.php';
?>
<div class="hero text-center mb-5">
  <h1 class="fw-bold">Book your appointment in minutes</h1>
  <p class="lead mb-4">Pick a service, choose a free time, and get your reference code right away.</p>
  <a href="book.php" class="btn btn-light btn-lg me-2"><i class="bi bi-calendar-plus"></i> Book Now</a>
  <a href="track.php" class="btn btn-outline-light btn-lg"><i class="bi bi-search"></i> Track Appointment</a>
</div>

<h4 class="text-center mb-4">How it works</h4>
<div class="row g-4 mb-5 text-center">
  <div class="col-md-4">
    <div class="card p-4 h-100">
      <div class="step-icon"><i class="bi bi-list-check"></i></div>
      <h6>1. Choose a service</h6>
      <p class="text-muted small mb-0">Select what you need from our list of services.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-4 h-100">
      <div class="step-icon"><i class="bi bi-clock"></i></div>
      <h6>2. Pick a date and time</h6>
      <p class="text-muted small mb-0">Only free time slots are shown, so no double booking.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-4 h-100">
      <div class="step-icon"><i class="bi bi-check2-circle"></i></div>
      <h6>3. Get your reference code</h6>
      <p class="text-muted small mb-0">Use it anytime to check or cancel your appointment.</p>
    </div>
  </div>
</div>

<h4 class="text-center mb-4">Our Services</h4>
<div class="row g-4">
  <?php while ($s = $services->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card service-card p-4 h-100">
        <h5><?= clean($s['service_name']) ?></h5>
        <p class="text-muted"><?= clean($s['description']) ?></p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-bold">&#8369;<?= number_format($s['price'], 2) ?></div>
            <div class="small text-muted"><i class="bi bi-clock"></i> <?= $s['duration_minutes'] ?> minutes</div>
          </div>
          <a href="book.php?service=<?= $s['service_id'] ?>" class="btn btn-primary btn-sm">Book this</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
