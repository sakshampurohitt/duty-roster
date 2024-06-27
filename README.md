# Duty Register Management System

## Introduction
The Duty Register Management System is a web application designed to manage department schedules, employee information, and their attendance records. It is built using PHP and MySQL. This project includes functionalities for both admin and employee roles, allowing them to manage and view schedules and attendance data.

## Project Structure
- `config/config.php`: Database configuration file.
- `login.php`: Login page for both admin and employee.
- `admindash.php`: Admin dashboard with options to manage employees, departments, and schedules.
- `empdash.php`: Employee dashboard with options to check schedule and monthly attendance.
- `check_schedule.php`: Page to check all schedules for present and future dates.
- `checkattend.php`: Page to check monthly attendance of employees.
- `monthlydeptstat.php`: Page to view monthly status for a department.
- `assets/`: Directory containing CSS and JS files.

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Setup Instructions
1. **Clone the Repository**
2. Database Setup Run the SQL script all.sql on your MySQL server to set up the database.
3. Replace username and password with your MySQL credentials.
4.Configuration
Update the database configuration in config/config.php with your MySQL server details.
Running the Project
5.Place the project files in the root directory of your web server.
Access the project in your web browser.
