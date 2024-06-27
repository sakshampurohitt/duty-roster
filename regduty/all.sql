CREATE DATABASE IF NOT EXISTS dutyregister;
USE dutyregister;

CREATE TABLE department (
    departmentid INT AUTO_INCREMENT PRIMARY KEY,
    departmentname VARCHAR(255) NOT NULL
);

CREATE TABLE employee (
    empid INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    departmentid INT NOT NULL,
    contact_no VARCHAR(15),
    stafftype ENUM('class1', 'class2', 'class3', 'class4', 'class5'),
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (departmentid) REFERENCES department(departmentid)
);

CREATE TABLE admin (
    adminid INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE schedule (
    scheduleid INT AUTO_INCREMENT PRIMARY KEY,
    departmentid INT NOT NULL,
    empid INT NOT NULL,
    date DATE NOT NULL,
    shift_start TIME NOT NULL,
    shift_end TIME NOT NULL,
    present ENUM('yes', 'no') NOT NULL,
    FOREIGN KEY (departmentid) REFERENCES department(departmentid),
    FOREIGN KEY (empid) REFERENCES employee(empid)
);
