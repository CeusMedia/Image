install: composer-install
	@cp Mail.ini.dist Mail.ini

install-dev: composer-install-dev

composer-install:
	@test ! -f vendor/autoload.php && XDEBUG_MODE=off composer install --no-dev || true

composer-install-dev:
	@test ! -d vendor/phpunit/phpunit && XDEBUG_MODE=off composer install || true

composer-update:
	@XDEBUG_MODE=off composer update --no-dev

composer-update-dev:
	@XDEBUG_MODE=off composer update

#dev-analyse-phan: composer-install-dev
#	@XDEBUG_MODE=off ./vendor/bin/phan -k=.phan --color --allow-polyfill-parser || true
#
#dev-analyse-phan-report: dev-analyse-phan-save
#	@php vendor/ceus-media/phan-viewer/phan-viewer generate --source=phan.json --target=doc/phan/
#
#dev-analyse-phan-save: composer-install-dev
#	@XDEBUG_MODE=off PHAN_DISABLE_XDEBUG_WARN=1 ./vendor/bin/phan -k=.phan -m=json -o=phan.json --allow-polyfill-parser -p || true

dev-analyse-phpstan: composer-install-dev
	@vendor/bin/phpstan analyse --xdebug || true

dev-analyse-phpstan-save-baseline: composer-install-dev composer-update-dev
	@vendor/bin/phpstan analyse --generate-baseline phpstan-baseline.neon || true

#dev-doc: composer-install-dev
#	@test -f doc/API/search.html && rm -Rf doc/API || true
#	@php vendor/ceus-media/doc-creator/doc.php --config-file=doc.xml

#dev-test-all-with-coverage:
#	@XDEBUG_MODE=coverage vendor/bin/phpunit -v || true
#
#dev-test-integration: composer-install-dev
#	@XDEBUG_MODE=off vendor/bin/phpunit -v --no-coverage --testsuite integration || true
#
#dev-test-units: composer-install-dev
#	@XDEBUG_MODE=off vendor/bin/phpunit -v --no-coverage --testsuite unit || true
#
#dev-test-units-with-coverage: composer-install-dev
#	@XDEBUG_MODE=coverage vendor/bin/phpunit -v --testsuite unit || true
#
#dev-retest-integration: composer-install-dev
#	@XDEBUG_MODE=off vendor/bin/phpunit -v --no-coverage --testsuite integration --order-by=defects --stop-on-defect || true
#
#dev-retest-units: composer-install-dev
#	@XDEBUG_MODE=off vendor/bin/phpunit -v --no-coverage --testsuite unit --order-by=defects --stop-on-defect || true

dev-test-syntax:
	@find src -type f -print0 | xargs -0 -n1 xargs php -l
#	@find test -type f -print0 | xargs -0 -n1 xargs php -l

#dev-rector-apply:
#	@vendor/bin/rector process src
#
#dev-rector-dry:
#	@vendor/bin/rector process src --dry-run
#
#dev-test-units-parallel: composer-install-dev
#	@XDEBUG_MODE=off vendor/bin/paratest -v --no-coverage --testsuite unit || true
