.PHONY: dev ## Test will be run, also phpcs fixer script will be run
dev: tests fixer

.PHONY: it ## All setup process will be handled
it:
	@echo "Please make sure your docker is run :)"

	docker-compose up -d
	docker exec meetings-app-1 bash -c "composer install"
	docker exec meetings-app-1 bash -c "cp .env.example .env"
	docker exec meetings-app-1 bash -c "php artisan key:generate"

	@echo "database migrations and seeders..."

	docker exec meetings-app-1 bash -c "php artisan migrate"
	docker exec meetings-app-1 bash -c "php artisan db:seed"

.PHONY: tests ## Tests will be run
tests:
	@echo "running tests ..."
	docker exec meetings-app-1 sh -c "php artisan test"

.PHONY: fixer ## phpcs fixer will be run
fixer:
	docker exec meetings-app-1 bash -c "PHP_CS_FIXER_IGNORE_ENV=1 composer run-script cs"