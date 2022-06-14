# cf-codestyle makefile

.DEFAULT_GOAL := list

.PHONY: list
list:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Setup this project.
	@make composer
	@make setup

# Composer commands
composer: ## Do a composer install for the php project.
	@composer install

setup: ## Setup git-hooks
	@composer run set-up

copy-cs-config: ## Setup cs config
	@composer run copy-cs-config

# Linting
options?=
files?="src\ tests"
phpcs: ## Check phpcs.
	@bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

phpcs-fix: ## Check phpcs and try to automatically fix issues.
	@bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)

eslint: ## Check eslint.
	@eslint --fix-dry-run --config=.eslintrc.dist $(options) $(files)

eslint-fix: ## Check eslint and try to automatically fix issues.
	@eslint --fix --config=.eslintrc.dist $(options) $(files)

# no dry-run possible for twig.
twig-fix: ## Check twig and try to automatically fix issues.
	@bin/console lint:twig --ansi $(options) $(files)

# Testing
args?="tests"
test: ## Run tests.
	@bin/phpunit $(args)
