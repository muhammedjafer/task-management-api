# Laravel Task Management Application

This is a Laravel application for managing tasks. It includes features for task creation, updating, assigning, importing/exporting tasks to/from CSV, and sending real-time alerts via WebSockets when a task passes its due date.

## Prerequisites

Before you begin, ensure you have met the following requirements:
- PHP >= 8.0
- Composer
- MySQL

## Installation

Follow these steps to set up and run the application:

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-repo/laravel-task-app.git
   cd laravel-task-app
2. **Update composer**

   ```bash 
   composer update
3. **Generate app key**

   ```bash 
   php artisan key:generate
4. **Create the env file**
    ```bash
    cp .env.example .env

5. **Configure the env file for database and email in env**

6. **Run the migration**
    ```bash
    php artisan migrate
7. **Seed the database**
    ```bash
    php artisan db:seed
8. **Run the queue**
    ```bash
    php artisan queue:work
## Default users
1. Product owner email and password where APP_ENV is not production\
    email: productowner@system.com\
    password: productowner2024staging

1. Product owner email and password where APP_ENV is production\
    email: productowner@system.com\
    password: productowner2024production

2. Developer\
    email: developer@system.com\
    password: developer2024system
3. Tester\
    email: tester@system.com\
    password: tester2024system
## Commands
1. **To export tasks run the command below**
    ```bash
    php artisan export:tasks exported_tasks.csv
2. **To import tasks run the command below**
    ```bash
    php artisan import:tasks "exported_tasks.csv"
3. **Create user**
    ```bash
    php artisan user:create
