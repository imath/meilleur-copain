/* jshint node:true */
/* global module */
module.exports = function( grunt ) {
	require( 'matchdep' ).filterDev( ['grunt-*', '!grunt-legacy-util'] ).forEach( grunt.loadNpmTasks );
	grunt.util = require( 'grunt-legacy-util' );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		jshint: {
			options: grunt.file.readJSON( '.jshintrc' ),
			grunt: {
				src: ['Gruntfile.js']
			},
			all: ['Gruntfile.js', 'src/customize/index.js']
		},
		checktextdomain: {
			options: {
				correct_domain: false,
				text_domain: ['meilleur-copain'],
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'_n:1,2,4d',
					'_ex:1,2c,3d',
					'_nx:1,2,4c,5d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: ['**/*.php', '!**/node_modules/**'],
				expand: true
			}
		},
		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: ['/node_modules'],
					mainFile: 'meilleur-copain.php',
					potFilename: 'meilleur-copain.pot',
					processPot: function( pot ) {
						pot.headers['last-translator']      = 'imath <contact@imathi.eu>';
						pot.headers['language-team']        = 'FRENCH <contact@imathi.eu>';
						pot.headers['report-msgid-bugs-to'] = 'https://github.com/imath/meilleur-copain/issues';
						return pot;
					},
					type: 'wp-plugin'
				}
			}
		},
		jsvalidate:{
			src: ['src/customize/index.js'],
			options:{
				globals: {},
				esprimaOptions:{},
				verbose: false
			}
		},
		exec: {
			build_parcel: {
				command: 'npm run build',
				stdout: true,
				stderr: true
			}
		}
	} );

    grunt.registerTask( 'jstest', ['jsvalidate', 'jshint'] );

    grunt.registerTask( 'translate', ['checktextdomain', 'makepot'] );

	// Default task.
	grunt.registerTask( 'default', ['checktextdomain'] );
};
