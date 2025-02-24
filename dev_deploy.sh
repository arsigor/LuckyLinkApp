#!/bin/bash

# Output a message indicating the start of the deployment
echo "Starting deployment..."

# Update the repository
git pull origin main

# Check if the .env file exists
if [ -f ".env" ]; then
    echo ".env file exists, skipping generation."
else
    echo "Creating .env file..."
    cp .env.example .env
fi

# Update dependencies via Composer
echo "Installing composer dependencies..."
./vendor/bin/sail composer install --no-interaction --prefer-dist

# Update dependencies via npm (for Vue.js and SSR)
echo "Installing npm dependencies..."
./vendor/bin/sail npm install

# Install SSR dependencies (if applicable)
echo "Installing SSR dependencies..."
./vendor/bin/sail npm run dev

# Run database migrations
echo "Running database migrations..."
./vendor/bin/sail artisan migrate --force

# Cache configurations and routes
echo "Caching configurations..."
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache

# Build the frontend assets (including SSR)
echo "Building frontend assets (SSR)..."
./vendor/bin/sail npm run production

# Restart Docker containers to apply changes
echo "Restarting Docker containers..."
./vendor/bin/sail down
./vendor/bin/sail up -d

# Clear cache (if necessary)
echo "Clearing cache..."
./vendor/bin/sail artisan cache:clear

# Optionally, you can also restart the queue worker if used
# echo "Restarting queue worker..."
# ./vendor/bin/sail artisan queue:restart

# Output a message with a link to open the website
echo "Deployment complete! You can access the website at http://localhost:8082"
