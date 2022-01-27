# Clearfacts codestyle component

This component provides an integration with php-cs-fixer
- every time you commit, the installed package hooks will run and fix the styling.
- commiting is blocked when a staged file has invalid styling

## Rules

At the moment we have some general rules applied, bundled under the @Symfony directive in our (phpcs config)[templates/cs/.php-cs-fixer.dist.php]. This directive includes PSR-12. For documentation see (FriendsOfPHP/PHP-CS-Fixer)[https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/ruleSets/Symfony.rst]. 

## Installation

- Add the following to composer:

```json
    ...,
    "repositories": [
        ...,
        { "type": "vcs", "url": "https://github.com/Clearfacts/cf-codestyle" }
    ],
    ...
```

- `composer require clearfacts/cf-codestyle --dev`

## Usage

- After composer has sucessfully run, add these scripts to composer.json:
```json
  ...,
    "scripts": {
        "set-up": [
           "@copy-phpcs-config",
           "vendor/bin/cf-codestyle clearfacts:codestyle:hooks-setup"
        ],
        "copy-phpcs-config": "vendor/bin/cf-codestyle clearfacts:codestyle:copy-cs-config",
  ...
```


- Add a Makefile (or modify your existing one) in the root directory of your project and add the following content.
Depending on whether your project is running locally, or via docker-compose, the Makefile is slightly different:
 
 
 -  [Using local setup](#local-setup)
  
 - [Using docker-compose](#docker-setup)


### Local setup
```make
    # Linting and testing
    setup: ## Setup git-hooks
	    @composer run set-up

    copy-phpcs-config: ## Setup phpcs config
        @composer run copy-phpcs-config
    
    options?=
    files?=src/
    phpcs: ## Check phpcs.
        @vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)
    
    phpcs-fix: ## Check phpcs and try to automatically fix issues.
        @vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)
```

### Docker setup

When using docker-compose, your `Makefile` will slightly differ. Important here is that the commands are executed with -T.

```make
    ...
    dc: ## Does docker-compose with the right projectname and config, so you can call anything allowed through `docker-compose`, passed through the parameter `cmd`.
	    @docker-compose -p $(name) -f docker/docker-compose.yaml $(cmd)

    det: ## An extension of `make dc` that calls `docker-compose exec` on the php-container.
	    @make dc cmd="exec -T $(phpcontainer) $(cmd)"

    # Linting and testing
    setup: ## Setup git-hooks
	    @make det cmd="composer.phar run set-up"

    copy-phpcs-config: ## Setup phpcs config
        @make det cmd="composer.phar run copy-phpcs-config"
    
    options?=
    files?=src/
    phpcs: ## Check phpcs.
        @make det cmd="vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)"
    
    phpcs-fix: ## Check phpcs and try to automatically fix issues.
        @make det cmd="vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)"
```


- run `make setup`
- add your copied config file (.php-cs-fixer.dist.php) to .gitignore
- make a commit and check that the hooks are correctly run!
    
You can test this by changing a file to non-valid styling and check if is fixed after committing.

### Commands

`vendor/bin/cf-codestyle clearfacts:codestyle:copy-cs-config`

- `--root` When you need to copy the cs config to a different directory, by default the root directory of your project

`vendor/bin/cf-codestyle clearfacts:codestyle:hooks-setup`

- `--root` The directory your .git/hooks folder is located, by default the root of your project
- `--custom-hooks-dir` When you have some custom pre-commit hooks you want to install, you can place them in this folder

