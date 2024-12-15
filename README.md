# Laravel Project

Welcome to the **Laravel Project**! This README file will guide you through setting up the project on your local machine.

---

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

- **PHP** (>= 8.1)
- **Composer** (Latest Version)
- **Node.js** and **npm**
- **MySQL** or any other database supported by Laravel
- **Git**

---

## Clone the Repository

To get started, clone the repository from GitHub:

```bash
git clone https://github.com/AmrAhmedHussien/project-management-tool.git
```
---

## Installation Steps

### 1. Install Dependencies

#### Install PHP Dependencies
Run the following command to install PHP dependencies using Composer:

```bash
composer install
```

#### Install Frontend Dependencies
Run the following command to install Node.js dependencies:

```bash
npm install
```

---

### 3. Configure Environment

Copy the example environment file and update it with your local configuration:

```bash
cp .env.example .env
```

Edit the `.env` file to configure the database and other environment variables.

---

### 4. Set Up the Database

#### Run Migrations
Run the following command to migrate the database:

```bash
php artisan migrate
```

#### Seed the Database
If your project includes seeders, run the following command to populate the database with test data:

```bash
php artisan db:seed
```

---

### 5. Optimize the Application

Clear and optimize the application cache:

```bash
php artisan optimize:clear
```

---

## Running Unit Tests

The project includes unit tests to ensure application stability. To run the tests, use:

```bash
php artisan test
```

---

## Development Workflow

### Running the Local Server
To start the development server, use the following command:

```bash
php artisan serve
```

---

## Additional Notes

- Ensure you have the correct database credentials in your `.env` file.
- Regularly pull the latest changes from the repository and run migrations/seeding if needed.

---

## Troubleshooting

If you encounter issues, consider the following:

1. Check that all dependencies are installed correctly.
2. Ensure your `.env` file is configured properly.
3. Run the following command to debug issues:

```bash
php artisan optimize:clear
```

If problems persist, refer to the Laravel documentation or open an issue in the project repository.

---

---

## Time to Complete

This task was completed in approximately **10 hours**.

---

## License

This project is open-source originally from https://github.com/devaslanphp .

---

Happy Coding!
