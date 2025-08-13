install:
	composer install

test:
	vendor/bin/phpunit

cs-fix:
	vendor/bin/phpcs --standard=PSR12 src tests

fixtures:
	bin/console doctrine:fixtures:load --no-interaction
