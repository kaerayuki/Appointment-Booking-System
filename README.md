# Appointment Booking System

A web-based appointment booking system where clients book a time slot online and an admin manages every booking. Built with PHP, MySQL, HTML, CSS and Bootstrap 5.

Final project for **Web Systems Technologies** (PHP and MySQL Development).

**Live site:** https://appointmentbookingsystem.rf.gd

## Features

### Clients
- Browse the list of services with price and duration
- Book an appointment with a date and a time slot
- Only free time slots are shown, so double booking is not possible
- Closed on Sundays, and past dates and times cannot be booked
- Get a reference code after booking
- Track an appointment using the reference code and email
- Cancel a pending or confirmed appointment

### Admin
- Secure login with hashed passwords
- Dashboard with counters and upcoming appointments
- Create, view, edit and delete appointments (CRUD)
- Change an appointment status: Pending, Confirmed, Completed or Cancelled
- Search and filter by name, email, reference code, status or date
- Add, edit and delete services

## Technologies Used
- PHP 8 (MySQLi with prepared statements)
- MySQL
- HTML5, CSS3
- Bootstrap 5 and Bootstrap Icons
- JavaScript (loads the available time slots)
- XAMPP for local development
- InfinityFree for hosting

## Project Structure
```
appointment-booking/
|-- admin/                  admin pages
|   |-- login.php
|   |-- logout.php
|   |-- dashboard.php
|   |-- appointments.php
|   |-- appointment_form.php
|   |-- services.php
|   `-- service_form.php
|-- assets/
|   |-- bootstrap/          Bootstrap CSS and JS
|   |-- css/style.css
|   `-- js/booking.js
|-- includes/
|   |-- header.php
|   |-- footer.php
|   |-- functions.php
|   `-- appointment_fields.php
|-- index.php               home page
|-- book.php                booking form
|-- confirmation.php        booking confirmation
|-- track.php               track or cancel a booking
|-- slots.php               returns the free time slots
|-- db_connect.php          database connection
`-- database.sql            database structure and sample data
```

## Database Design
The database has three tables:

| Table | Purpose | Main fields |
|-------|---------|-------------|
| `admins` | Admin accounts | admin_id, username, password, full_name |
| `services` | Services offered | service_id, service_name, description, duration_minutes, price |
| `appointments` | Bookings | appointment_id, reference_code, client_name, email, phone, service_id, appointment_date, appointment_time, status, notes, created_at |

`appointments.service_id` is a foreign key to `services.service_id`.

## How to Run Locally
1. Install [XAMPP](https://www.apachefriends.org) and start **Apache** and **MySQL**.
2. Copy this folder to `C:\xampp\htdocs\appointment-booking`.
3. Open `http://localhost/phpmyadmin` and create a database named `appointment_db`.
4. Select the database, click **Import**, and choose `database.sql`.
5. Check `db_connect.php`. The default XAMPP values are:
   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $dbname = 'appointment_db';
   ```
6. Open `http://localhost/appointment-booking/` in your browser.

## Admin Login
Open `/admin/login.php`

- Username: `admin`
- Password: `admin123`

## Deployment (InfinityFree)
1. Create a hosting account and a MySQL database in InfinityFree.
2. Put the InfinityFree host, username, password and database name in `db_connect.php`.
3. Upload all project files to the `htdocs` folder using FileZilla.
4. Import `database.sql` using the InfinityFree phpMyAdmin.
5. Open the live link and test the system.


