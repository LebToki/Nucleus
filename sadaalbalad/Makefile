# Convenience targets for local development
.PHONY: deploy deploy-migrate serve test

## Deploy code to remote production (rsync)
deploy:
	./scripts/deploy.sh

## Deploy code and run migrations
deploy-migrate:
	./scripts/deploy.sh --migrate

## Show deploy configuration
deploy-config:
	./scripts/deploy.sh --help

## Run local development server
serve:
	php artisan serve

## Run test suite
test:
	php artisan test
