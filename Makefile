r.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Install symfony project (dev)
	composer install
	make build_assets
	make build

reinstall: ## Reinstall symfony project (dev)
	composer install
	make build_assets
	make rebuild

rebuild: ## Rebuild database (dev)
	php bin/console doctrine:database:drop --force --no-interaction
	make build
	rm -rf files

build: ## Build database (dev)
	php bin/console doctrine:database:create --if-not-exists --no-interaction
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --append --no-interaction

build_assets: ## build assets (dev)
	yarn install
	yarn encore dev

install_prod: ## Install symfony project (prod)
	composer install
	yarn install
	yarn encore prod
	php bin/console doctrine:database:create --if-not-exists --no-interaction
	php bin/console doctrine:migrations:migrate --no-interaction