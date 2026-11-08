<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../db_connect.php';

function clean($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function run($conn, $sql, $types = '', $params = array()) {
    $stmt = $conn->prepare($sql);
    if ($types != '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

function statusBadge($status) {
    $colors = array(
        'Pending' => 'warning text-dark',
        'Confirmed' => 'primary',
        'Completed' => 'success',
        'Cancelled' => 'danger'
    );
    $color = isset($colors[$status]) ? $colors[$status] : 'secondary';
    return '<span class="badge bg-' . $color . '">' . clean($status) . '</span>';
}

function getTimeSlots() {
    return array('09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00');
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function formatDate($date) {
    return date('D, M j, Y', strtotime($date));
}

function setFlash($type, $message) {
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

function slotIsFree($conn, $date, $time, $ignoreId = 0) {
    $time = substr($time, 0, 5) . ':00';
    $sql = "SELECT appointment_id FROM appointments
            WHERE appointment_date = ? AND appointment_time = ?
            AND status <> 'Cancelled' AND appointment_id <> ?";
    $stmt = run($conn, $sql, 'ssi', array($date, $time, $ignoreId));
    $stmt->store_result();
    $free = $stmt->num_rows == 0;
    $stmt->close();
    return $free;
}

function getTakenTimes($conn, $date, $ignoreId = 0) {
    $sql = "SELECT appointment_time FROM appointments
            WHERE appointment_date = ? AND status <> 'Cancelled' AND appointment_id <> ?";
    $result = run($conn, $sql, 'si', array($date, $ignoreId))->get_result();
    $taken = array();
    while ($row = $result->fetch_assoc()) {
        $taken[] = substr($row['appointment_time'], 0, 5);
    }
    return $taken;
}

function makeReference($conn) {
    do {
        $code = 'APT' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $stmt = run($conn, "SELECT appointment_id FROM appointments WHERE reference_code = ?", 's', array($code));
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $code;
}

function getFormData() {
    return array(
        'client_name' => isset($_POST['client_name']) ? trim($_POST['client_name']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
        'service_id' => isset($_POST['service_id']) ? (int) $_POST['service_id'] : 0,
        'appointment_date' => isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '',
        'appointment_time' => isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '',
        'status' => isset($_POST['status']) ? $_POST['status'] : 'Pending',
        'notes' => isset($_POST['notes']) ? trim($_POST['notes']) : ''
    );
}

function validateAppointment($conn, $d, $ignoreId = 0, $byAdmin = false) {
    $errors = array();

    if (!in_array($d['status'], array('Pending', 'Confirmed', 'Completed', 'Cancelled'))) {
        $errors[] = 'Invalid status.';
    }
    if (strlen($d['client_name']) < 2) {
        $errors[] = 'Please enter the full name.';
    }
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $d['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }

    $stmt = run($conn, "SELECT service_id FROM services WHERE service_id = ?", 'i', array($d['service_id']));
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $errors[] = 'Please choose a service.';
    }
    $stmt->close();

    $dateOk = false;
    $parts = explode('-', $d['appointment_date']);
    if (count($parts) == 3 && checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
        $dateOk = true;
    }

    if (!$dateOk) {
        $errors[] = 'Please choose a valid date.';
    } else {
        $today = date('Y-m-d');
        if (date('N', strtotime($d['appointment_date'])) == 7) {
            $errors[] = 'We are closed on Sundays. Please pick another day.';
        }
        if (!$byAdmin) {
            if ($d['appointment_date'] < $today) {
                $errors[] = 'You cannot book a date that has already passed.';
            }
            if ($d['appointment_date'] > date('Y-m-d', strtotime('+90 days'))) {
                $errors[] = 'Bookings are only open for the next 90 days.';
            }
        }
    }

    if (!in_array(substr($d['appointment_time'], 0, 5), getTimeSlots())) {
        $errors[] = 'Please choose a time slot.';
    }

    if (count($errors) == 0 && !$byAdmin) {
        $when = strtotime($d['appointment_date'] . ' ' . $d['appointment_time']);
        if ($when <= time()) {
            $errors[] = 'That time has already passed. Please choose a later slot.';
        }
    }

    if (count($errors) == 0 && $d['status'] != 'Cancelled') {
        if (!slotIsFree($conn, $d['appointment_date'], $d['appointment_time'], $ignoreId)) {
            $errors[] = 'Sorry, that time slot is already taken. Please choose another one.';
        }
    }

    return $errors;
}
