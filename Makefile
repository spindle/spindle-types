default: test

apigen:
	php -dopen_basedir= vendor/bin/apigen.php

test:
	php -dopen_basedir= vendor/bin/phpunit

pdepend:
	php -dopen_basedir= vendor/bin/pdepend --jdepend-chart=builds/pdepend.svg --overview-pyramid=builds/pyramid.svg src/

all: test pdepend apigen
