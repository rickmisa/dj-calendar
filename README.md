# Daymark Event Calendar

A lightweight calendar event management system built for XAMPP with PHP, MySQL, HTML, CSS, and vanilla JavaScript.

## XAMPP setup

1. Copy this folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `database.sql`. It creates the `event_calendar` database and sample events.
5. Visit `http://localhost/event-calendar-management-system/`.

The default XAMPP MySQL connection is in `config.php`:

- Host: `127.0.0.1`
- Database: `event_calendar`
- User: `root`
- Password: empty

Update those constants if your XAMPP installation uses different credentials.

## Features

- Monthly calendar with previous, next, and today navigation
- Create, edit, and delete events
- Event categories and category color coding
- Search by event title
- Agenda view for the selected month
- Responsive layout for smaller screens
- PDO prepared statements and JSON API
