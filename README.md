# Laravel File Management API

Technical test for the company Anchorless.
A Laravel API for file upload, management, and deletion.

---

## Requirements

- Docker
- Docker Compose

---

## Setup & Installation

1. Clone the repository:

```bash
git git@github.com:bode-locke/anchorless-api-test.git
cd anchorless-api-test
```

2. Copy the example environment file and customize it if needed:
```bash
cp .env.example .env
```

3. Start the Docker containers + enter inside the app container
```bash
docker compose up -d
docker compose exec app bash
```

4. Install dependencies inside the app container:
```bash
composer install
```

5. Generate the application key:
```bash
php artisan key:generate
```

6. Run the database migrations:
```bash
php artisan migrate
```

7. Seed the database
```bash
php artisan db:seed
```
