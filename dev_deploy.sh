#!/bin/bash

set -e

echo "🚀 Starting deployment..."

git pull origin main

if [ -f ".env" ]; then
    echo "✅ .env file exists, skipping generation."
else
    echo "📄 Creating .env file..."
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    docker run --rm \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
else
    echo "✅ Vendor directory exists, skipping Composer install."
fi

echo "🐳 Starting Docker containers..."
./vendor/bin/sail up -d

./vendor/bin/sail ps | grep "laravel.test" > /dev/null
if [ $? -ne 0 ]; then
    echo "❌ Sail is not running. Check your Docker setup."
    exit 1
fi

echo "⏳ Waiting for database to be ready..."
DB_READY=1
for i in {1..10}; do
    if ./vendor/bin/sail artisan migrate:status &>/dev/null; then
        DB_READY=0
        break
    fi
    echo "⏳ Database not ready yet... Retrying in 3 seconds."
    sleep 3
done

if [ $DB_READY -ne 0 ]; then
    echo "❌ Database connection failed. Restarting containers..."
    ./vendor/bin/sail down
    ./vendor/bin/sail up -d
    sleep 5
fi

echo "📊 Running migrations..."
./vendor/bin/sail artisan migrate --force

if [ ! -d "node_modules" ]; then
    echo "💼 Installing Npm dependencies..."
    docker run --rm \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        node:18 \
        npm install \
        npm run build
else
    echo "✅ node_modules directory exists, skipping Composer install."
fi

#echo "⚙️ Building frontend..."
#./vendor/bin/sail npm run build
#./vendor/bin/sail npm run ssr

echo "✅ Deployment complete! You can access the website at http://localhost:8082"
