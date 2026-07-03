# Bidding Application

A Laravel-based bidding application running with Docker.

## Prerequisites

Make sure you have the following installed:

* Docker
* Docker Compose
* PHP (for local Artisan commands)
* Composer
* Node.js and npm (for frontend tests)

## Installation

### 1. Install PHP dependencies

```bash
composer install
```

### 2. Create environment file

```bash
cp .env.example .env
```

### 3. Generate application key

```bash
php artisan key:generate
```

### 4. Build and start Docker containers

```bash
docker compose up -d --build
```

### 5. Run database migrations and seed data

```bash
docker exec -it bidding_app php artisan migrate --seed
```

## Running the Application

Open the application in your browser:

```text
http://localhost
```

After loading the application, you will be redirected to the seeded product's bidding page.

## Running Tests

### Backend Tests

```bash
php artisan test
```

### Frontend Tests

Install frontend dependencies:

```bash
npm install
```

Run the frontend test suite:

```bash
npm test
```

## Docker Services

The application consists of:

* **app** – PHP/Laravel application container
* **nginx** – Web server container

## Useful Commands

Stop containers:

```bash
docker compose down
```

View logs:

```bash
docker compose logs -f
```

Access the application container:

```bash
docker exec -it bidding_app bash
```
