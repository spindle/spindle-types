module.exports = (grunt) ->
    grunt.initConfig
        shell:
            phpunit:
                command: 'vendor/bin/phpunit'
                options: {stdout: true, stderr: true}

            apigen:
                command: 'vendor/bin/apigen.php'
                options: {stdout: true, stderr: true}

            pdepend:
                command: [
                    'vendor/bin/pdepend'
                    '--jdepend-chart=builds/pdepend.svg'
                    '--overview-pyramid=builds/pyramid.svg'
                    '--summary-xml=builds/summary.xml'
                    'src/'
                ].join ' '
                options: {stdout: true, stderr: true}

        connect:
            server:
                options:
                    port: 9000
                    base: 'builds'
                    livereload: true

        watch:
            php:
                tasks: 'shell:phpunit'
                files: [
                    'src/**/*.php'
                    'tests/**/*.php'
                ]
                options:
                    livereload: true

    require('load-grunt-tasks')(grunt)

    grunt.registerTask 'default', ['connect', 'watch']
    grunt.registerTask 'test', ['shell:phpunit']
    grunt.registerTask 'all', ['shell']
