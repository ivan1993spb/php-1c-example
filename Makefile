
install:

	composer install

test:

	COMPOSER_PROCESS_TIMEOUT=3600 composer test

clean:

	rm -rf vendor
	git clean -fd
