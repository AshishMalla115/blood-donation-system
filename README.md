# Simple Blood Donation System

A simple web-based Blood Donation Management System built with PHP and MySQL.

## Features

- Add new blood donors
- View list of donors with blood groups
- Simple and easy to use interface

## Requirements

- PHP 7.0 or higher
- MySQL 5.6 or higher
- Web Server (Apache/Nginx)
- XAMPP/WAMP/MAMP (for local development)

## Installation

1. Place these files in your web server's directory
2. Import the database schema from `database.sql` file
3. Configure the database connection in `config/db.php`

## Database Configuration

The database configuration is located in `config/db.php`. Default settings are:

```php
Host: localhost
Database Name: blood_donation_db
Username: root
Password: '' (empty)
```

## Usage

1. Start your web server
2. Access index.php through your web browser
3. Use the form to add new donors
4. View the list of donors below the form

## Security

- PDO prepared statements are used to prevent SQL injection
- Input validation and sanitization implemented 