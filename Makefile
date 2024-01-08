.PHONY: all

up:
	docker-compose up -d
.PHONY: up

down:
	docker-compose down
.PHONY: down

logs:
	docker-compose logs -f
.PHONY: logs

build:
	docker-compose build --no-cache
.PHONY: build

ssh:
	docker-compose exec app sh -c "cd ../project && sh"
.PHONY: ssh

cc:
	docker-compose exec app sh -c "cd ../project && php bin/console cache:clear"
.PHONY: cc

db-diff:
	docker-compose exec app sh -c "cd ../project && php bin/console doctrine:migrations:diff"
.PHONY: db-diff

db-migrate:
	docker-compose exec app sh -c "cd ../project && php bin/console doctrine:migrations:migrate"
.PHONY: db-migrate

db-prev:
	docker-compose exec app sh -c "cd ../project && php bin/console doctrine:migrations:migrate prev"
.PHONY: db-prev

cs:
	docker-compose exec app sh -c "cd ../project && vendor/bin/php-cs-fixer fix"
.PHONY: cs

generate-test-data:
	@if [ -z $(num) ]; then \
		echo "Usage: generate-test-data num=<number>"; \
	else \
		docker-compose exec app sh -c "cd ../project && php bin/console app:generate-test-data $(num)"; \
	fi
.PHONY: generate-test-data

test-coverage:
	docker-compose exec app sh -c "cd ../project && vendor/bin/phpunit --coverage-text"
.PHONY: test-coverage

composer-install:
	docker-compose exec app sh -c "cd ../project && composer install --no-interaction --optimize-autoloader"
.PHONY: ssh



