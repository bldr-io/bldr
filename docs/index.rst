Welcome to Bldr's documentation!
===================================

Bldr, in the simplest terms, is a build system, written with yaml configs. If you are used to build systems,
you've probably seen some pretty complicated build files, and they were probably written in xml.

Well, here's one written for Bldr:

```yml
name: bldr-io/bldr
description: 'Build System with YAML in mind'

profiles:
    default:
        description: 'Development Profile'
        tasks:
            - prepare
            - lint
            - phpcs
            - test

tasks:
    prepare:
        description: 'Cleans up old builds and prepares the new one'
        calls:
            -
                type: filesystem:remove
                arguments: [build/coverage, build/logs]
            -
                type: filesystem:mkdir
                arguments: [build/coverage, build/logs]
            -
                type: filesystem:touch
                arguments: [build/coverage/index.html]
            -
                type: exec
                arguments:
                    - composer
                    - --prefer-dist
                    - install
    lint:
        describe: 'Lints the files of the project'
        calls:
            -
                type: apply
                fileset: src/**/*.php
                arguments:
                    - /usr/bin/php
                    - -l

    phpcs:
        description: 'Runs the PHP Code Sniffer'
        calls:
            -
                type: exec
                arguments:
                    - /usr/bin/php
                    - bin/phpcs
                    - -p
                    - --standard=build/phpcs.xml
                    - --report=checkstyle
                    - --report-file=build/logs/checkstyle.xml
                    - src/
    test:
        description: 'Runs the PHPUnit Tests'
        calls:
            -
                type: exec
                failOnError: true
                arguments:
                    - /usr/bin/php
                    - bin/phpunit
                    - --testdox
                    - --coverage-text

```

For now (while im still working on the documentation), this will hopefully serve as ample documentation.

Contents:

.. toctree::
   :maxdepth: 2
