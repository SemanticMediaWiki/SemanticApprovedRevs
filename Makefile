-include .env
export

# setup for docker-compose-ci build directory
# delete "build" directory to update docker-compose-ci

ifeq (,$(wildcard ./build/))
    $(shell git submodule update --init --remote)
endif

EXTENSION=SemanticApprovedRevs

# docker images
MW_VERSION?=1.43
PHP_VERSION?=8.1
DB_TYPE?=mysql
DB_IMAGE?="mariadb:10"

# extensions
SMW_VERSION?=7.3.1
AR_VERSION ?= master

# composer
# Enables "composer update" inside of extension
COMPOSER_EXT?=true

# nodejs
# Enables node.js related tests and "npm install"
# NODE_JS?=true

# check for build dir and git submodule init if it does not exist
include build/Makefile



.PHONY: composer-phan
composer-phan: .init ## Run Phan static analysis
	$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan $(COMPOSER_PARAMS)"

.PHONY: composer-phan-update-baseline
composer-phan-update-baseline: .init ## Re-generate baseline and fix indentation for PHPCS
	-$(compose-exec-wiki) bash -c "cd $(EXTENSION_FOLDER) && composer phan -- --save-baseline=.phan/baseline.php"
	$(compose) cp wiki:$(EXTENSION_FOLDER)/.phan/baseline.php .phan/baseline.php
	unexpand --first-only -t 4 .phan/baseline.php > /tmp/baseline.php && mv /tmp/baseline.php .phan/baseline.php

# Phan runs on the coverage row only, to avoid baseline mismatches across MW versions
ci-coverage: composer-phan
