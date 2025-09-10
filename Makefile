# Makefile para ejecutar tests en un contenedor Docker con PHP 8.1

DOCKER_IMAGE=php:8.1-cli
PROJECT_DIR=$(shell pwd)

.PHONY: test install-deps update-deps help

help:
	@echo "\n\033[1m🛠️  Wipop Payment PHP - Makefile Commands\033[0m\n"
	@echo "\033[1mGeneral:\033[0m"
	@echo "  🤖 help           Show this help message."
	@echo "\n\033[1mComposer (Dependencies):\033[0m"
	@echo "  📦 install-deps   Install Composer dependencies."
	@echo "  ♻️  update-deps    Update Composer dependencies."
	@echo "\n\033[1mTesting:\033[0m"
	@echo "  🧪 test           Run PHPUnit tests."
	@echo "  🧪 test-dox       Run PHPUnit tests with DOX format."
	@echo "\n"

test:
	docker run --rm -v $(PROJECT_DIR):/app -w /app $(DOCKER_IMAGE) vendor/bin/phpunit

test-dox:
	docker run --rm -v $(PROJECT_DIR):/app -w /app $(DOCKER_IMAGE) vendor/bin/phpunit --testdox

install-deps:
	docker run --rm -v $(PROJECT_DIR):/app -w /app composer:2 composer install

update-deps:
	docker run --rm -v $(PROJECT_DIR):/app -w /app composer:2 composer update
