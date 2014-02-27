default: test

apigen:
	apigen

test:
	phpunit

pdepend:
	pdepend --jdepend-chart=builds/pdepend.svg --overview-pyramid=builds/pyramid.svg src/
