#!/bin/bash

# Run scheduler
while [ true ]
do
  if [ -d "./vendor" ] 
  then
    php artisan schedule:run --verbose --no-interaction
    sleep 60
  else
    echo "composer command is running...."
    sleep 60
  fi
done