# Appointment Booking System
PHP + MySQL + Bootstrap 5 CRUD app (Web Systems Technologies final project).

## Features
- Create / Read / Update / Delete appointments
- Services table linked by foreign key (JOIN)
- Search, status filter, dashboard counters
- Double-booking prevention, input validation, prepared statements (SQL-injection safe)
- Responsive Bootstrap 5 UI

## Run locally (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\appointment-booking`
2. Start Apache + MySQL in XAMPP
3. phpMyAdmin -> create DB `appointment_db` -> Import `database.sql`
4. Open http://localhost/appointment-booking/

## Deploy (InfinityFree)
Edit `db_connect.php` with InfinityFree credentials, upload to `htdocs/`, import `database.sql` in phpMyAdmin.
