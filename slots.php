<?php
require_once 'includes/functions.php';
header('Content-Type: application/json');

$date = isset($_GET['date']) ? $_GET['date'] : '';
$ignoreId = isset($_GET['ignore']) ? (int) $_GET['ignore'] : 0;
$admin = isAdmin() && isset($_GET['admin']);

$parts = explode('-', $date);
if (count($parts) != 3 || !checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
    echo json_encode(array('closed' => true, 'message' => 'Please choose a valid date.', 'slots' => array()));
    exit;
}

if (date('N', strtotime($date)) == 7) {
    echo json_encode(array('closed' => true, 'message' => 'We are closed on Sundays. Please pick another day.', 'slots' => array()));
    exit;
}

if (!$admin && $date < date('Y-m-d')) {
    echo json_encode(array('closed' => true, 'message' => 'That date has already passed.', 'slots' => array()));
    exit;
}

$taken = getTakenTimes($conn, $date, $ignoreId);
$slots = array();
foreach (getTimeSlots() as $time) {
    $passed = !$admin && strtotime($date . ' ' . $time) <= time();
    $slots[] = array(
        'time' => $time,
        'label' => formatTime($time),
        'available' => !in_array($time, $taken) && !$passed
    );
}

echo json_encode(array('closed' => false, 'message' => '', 'slots' => $slots));
