# путь к .env
ENV=--env-file .env
# аргументы переданные вместе с вызовом инструкции
ARGS=$(filter-out $@, $(MAKECMDGOALS))

test:
	docker-compose exec -it fpm php vendor/bin/phpunit -d memory_limit=256M \
          --colors=never \
          --log-junit build/report.xml \
          --coverage-clover build/coverage.xml \
          --coverage-text

composer:
	docker run --rm \
    		--volume ${CURDIR}:/app \
    		--volume ${HOME}/.config/composer:/tmp \
    		--volume /etc/passwd:/etc/passwd:ro \
    		--volume /etc/group:/etc/group:ro \
    		--user $(shell id -u):$(shell id -g) \
    		--interactive \
    		composer composer ${ARGS} --ignore-platform-reqs
