#!/bin/bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

if [ "$role" = "app" ]; then

    exec apache2-foreground

elif [ "$role" = "queue" ]; then

    echo "Running the queue..."
    (cd /var/www/html && php artisan queue:work --verbose --tries=3 --timeout=90)

elif [ "$role" = "scheduler" ]; then

    while [ true ]
    do
      php /var/www/html/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi