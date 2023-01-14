.PHONY: dev
dev: tests queue schedule

.PHONY: it
it: setup

.PHONY: setup
setup:
	docker-compose up -d
	@echo "Please make sure your docker is run :)"
	docker exec -it meetings-app-1 bash -c "composer install"
	docker exec -it meetings-app-1 bash -c "cp .env-example .env"
	docker exec -it meetings-app-1 bash -c "php artisan key:generate"

	@echo "database password will be set in env file"
	docker exec -it meetings-app-1 bash -c php "artisan config:set DB_DATABASE=lime"
	docker exec -it meetings-app-1 bash -c php "artisan config:set DB_USERNAME=default"
	docker exec -it meetings-app-1 bash -c php "artisan config:set DB_PASSWORD=secret"
	
	@echo "database migration ..."
	docker exec -it meetings-app-1 bash -c "php artisan migrate"

	@echo"Default queue driver will be changed to redis"
	docker exec -it meetings-app-1 bash -c "php artisan config:set QUEUE_CONNECTION=sync"

.PHONY: tests
tests:
	docker exec -it meetings-app-1 sh -c "php artisan test"

.PHONY: kill
kill:
	docker-compose down
	docker ps

.PHONY: fixer
fixer:
	docker exec -it meetings-app-1 sh -c "PHP_CS_FIXER_IGNORE_ENV=1 composer run-script cs"

.PHONY: queue
queue:
	docker exec -it meetings-queue-1 bash -c "php artisan config:set CACHE_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set SESSION_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set QUEUE_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set REDIS_HOST=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan queue:work --verbose --tries=3 --timeout=90 --no-interaction"
.PHONY: schedule
schedule:
	docker exec -it meetings-queue-1 bash -c "php artisan config:set CACHE_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set SESSION_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set QUEUE_DRIVER=redis"
	docker exec -it meetings-queue-1 bash -c "php artisan config:set REDIS_HOST=redis"
	docker exec -it meetings-scheduler-1 bash -c "php artisan schedule:run --verbose --no-interaction"