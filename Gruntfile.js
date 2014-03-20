module.exports = function ( grunt ) {
    grunt.initConfig( {
        shell: {
            tests: {
                command: [
                             'clear', 'php bin/phpunit', 'box build'
                         ].join( '&&' ),
                options: {
                    stdout: true
                }
            }
        },
        watch: {
            tests: {
                files: ['**/*.php', '**/*.yml'],
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
