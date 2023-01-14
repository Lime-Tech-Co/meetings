#!/bin/bash

# Defaults to an app server
role=${CONTAINER_ROLE:-queue}

echo "Container role: $role"

if [ "$role" = "queue" ]; then
    # Run queue
    while [ true ]
    do
      php artisan queue:work --queue=listeners,delete_files_queue,busy_times_importer_queue,user_registration_queue --verbose --tries=3 --timeout=90 --no-interaction
    done
fi