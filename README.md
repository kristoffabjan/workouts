# Workouts

## Prerequisites

- Docker & Docker Compose

## Development Setup

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Start containers
docker-compose up -d --build

# 3. Install dependencies
docker-compose exec php composer install
docker-compose exec node npm install
docker-compose exec node npm run build

# 4. Setup Laravel
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate --seed
docker-compose exec php php artisan storage:link
```

## Access

- **App:** http://localhost
- **phpMyAdmin:** http://localhost:8080
- **Mailpit:** http://localhost:8025

## Running Commands

```bash
# PHP/Artisan
docker-compose exec php php artisan <command>

# Composer
docker-compose exec php composer <command>

# Node/NPM
docker-compose exec node npm <command>
```
