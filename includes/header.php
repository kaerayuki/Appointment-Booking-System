<?php
if (!isset($base)) { $base = ''; }
if (!isset($pageTitle)) { $pageTitle = 'Appointment Booking'; }
if (!isset($isAdminPage)) { $isAdminPage = false; }
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= clean($pageTitle) ?> | BookIt</title>
  <link href="<?= $base ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= $base ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark main-nav">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= $base ?><?= $isAdminPage ? 'admin/dashboard.php' : 'index.php' ?>">
      <i class="bi bi-calendar-check me-2"></i>BookIt<?= $isAdminPage ? ' Admin' : '' ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if ($isAdminPage): ?>
          <li class="nav-item"><a class="nav-link <?= $current == 'dashboard.php' ? 'active' : '' ?>" href="<?= $base ?>admin/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= in_array($current, array('appointments.php', 'appointment_form.php')) ? 'active' : '' ?>" href="<?= $base ?>admin/appointments.php"><i class="bi bi-calendar-week"></i> Appointments</a></li>
          <li class="nav-item"><a class="nav-link <?= in_array($current, array('services.php', 'service_form.php')) ? 'active' : '' ?>" href="<?= $base ?>admin/services.php"><i class="bi bi-list-check"></i> Services</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>index.php"><i class="bi bi-globe"></i> View Site</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-light ms-lg-2 mt-2 mt-lg-0" href="<?= $base ?>admin/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= $current == 'index.php' ? 'active' : '' ?>" href="<?= $base ?>index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= $current == 'book.php' ? 'active' : '' ?>" href="<?= $base ?>book.php">Book Appointment</a></li>
          <li class="nav-item"><a class="nav-link <?= $current == 'track.php' ? 'active' : '' ?>" href="<?= $base ?>track.php">Track Appointment</a></li>
          <li class="nav-item">
            <a class="btn btn-sm btn-light ms-lg-2 mt-2 mt-lg-0" href="<?= $base ?>admin/<?= isAdmin() ? 'dashboard.php' : 'login.php' ?>">
              <i class="bi bi-person-lock"></i> <?= isAdmin() ? 'Dashboard' : 'Admin' ?>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if (isset($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
    <?= clean($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
