# QrMenu - Laravel + Next.js + PostgreSQL + Docker

A modern, production-ready full-stack application with Laravel backend, Next.js frontend, and PostgreSQL database, all containerized with Docker.

## 🚀 Quick Start

**One-command setup:**
```bash
chmod +x setup.sh && ./setup.sh
```

**Access your application:**
- **Laravel API**: http://localhost:8080
- **Next.js Frontend**: http://localhost:3000
- **Filament Admin Panel** (if enabled): http://localhost:8080/admin

## 📋 Prerequisites

- **Docker** & **Docker Compose** installed
- **Git** (for cloning the repository)

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.3)
- **Frontend**: Next.js 14 (React, TypeScript, Tailwind CSS)
- **Database**: PostgreSQL 16
- **Cache/Queue**: Redis
- **Web Server**: Nginx 1.25
- **Mail Testing**: Mailpit

## 📦 Services & Ports

| Service | Container | Port | Description |
|---------|-----------|------|-------------|
| Nginx | `app-nginx` | 8080 | Web server |
| PHP-FPM | `app-api` | - | Laravel backend |
| PostgreSQL | `app-postgres` | 5432 | Database |
| Redis | `app-redis` | 6379 | Cache & sessions |
| Next.js | `app-front` | 3000 | Frontend |

## 🔧 Manual Setup

If you prefer step-by-step setup:

```bash
# 1. Stop any existing containers
docker compose down -v

# 2. Build Docker images
docker compose build --no-cache

# 3. Start containers
docker compose up -d

# 4. Setup Laravel
docker compose exec app-api composer install
docker compose exec app-api cp .env.example .env
docker compose exec app-api php artisan key:generate
docker compose exec app-api php artisan migrate --seed

# 5. Setup Next.js
docker compose exec app-front npm install
docker compose exec app-front npm run dev

## 🐛 Troubleshooting

### 502 Bad Gateway
```bash
docker compose restart app-api
docker compose logs app-api
```

### Database Connection Error
```bash
docker compose restart app-postgres
docker compose exec app-api php artisan config:clear
```

### Permission Issues
```bash
docker compose exec app-api chmod -R 775 storage bootstrap/cache
docker compose exec app-api chown -R www-data:www-data storage bootstrap/cache
```

### Role/Permission Issues
If you changed role/permission identifiers, reseed or rename them in DB and reset caches:
```bash
docker compose exec app-api php artisan db:seed --class=RolesSeeder
docker compose exec app-api php artisan permission:cache-reset
```

### Complete Reset
```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```

## 📖 Project Structure

```
QrMenu/
├── backend/              # Laravel application
├── frontend/app/         # Next.js application
├── docker/               # Docker configurations
│   ├── nginx/           # Nginx config
│   ├── php/             # PHP-FPM config
│   └── front/           # Frontend Dockerfile
├── docker-compose.yml   # Docker Compose config
├── setup.sh             # Automated setup script
├── validate.sh          # Validation script
└── cleanup.sh           # Cleanup script
```

## 🚀 Deployment (Docker, Production)

Follow these concise steps to deploy on a fresh Linux server (Ubuntu/Debian recommended).

### 1) Prepare the server
- **Install Docker + Docker Compose** (see official docs)
- **Open firewall**: 80/443 (HTTP/HTTPS). Optionally 22 (SSH), 8080 (API), 3000 (frontend dev/test).

```bash
sudo apt update
sudo apt install -y ca-certificates curl gnupg
# Install Docker Engine: https://docs.docker.com/engine/install/ubuntu/
docker --version && docker compose version
```

### 2) Clone project and set environment
```bash
git clone <your_repo_url> QrMenu
cd QrMenu
cp backend/.env.example backend/.env
```

- Edit `backend/.env` (production basics):
```env
APP_NAME=QrMenu
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=app-postgres
DB_PORT=5432
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=strong_password

REDIS_HOST=app-redis

FRONTEND_URL=https://yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,api.yourdomain.com
SESSION_DOMAIN=.yourdomain.com

FILESYSTEM_DISK=public
```

- Create `frontend/app/.env` (if missing):
```env
NEXT_PUBLIC_API_URL=https://api.yourdomain.com/api
NODE_ENV=production
```

### 3) Persistent data (volumes/storage)
Ensure Docker volumes or bind mounts persist for:
- PostgreSQL data
- Laravel `storage/` (uploads, caches) and `bootstrap/cache`
- Optional: Nginx logs

### 4) Build and start services
```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```

### 5) Initialize Laravel
```bash
docker compose exec app-api composer install --no-dev --optimize-autoloader
docker compose exec app-api php artisan key:generate
docker compose exec app-api php artisan storage:link
docker compose exec app-api php artisan migrate --force --seed
docker compose exec app-api php artisan optimize
```

If permissions are restrictive on host:
```bash
sudo chown -R $USER:$USER backend/storage backend/bootstrap/cache
```

### 6) HTTPS and domains (reverse proxy)
Point DNS:
- `yourdomain.com` → server IP (frontend)
- `api.yourdomain.com` → server IP (Laravel API)

Use a host reverse proxy (e.g., Caddy) for TLS:
```
yourdomain.com {
  reverse_proxy 127.0.0.1:3000
}

api.yourdomain.com {
  reverse_proxy 127.0.0.1:8080
}
```

Alternative: terminate SSL in Docker (Traefik/Certbot) via an override compose file.

### 7) Next.js production build
```bash
docker compose exec app-front npm ci --omit=dev
docker compose exec app-front npm run build
docker compose restart app-front
```

Alternative: static export (no SSR) to be served by Nginx from `backend/public/`:
```bash
docker compose exec app-front npm ci --omit=dev
docker compose exec app-front npm run build:export
# Then reload Nginx serving Laravel/public
docker compose restart app-nginx
```

### 8) Queues and scheduler (optional)
- Queues:
```bash
docker compose exec app-api php artisan queue:work --daemon
```
- Scheduler (host cron):
```bash
crontab -e
# Every minute
* * * * * docker compose exec -T app-api php artisan schedule:run >> /dev/null 2>&1
```

### 9) Backups
- DB backup:
```bash
docker compose exec app-postgres pg_dump -U app app > backup_$(date +%F).sql
```
- Files: archive `backend/storage/` (or mounted path)

### 10) Logs and health
```bash
docker compose logs -f --tail=200
docker compose ps
docker compose restart
```

### 11) Deploying updates
```bash
git pull
docker compose build
docker compose up -d
docker compose exec app-api php artisan migrate --force
docker compose exec app-api php artisan optimize
```

### 12) Common pitfalls
- **CORS/cookies**: align `APP_URL`, `FRONTEND_URL`, `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN` with your domains.
- **HTTPS**: use `https://` in `NEXT_PUBLIC_API_URL`.
- **Storage**: run `php artisan storage:link`; ensure disk is `public`.
- **Hydration warnings**: already handled in app; verify env URLs.
