# Clearfacts codestyle component

This component provides an integration with php-cs-fixer
- every time you commit, the installed package hooks will run and fix the styling.
- commiting is blocked when a staged file has invalid styling

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

- `composer require clearfacts/cf-codestyle`

- After composer has sucessfully run, add these scripts to composer.json:
```json
  ...,
    "scripts": {
        "set-up": [
           "@copy-phpcs-config",
           "vendor/bin/cf-codestyle clearfacts:codestyle:hooks-setup" // optional parameter --custom-hooks-dir=custom-dir (default is null)
        ],
        "copy-phpcs-config": "vendor/bin/cf-codestyle clearfacts:codestyle:copy-cs-config", // optional parameter --config-dir=./app/config (default is config)
  ...
```
### Composer script parameters
> custom-hooks-dir => Here you can place your custom hooks that are executed on pre-commit

> config-dir => This is where the codestyle configuration files will be copied to


- Add a Makefile (or modify your existing one) in the root directory of your project and add the following content:

```make
    # Linting and testing
    setup: ## Setup git-hooks
	    @composer run set-up

    copy-phpcs-config: ## Setup phpcs config
        @composer run copy-phpcs-config
    
    options?=
    files?=src/
    lint-phpcs: ## Check phpcs.
        @bin/php-cs-fixer fix --config=./config/.php-cs --dry-run --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)
    
    lint-phpcs-fix: ## Check phpcs and try to automatically fix issues.
        @bin/php-cs-fixer fix --config=./config/.php-cs --diff --using-cache=no --allow-risky=yes --ansi $(options) $(files)
```

- run make setup
- add your copied config file (.php-cs) to .gitignore
- make a commit and check that the hooks are correctly run!
    You can test this by changing a file to non-valid styling and check if is fixed after committing.
