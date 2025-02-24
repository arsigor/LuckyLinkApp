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
        --pull=always \
        -v "$(pwd)":/opt \
        -w /opt \
        laravelsail/php84-composer:latest \
        bash -c "composer install"
else
    echo "✅ Vendor directory exists, skipping Composer install."
fi

echo "🐳 Starting Docker containers..."
./vendor/bin/sail up -d

./vendor/bin/sail ps | grep "lapp.test" > /dev/null
if [ $? -ne 0 ]; then
    echo "❌ Sail is not running. Check your Docker setup."
    exit 1
fi

echo "📊 Running migrations..."
./vendor/bin/sail artisan migrate --force

if [ ! -d "node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    ./vendor/bin/sail npm install
else
    echo "✅ Node modules exist, skipping npm install."
fi

echo "⚙️ Building frontend..."
./vendor/bin/sail npm run build
./vendor/bin/sail npm run ssr

echo "✅ Deployment complete! You can access the website at http://localhost:8082"
