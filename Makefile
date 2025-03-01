init:
	docker-compose up -d --build
	docker-compose exec php composer install
	docker-compose exec php cp .env.example .env
	docker-compose exec php php artisan key:generate
	docker-compose exec php chmod -R 777 storage bootstrap/cache
	@make wait-for-mysql
	@make fresh

fresh:
	docker-compose exec php php artisan migrate:fresh --seed

restart:
	@make down
	@make up

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

cache:
	docker-compose exec php php artisan cache:clear
	docker-compose exec php php artisan config:cache

stop:
	docker-compose stop

# MySQLが完全に起動するまで待機する処理
wait-for-mysql:
	@echo "Waiting for MySQL to be ready..."
	@until docker-compose exec mysql mysqladmin ping -h"mysql" --silent; do \
		echo "Waiting for database connection..."; \
		sleep 2; \
	done
	@echo "MySQL is ready!"
