
module.exports = (grunt) ->
    grunt.initConfig
        shell:
            phpunit:
                command: 'php -dopen_basedir= vendor/bin/phpunit'
                options: {stdout: true}

            apigen:
                command: 'php -dopen_basedir= vendor/bin/apigen.php'
                options: {stdout: true}

            pdepend:
                command: 'php -dopen_basedir= vendor/bin/pdepend --jdepend-chart=builds/pdepend.svg --overview-pyramid=builds/pyramid.svg src/'
                options: {stdout: true}

        watch:
            php:
                tasks: 'shell:phpunit'
                files: [
                    'src/**/*.php'
                    'tests/**/*.php'
                ]

    require('load-grunt-tasks')(grunt)

    grunt.registerTask 'default', ['watch']
    grunt.registerTask 'all', ['shell']
