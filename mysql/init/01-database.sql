CREATE DATABASE IF NOT EXISTS student_registration;

USE student_registration;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL,
    department_code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(150) NOT NULL,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    department_id INT NOT NULL,
    duration_years INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

CREATE TABLE academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year VARCHAR(20) NOT NULL,
    semester VARCHAR(30) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Upcoming', 'Active', 'Completed')
        DEFAULT 'Upcoming',
    UNIQUE (academic_year, semester)
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    course_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive', 'Graduated')
        DEFAULT 'Active',
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
);

-- Departments

INSERT INTO departments
(department_name, department_code, description)
VALUES
('Computing and Informatics', 'CIT', 'Computing and IT programmes'),
('Business Studies', 'BUS', 'Business-related programmes'),
('Education', 'EDU', 'Education programmes'),
('Pure and Applied Sciences', 'PAS', 'Science programmes');

-- Courses

INSERT INTO courses
(course_name, course_code, department_id, duration_years)
VALUES
('BSc Computer Science', 'BCS',
 (SELECT id FROM departments WHERE department_code = 'CIT'), 4),

('BSc Information Technology', 'BIT',
 (SELECT id FROM departments WHERE department_code = 'CIT'), 4),

('Diploma in IT', 'DIT',
 (SELECT id FROM departments WHERE department_code = 'CIT'), 3),

('B Software Engineering', 'BSE',
 (SELECT id FROM departments WHERE department_code = 'CIT'), 4),

('Bachelor of Commerce', 'BCOM',
 (SELECT id FROM departments WHERE department_code = 'BUS'), 4),

('Bachelor of Business Administration', 'BBA',
 (SELECT id FROM departments WHERE department_code = 'BUS'), 4),

('Bachelor of Education Arts', 'BEDA',
 (SELECT id FROM departments WHERE department_code = 'EDU'), 4),

('Bachelor of Education Science', 'BEDS',
 (SELECT id FROM departments WHERE department_code = 'EDU'), 4),

('BSc Mathematics', 'BSM',
 (SELECT id FROM departments WHERE department_code = 'PAS'), 4),

('BSc Biology', 'BSB',
 (SELECT id FROM departments WHERE department_code = 'PAS'), 4);

-- Academic years

INSERT INTO academic_years
(academic_year, semester, start_date, end_date, status)
VALUES
('2026/2027', 'Semester 1', '2026-09-01', '2026-12-31', 'Active'),
('2026/2027', 'Semester 2', '2027-01-15', '2027-04-30', 'Upcoming'),
('2026/2027', 'Semester 3', '2027-05-15', '2027-08-31', 'Upcoming');
