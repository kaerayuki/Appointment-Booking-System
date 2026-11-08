DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE services (
  service_id INT AUTO_INCREMENT PRIMARY KEY,
  service_name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  duration_minutes INT NOT NULL DEFAULT 30,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

CREATE TABLE appointments (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(12) NOT NULL UNIQUE,
  client_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  service_id INT NOT NULL,
  appointment_date DATE NOT NULL,
  appointment_time TIME NOT NULL,
  status ENUM('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointment_service FOREIGN KEY (service_id) REFERENCES services(service_id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$12$WCB4vEhsRFx7NRSMeOiaFe8SAxhnboCfwTkz.lZFkUWnIJz/lK6Qq', 'System Administrator');

INSERT INTO services (service_name, description, duration_minutes, price) VALUES
('General Consultation', 'Talk to our staff about your needs and get proper advice.', 30, 500.00),
('Dental Checkup', 'Routine teeth examination and cleaning.', 45, 800.00),
('Haircut & Styling', 'Haircut, wash and styling by our stylists.', 60, 350.00),
('Counseling Session', 'One-on-one private counseling session.', 60, 700.00),
('Follow-up Visit', 'Short visit for existing clients.', 20, 300.00);

INSERT INTO appointments (reference_code, client_name, email, phone, service_id, appointment_date, appointment_time, status, notes) VALUES
('APT100001', 'Maria Santos', 'maria@example.com', '09171234567', 1, '2026-11-24', '09:00:00', 'Confirmed', 'First visit'),
('APT100002', 'Juan Dela Cruz', 'juan@example.com', '09181234567', 2, '2026-11-24', '10:00:00', 'Pending', NULL),
('APT100003', 'Ana Reyes', 'ana@example.com', '09191234567', 3, '2026-11-25', '14:00:00', 'Pending', 'Prefers afternoon');
