module.exports = function ( grunt ) {
    grunt.initConfig( {
        shell: {
            tests: {
                command: [
                     'php bin/phpunit',
                     'box build -v'
                 ].join( '&&' ),
                options: {
                    stdout: true
                }
            }
        },
        watch: {
            tests: {
                files: ['**/*.php', '**/*.yml', 'bin/bldr'],
                tasks: ['shell:tests']
            }
        }
    } );

    // plugins
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-shell' );

    // tasks
    grunt.registerTask( 'default', ['shell:tests', 'watch:tests'] );
};
