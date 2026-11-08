<?php
$host = 'sql101.infinityfree.com';
$user = 'if0_43380014';
$pass = '';
$dbname = 'if0_43380014_appointment_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
