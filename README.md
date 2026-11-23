# Student Registration System

A web-based Student Registration System built using PHP, MySQL, Nginx and Docker.

The application allows students to register online while administrators can securely manage student records.

## Features

### Public Features
- View the public dashboard.
- Register as a student.
- Receive an automatically generated Student ID.
- Select a department, course and academic year.

### Administrator Features
- Secure administrator login.
- View registered students.
- Search students by name or email.
- Edit student information.
- Delete student records.
- Manage student registration information.

## Technologies Used

- PHP 8.3
- MySQL 8.4
- HTML5
- CSS3
- JavaScript
- Nginx
- Docker and Docker Compose
- phpMyAdmin

## Project Structure

```text
student-registration-system/
│
├── mysql/
│   └── init/
│       └── 01-database.sql
│
├── nginx/
│   └── default.conf
│
├── php/
│   └── Dockerfile
│
├── src/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   │
│   ├── includes/
│   ├── admin-login.php
│   ├── delete.php
│   ├── edit.php
│   ├── index.php
│   ├── logout.php
│   ├── register.php
│   └── students.php
│
├── .env.example
├── .gitignore
├── docker-compose.yml
└── README.md
```

## Requirements

Install the following before running the project:

- Docker Desktop or Docker Engine.
- Docker Compose.
- Git.

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/martin-baraka/student-registration-system.git
```

### 2. Enter the project directory

```bash
cd student-registration-system
```

### 3. Create your environment file

```bash
cp .env.example .env
```

Open `.env` and replace the example passwords with your own strong passwords.

Never share your actual `.env` file.

### 4. Build and start the application

```bash
docker compose up -d --build
```

Docker will build the PHP application, start Nginx and MySQL, and initialize the database.

### 5. Access the application

Open your browser:

**Application:** http://localhost

**phpMyAdmin:** http://localhost:8081

## Administrator Account

The database initialization script creates the administrators table but does not create an administrator account.

Each person running the project should create their own administrator account.

To generate a secure password hash, run:

```bash
docker compose exec app php -r '$p = readline("Enter a new admin password: "); echo password_hash($p, PASSWORD_DEFAULT), PHP_EOL;'
```

Copy the generated hash.

Open phpMyAdmin, select the `student_registration` database, and execute the following SQL, replacing the example values with your own:

```sql
INSERT INTO admins (username, email, password)
VALUES (
    'admin',
    'admin@example.com',
    'PASTE_YOUR_GENERATED_PASSWORD_HASH_HERE'
);
```

You can then log in using the username or email and the password you selected.

## Useful Docker Commands

Start the application:

```bash
docker compose up -d
```

Stop the application:

```bash
docker compose down
```

View running containers:

```bash
docker compose ps
```

View application logs:

```bash
docker compose logs
```

Rebuild the application:

```bash
docker compose up -d --build
```

## Database Persistence

The project uses a Docker volume named `mysql_data` to preserve database records when containers restart.

The initial SQL script runs automatically when MySQL initializes a fresh database volume.

**Warning:** Running `docker compose down -v` deletes the database volume and its stored records. Do not use it unless you intentionally want to remove the database data.

## Security

- Database credentials are stored in `.env`.
- The `.env` file is excluded from Git.
- Administrator passwords are stored as password hashes.
- Student management pages require administrator authentication.
- Public visitors can register but cannot manage student records.

## Author

Martin Baraka

## License

This project is available for educational and learning purposes.
