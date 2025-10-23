#!/bin/bash

echo "🚀 QrMenu Docker Setup Script"
echo "=============================="

# Stop and remove existing containers
echo "📦 Stopping and removing existing containers..."
docker-compose down -v

# Remove old images
echo "🗑️  Removing old images..."
docker-compose rm -f

# Build new images
echo "🔨 Building new Docker images..."
docker-compose build --no-cache

# Start containers
echo "▶️  Starting containers..."
docker-compose up -d

# Wait for PostgreSQL to be ready
echo "⏳ Waiting for PostgreSQL to be ready..."
sleep 10

# Copy .env.example to .env if it doesn't exist
if [ ! -f backend/.env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp backend/.env.example backend/.env
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec -T app-api composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec -T app-api php artisan key:generate

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T app-api php artisan migrate --force --seed

# Clear and cache config
echo "🧹 Clearing and caching configuration..."
docker-compose exec -T app-api php artisan config:clear
docker-compose exec -T app-api php artisan cache:clear
docker-compose exec -T app-api php artisan route:clear
docker-compose exec -T app-api php artisan view:clear

# Set permissions
echo "🔐 Setting permissions..."
docker-compose exec -T app-api chmod -R 775 storage bootstrap/cache
docker-compose exec -T app-api chown -R www-data:www-data storage bootstrap/cache

echo ""
echo "✅ Setup complete!"
echo ""
echo "📍 Services are running at:"
echo "   - Laravel API: http://localhost:8080"
echo "   - Frontend: http://localhost:3000"
echo "   - Mailpit: http://localhost:8025"
echo "   - PostgreSQL: localhost:5432"
echo "   - Redis: localhost:6379"
echo ""
echo "🔧 Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop services: docker-compose down"
echo "   - Restart services: docker-compose restart"
echo "   - Access PHP container: docker-compose exec app-api bash"
echo ""
