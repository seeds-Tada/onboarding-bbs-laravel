#!/bin/bash
set -e

FLAG_FILE=/var/www/html/storage/.initial-set-up-done

echo "Waiting for database"
/usr/local/bin/wait-for-it.sh db:3306 -t 30 --

if [ ! -f "$FLAG_FILE" ]; then
    echo "Running setup commands..."
    php artisan migrate
    php artisan db:seed

    mkdir -p /var/www/html/storage/
    touch "$FLAG_FILE"
    echo "Setup completed"
else
    echo "Skipping migrate/seeder."
fi
echo "starting Apach forground process..."
exec /usr/local/bin/apache2-foreground