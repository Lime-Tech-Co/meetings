#!/bin/bash

# Defaults to an app server
role=${CONTAINER_ROLE:-queue}

echo "Container role: $role"

if [ "$role" = "queue" ]; then
    # Run queue
    while [ true ]
    do
      if [ -d "./vendor" ] 
      then
        php artisan queue:work --queue=user_registration_queue,listeners,delete_files_queue,busy_times_importer_queue --verbose --tries=3 --timeout=90 --no-interaction
      else
        echo "composer command is running...."
        sleep 60
      fi
    done
fi