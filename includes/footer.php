</main>
<footer class="text-center text-muted small py-4">
  BookIt Appointment Booking System &copy; <?= date('Y') ?> &middot; Mon - Sat, 9:00 AM - 5:00 PM
</footer>
<script src="<?= isset($base) ? $base : '' ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php if (isset($extraScript)): ?>
<script src="<?= isset($base) ? $base : '' ?>assets/js/<?= $extraScript ?>"></script>
<?php endif; ?>
</body>
</html>
