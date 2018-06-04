DOCKER_COMPOSE?=docker-compose
RUN=$(DOCKER_COMPOSE) run --rm app
EXEC?=$(DOCKER_COMPOSE) exec app
CONSOLE=bin/console
PHPCSFIXER?=$(RUN) php -d memory_limit=1024m vendor/bin/php-cs-fixer

##
## Project setup
##---------------------------------------------------------------------------

start: build up db ## Install and start the project

stop:                                                                                                  ## Remove docker containers
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) rm -v --force

reset: stop start

clear: perm                                                                                            ## Remove all the cache, the logs, the sessions and the built assets
	-$(EXEC) rm -rf var/cache/*
	-$(EXEC) rm -rf var/sessions/*
	rm -rf var/logs/*
	rm var/.php_cs.cache

clean: clear                                                                                           ## Clear and remove dependencies
	rm -rf vendor

cc:                                                                                                    ## Clear the cache in dev env
	$(EXEC) $(CONSOLE) cache:clear --no-warmup
	$(EXEC) $(CONSOLE) cache:warmup

tty:                                                                                                   ## Run app container in interactive mode
	$(RUN) /bin/bash

##
## Database
##---------------------------------------------------------------------------

wait-for-db:
	$(EXEC) php -r "set_time_limit(60);for(;;){if(@fsockopen('db',3306)){break;}echo \"Waiting for MySQL\n\";sleep(1);}"

db: vendor wait-for-db                                                                                 ## Reset the database and load fixtures
	$(EXEC) $(CONSOLE) doctrine:database:drop --force --if-exists
	$(EXEC) $(CONSOLE) doctrine:database:create --if-not-exists
	$(EXEC) $(CONSOLE) doctrine:migrations:migrate -n

db-diff: vendor wait-for-db                                                                            ## Generate a migration by comparing your current database to your mapping information
	$(EXEC) $(CONSOLE) doctrine:migration:diff

db-migrate: vendor wait-for-db                                                                         ## Migrate database schema to the latest available version
	$(EXEC) $(CONSOLE) doctrine:migration:migrate -n

db-rollback: vendor wait-for-db                                                                        ## Rollback the latest executed migration
	$(EXEC) $(CONSOLE) doctrine:migration:migrate prev -n

db-load: vendor wait-for-db                                                                            ## Reset the database fixtures
	$(EXEC) $(CONSOLE) doctrine:fixtures:load -n

db-validate: vendor wait-for-db                                                                        ## Check the ORM mapping
	$(EXEC) $(CONSOLE) doctrine:schema:validate

##
## Tests
##---------------------------------------------------------------------------

test: tu                                                                                       ## Run the PHP and the Javascript tests

tu: vendor                                                        ## Run the PHP unit tests
	$(EXEC) vendor/bin/phpunit

phpcs: vendor                                                                                          ## Lint PHP code
	$(PHPCSFIXER) fix --diff --dry-run --no-interaction -v

phpcsfix: vendor                                                                                       ## Lint and fix PHP code to follow the convention
	$(PHPCSFIXER) fix

security-check: vendor                                                                                 ## Check for vulnerable dependencies
	$(EXEC) vendor/bin/security-checker security:check

##
## Dependencies
##---------------------------------------------------------------------------

deps: vendor                                                                                ## Install the project PHP and JS dependencies

##

# Internal rules

build:
	$(DOCKER_COMPOSE) pull --parallel --ignore-pull-failures
	$(DOCKER_COMPOSE) build --force-rm
	rm -rf .php_cs
	cp .php_cs.dist .php_cs

up:
	$(DOCKER_COMPOSE) up -d --remove-orphans

perm:
	$(EXEC) chown -R www-data:root var

# Rules from files

vendor: composer.lock
	$(EXEC) composer install -n

composer.lock: composer.json
	@echo compose.lock is not up to date.