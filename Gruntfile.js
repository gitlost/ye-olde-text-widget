module.exports = function( grunt ) { //The wrapper function

	require( 'load-grunt-tasks' )( grunt );

	// Project configuration & task configuration
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		wp_readme_to_markdown: {
			convert:{
				files: {
					'README.md': 'readme.txt'
				},
				options: {
					'screenshot_url': 'https://github.com/gitlost/{plugin}/raw/master/assets/{screenshot}.png', //'https://ps.w.org/{plugin}/assets/{screenshot}.png'
				}
			}
		},

		compress: {
			main: {
				options: {
					archive: 'dist/<%= pkg.name %>-<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [
					{
						src: [
							'../ye-olde-text-widget/readme.txt',
							'../ye-olde-text-widget/ye-olde-text-widget.php',
							'../ye-olde-text-widget/includes/class-yotw-widget-text.php'
						]
					}
				]
			}
		},

	} );

	// Default task(s), executed when you run 'grunt'
	grunt.registerTask( 'default', [ 'wp_readme_to_markdown', 'compress' ] );
};
