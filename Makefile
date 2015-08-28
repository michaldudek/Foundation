# Makefile
# 
# Common Makefile for web projects.
# 
# @author		Michał Pałys-Dudek <michal@michaldudek.pl>
# @link			TBD
# @version		0.1.0
# @date			28.08.2015
# ---------------------------------------------------------------------------
# 
# MD\Foundation

.PHONY: help all install install_dev clear assets warmup run prepublish postpublish css js watch test lint qa report docs noop composer composer_dev workers workers_start assets assets_install cache cache_file cache_app knit_indexes phpunit phpcs phpcs_test phpcs_fix phpcs_test_fix phpmd jslint npm_dev

# Variables
# ---------------------------------------------------------------------------

# Current path.
CWD=`pwd`

# Previous release path.
PREVIOUS_PATH?=$(CWD)

# Current (new) release path.
CURRENT_PATH?=$(CWD)

# Targets
# ---------------------------------------------------------------------------

help:
	@echo ""
	@echo "Following commands are available:"
	@echo "(this is summary of the main commands,"
	@echo " but for more fine-grained commands see the Makefile)"
	@echo ""
	@echo "     make help           : This info."
	@echo ""
	@echo " Installation:"
	@echo "     make install        : Installs all dependencies for production."
	@echo "     make install_dev    : Installs all dependencies (including dev dependencies)"
	@echo "     make clear          : Clears any build artifacts, caches, installed packages, etc."
	@echo ""
	@echo " Quality Assurance:"
	@echo "     make test           : Run tests."
	@echo "     make lint           : Lint the code."
	@echo "     make qa             : Run tests, linters and any other quality assurance tool."
	@echo "     make report         : Build reports about the code / the project / the app."
	@echo "     make docs           : Build docs."
	@echo ""

# alias for help
all: help

# Installation
# ---------------------------------------------------------------------------

# Installs all dependencies for production.
install: composer

# Installs all dependencies (including dev dependencies)
install_dev: composer_dev

# Clears any build artifacts, caches, installed packages, etc.
clear: noop

# Installs / prepares / builds the frontend assets.
assets: noop

# Warms up the application.
warmup: noop

# Runs / restarts the application.
run: noop

# Deployment
# ---------------------------------------------------------------------------

# Runs everything that needs to be ran before publishing the app.
prepublish: noop

# Runs everything that needs to be ran after the app has been published.
postpublish: noop

# Development
# ---------------------------------------------------------------------------

# Build CSS.
css: noop

# Build JavaScript.
js: noop

# Watch files for changes and trigger appropriate tasks on changes.
watch: noop

# Quality Assurance
# ---------------------------------------------------------------------------

# Run tests.
test: phpunit

# Lint the code.
lint: phpcs phpmd

# Run tests, linters and any other quality assurance tool.
qa: test lint

# Build reports about the code / the project / the app.
report: phpunit_coverage

# Build docs.
docs: noop

# Misc
# ---------------------------------------------------------------------------

noop:
	@echo "Nothing to do."

# End of interface
# ----------------

# ---------------------------------------------------------------------------
# App specific commands
# ---------------------------------------------------------------------------

# install Composer dependencies for production
composer:
	composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# install Composer dependencies for development
composer_dev:
	composer install --no-interaction --prefer-dist

# run the PHPUnit tests
phpunit:
	php ./vendor/bin/phpunit -c phpunit.xml.dist

# run the PHPUnit tests with coverage report
phpunit_coverage:
	php ./vendor/bin/phpunit -c phpunit.cov.xml

# run PHPCS on the source code and show any style violations
phpcs:
	php ./vendor/bin/phpcs --standard="phpcs.xml" src

# run PHPCBF to auto-fix code style violations
phpcs_fix:
	php ./vendor/bin/phpcbf --standard="phpcs.xml" src

# run PHPCS on the test code and show any style violations
phpcs_test:
	php ./vendor/bin/phpcs --standard="phpcs.xml" tests

# run PHPCBF on the test code to auto-fix code style violations
phpcs_test_fix:
	php ./vendor/bin/phpcbf --standard="phpcs.xml" tests

# Run PHP Mess Detector on the source code
phpmd:
	php ./vendor/bin/phpmd src text ./phpmd.xml
