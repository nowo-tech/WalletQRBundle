# Makefile for Symfony 8.0 + PHP 8.5 Demo (Wallet QR Bundle)
# Run from this directory: make up, make install, etc.

# Prefer Compose V2; absolute docker path avoids shadowing by local docker/ when PATH has "." (REQ-MAKE-010).
DOCKER_BIN := $(shell PATH="/usr/local/bin:/usr/bin:/bin:$$PATH" command -v docker 2>/dev/null)
ifeq ($(DOCKER_BIN),)
COMPOSE ?= docker-compose
else
COMPOSE ?= $(shell $(DOCKER_BIN) compose version >/dev/null 2>&1 && echo "$(DOCKER_BIN) compose" || echo "docker-compose")
endif

.PHONY: help up down build install shell logs test test-coverage cache-clear update-bundle ensure-up restart test-e2e

help:
	@echo "Symfony 8.0 + PHP 8.5 Demo - Wallet QR Bundle"
	@echo ""
	@echo "  make up             Start containers"
	@echo "  make down           Stop containers"
	@echo "  make build          Build images (no cache)"
	@echo "  make install        Install dependencies (composer install in container)"
	@echo "  make shell          Open shell in PHP container"
	@echo "  make logs           Show container logs"
	@echo "  make test           Run tests"
	@echo "  make test-coverage  Run tests with coverage"
	@echo "  make cache-clear    Clear Symfony cache"
	@echo "  make update-bundle  Update nowo-tech/wallet-qr-bundle and clear cache"
	@echo "  make test-e2e       Playwright e2e: demo platform rules and axe (Docker image; DEMO_PORT overrides .env PORT)"
	@echo ""

up:
	@if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi
	$(COMPOSE) up -d
	@echo "Waiting for container to be ready..."
	@sleep 5
	@echo "Installing dependencies..."
	@$(COMPOSE) exec -T -e COMPOSER_MEMORY_LIMIT=-1 php composer install --no-interaction
	@PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
	[ -z "$$PORT" ] && PORT=$$(grep "^PORT=" .env.example 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
	echo "Demo started at: http://localhost:$$PORT"
down:
	$(COMPOSE) down

restart: ensure-up
	$(COMPOSE) restart

build:
	$(COMPOSE) build --no-cache

ensure-up:
	@if ! $(COMPOSE) exec -T php true 2>/dev/null; then \
		echo "Starting container..."; \
		$(COMPOSE) up -d; \
		sleep 3; \
	fi

install: ensure-up
	$(COMPOSE) exec -T -e COMPOSER_MEMORY_LIMIT=-1 php composer install --no-interaction

shell: ensure-up
	$(COMPOSE) exec php sh

logs:
	$(COMPOSE) logs -f

test: ensure-up
	$(COMPOSE) exec -e APP_ENV=test php composer test

test-coverage: ensure-up
	$(COMPOSE) exec -e APP_ENV=test php composer test-coverage

cache-clear: ensure-up
	$(COMPOSE) exec php php bin/console cache:clear

update-bundle: ensure-up
	$(COMPOSE) exec php composer dump-autoload --optimize
	$(COMPOSE) exec php php bin/console cache:clear --env=dev --no-warmup || true
	@echo "Bundle updated. Reload the page in the browser."


# REQ-MAKE-008: update-deps (REQ-MAKE-008)
SERVICE_PHP := php
BUNDLE_ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/../..)
# Optional: monorepo helper absent on standalone GitHub Actions checkout (REQ-MAKE-009).
-include $(BUNDLE_ROOT)/../.scripts/Makefile.demo-update-deps.mk

# REQ-DEMO-013: Playwright e2e (official Playwright image; the image tag follows the exact @playwright/test version pinned in e2e/package.json).
PLAYWRIGHT_VERSION := $(shell sed -n 's/.*"@playwright\/test": "\([0-9.]*\)".*/\1/p' e2e/package.json)
PLAYWRIGHT_IMAGE ?= mcr.microsoft.com/playwright:v$(PLAYWRIGHT_VERSION)-jammy

test-e2e: ensure-up
	@mkdir -p e2e/node_modules
	@PORT=$${DEMO_PORT:-$$(grep -E '^PORT=' .env 2>/dev/null | cut -d= -f2)}; PORT=$${PORT:-8004}; \
	docker run --rm --network host --user "$$(id -u):$$(id -g)" -e HOME=/tmp \
		-v "$(CURDIR)/e2e:/e2e" -w /e2e \
		-e DEMO_BASE_URL=http://127.0.0.1:$$PORT \
		$(PLAYWRIGHT_IMAGE) \
		bash -lc 'npm ci --no-fund --no-audit && npx playwright test --project=chromium'
