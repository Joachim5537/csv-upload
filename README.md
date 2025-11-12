# Laravel CSV Upload Project

This mini Laravel project demonstrates CSV file uploads with background processing and real-time status updates.  
It’s built to showcase proficiency in Laravel, queues, and handling large CSV files efficiently.

---

## Tech Stack

- **Laravel 12.37** (PHP 8.2.29)  
- **SQLite** (default database for demo)  
- **Redis** (optional queue backend for Horizon)  
- **Nginx** (developed and tested on Ubuntu)  

---

## Setup Instructions

1. **Clone the repository**
   ```bash
    git clone https://github.com/<your-username>/<repo-name>.git
    cd <repo-name>

2. **Install dependencies**
   ```bash
   composer install

3. **Configure environment**
   ```bash
   cp .env.example .env

5. **Database setup**

- For Linux :
  ```bash
  touch database/database.sqlite

- For Windows :
  - create a new file named database.sqlite in the database folder 

- Update.env
  ```bash
  DB_CONNECTION=sqlite
  DB_DATABASE=/absolute/path/to/database/database.sqlite

5. **Queue configuration**
   ```bash
   QUEUE_CONNECTION=redis
   REDIS_CLIENT=predis

6. **Run database migrations**
    ```bash
   php artisan migrate

7. **Start background process**
   ```bash
   php artisan horizon

- or without horizon (horizon might not work in Window), you may use
  ```bash
  php artisan queue:work --queue=uploads


## Important Notes
---

-Ensure storage and bootstrap/cache directories are writable by your web server user (e.g., www-data).

-CSV uploads are processed in the background and statuses update in real-time.

-non-UTF-8 characters are cleaned automatically.

-This readme document will not cover web hosting part on your server.