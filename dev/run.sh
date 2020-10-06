#!/bin/bash

if [ $# -gt 0 ]; then
    echo "Stopping services"
    docker-compose down
    echo "Stopping services done..."
    exit 1
fi

echo "Stopping services"
docker-compose down
echo "Stopping services done..."
echo "Restart services..."
docker-compose up -d --build
echo "Please wait while service is up..."
cp .env ../src/.env
sleep 5
echo "Grant permissions..."
docker-compose exec app touch storage/logs/laravel.log
docker-compose exec app chmod -R 0777 storage/ vendor/
docker-compose exec app chown www-data storage/
echo "Install project dependencies..."
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
echo "Done..."
sleep 1
docker-compose exec app php artisan migrate
docker-compose exec -d app php artisan horizon
echo "All done"