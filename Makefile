.PHONY: it
it:
	@echo "Please make sure your docker is running :)"
	docker-compose build
	docker-compose up -d
	docker exec meetings-app-1 bash -c "composer install"
	docker exec meetings-app-1 bash -c "cp .env.example .env"
	docker exec meetings-app-1 bash -c "php artisan key:generate"
	docker exec meetings-app-1 bash -c "php artisan migrate"
	docker exec meetings-app-1 bash -c "php artisan db:seed"
	docker exec meetings-app-1 bash -c "php artisan storage:link"
	docker exec meetings-app-1 bash -c "composer docs"
	docker exec meetings-app-1 bash -c "php artisan test"
.PHONY: tests
tests:
	@echo "running tests ..."
	docker exec meetings-app-1 sh -c "php artisan test"

.PHONY: fixer
fixer:
	docker exec meetings-app-1 bash -c "PHP_CS_FIXER_IGNORE_ENV=1 composer run-script cs"

.PHONY: dev
dev: tests fixer