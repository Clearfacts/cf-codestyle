.DEFAULT_GOAL := list

docker=docker run -it --volume $$PWD:/var/www/html -e COMPOSER_MEMORY_LIMIT=-1 077201410930.dkr.ecr.eu-west-1.amazonaws.com/cf-docker-base-php:8.1.7

.PHONY: list
list:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Setup this project.
	@make composer
	@make setup

setup: ## Setup git-hooks
	@$(docker) composer.phar run set-up

copy-cs-config: ## Setup cs config
	@$(docker) composer.phar run copy-cs-config

bash: ## ssh into the php container.
	@$(docker) bash

# Composer commands
composer: ## Do a composer install.
	@$(docker) composer.phar install
composer-update: ## Do a composer update.
	@$(docker) composer.phar update

# Linting and testing
args?=
options?=
files?=src/

test: ## Run all tests with an optional parameter `args` to run a specific suite or test-file, or pass some other testing arguments.
	@$(docker) vendor/bin/phpunit $(args)

phpcs: ## Check phpcs.
	@$(docker) vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

phpcs-fix: ## Check phpcs and try to automatically fix issues.
	@$(docker) vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

psalm: ## Check phpcs and try to automatically fix issues.
	@$(docker) vendor/bin/psalm $(options)