POSTGRES_USER=oro
POSTGRES_PASSWORD=oro
POSTGRES_DB=doctrine_extensions_tests
MYSQL_USER=oro
MYSQL_PASSWORD=oro
MYSQL_DATABASE=doctrine_extensions_tests
MYSQL_RANDOM_ROOT_PASSWORD=true

ORO_DB_HOST=0.0.0.0
ORO_DB_USER=oro
ORO_DB_PASS=oro
ORO_DB_NAME=doctrine_extensions_tests
export

PHP?=7.4
PGSQL?=9.6
MYSQL?=5.5
TARGET_DIR=/app

syntax: vendor
	docker run --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpcs src/ tests/ -p --encoding=utf-8 --extensions=php --standard=psr2

pgsql: export ORO_DB_DRIVER=pdo_pgsql
pgsql: export ORO_DB_PORT=5432
pgsql: vendor
	docker run -d --env-file <(env | grep POSTGRES_) --rm -it -p "5432:5432" --name postgres postgres:$(PGSQL) && sleep 30
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit tests/Oro/Tests/Connection/SetupTest.php
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit --testsuite="Oro Doctrine Extensions Test Suite"
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit tests/Oro/Tests/Connection/TearDownTest.php
	docker ps -a -q --filter="name=postgres" | xargs docker rm -fv

mysql: export ORO_DB_DRIVER=pdo_mysql
mysql: export ORO_DB_PORT=3306
mysql: vendor
	docker run -d --env-file <(env | grep MYSQL_) --rm -it -p "3306:3306" --name mysql mysql:$(MYSQL) && sleep 30
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit tests/Oro/Tests/Connection/SetupTest.php
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit --testsuite="Oro Doctrine Extensions Test Suite"
	docker run --network=host --env-file <(env | grep ORO_DB_) --rm -v $(shell pwd):$(TARGET_DIR) -w $(TARGET_DIR) -it oroinc/php:$(PHP) php vendor/bin/phpunit tests/Oro/Tests/Connection/TearDownTest.php
	docker ps -a -q --filter="name=mysql" | xargs docker rm -fv

clear:
	-docker ps -a -q --filter="name=postgres" | xargs docker rm -fv
	-docker ps -a -q --filter="name=mysql" | xargs docker rm -fv

vendor: composer.phar
	docker run --rm --interactive --tty \
		--volume $(shell pwd):$(TARGET_DIR) \
		--volume $(SSH_AUTH_SOCK):/ssh-auth.sock \
		--volume /etc/passwd:/etc/passwd:ro \
		--volume /etc/group:/etc/group:ro \
		--env SSH_AUTH_SOCK=/ssh-auth.sock \
		--workdir $(TARGET_DIR) \
		--user $(shell id -u):$(shell id -g) \
		oroinc/php:$(PHP) php composer.phar install --prefer-dist

composer.phar:
	wget https://getcomposer.org/download/latest-2.2.x/composer.phar

.PHONY: tests
.DEFAULT: tests
