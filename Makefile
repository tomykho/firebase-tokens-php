.DEFAULT_GOAL:= tests

.PHONY: phpstan
phpstan: ## Performs a static analysis
	vendor/bin/phpstan analyse

phpunit:
	vendor/bin/phpunit

.PHONY: tests
tests: phpstan phpunit

.PHONY: cs
cs:
	@vendor/bin/php-cs-fixer fix || true
