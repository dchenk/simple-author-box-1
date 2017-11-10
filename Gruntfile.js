'use strict';
module.exports = function( grunt ) {

  // load all tasks
  require( 'load-grunt-tasks' )( grunt, { scope: 'devDependencies' } );

  grunt.config.init( {
    pkg: grunt.file.readJSON( 'package.json' ),

    dirs: {
      css: '/assets/css',
      js: '/assets/js'
    },

    clean: {
      css: [ 'assets/css/*.min.css', '!assets/css/jquery-ui.min.css' ]
    },
    checktextdomain: {
      standard: {
        options: {
          text_domain: [ 'saboxplugin' ], //Specify allowed domain(s)
          create_report_file: 'true',
          keywords: [ //List keyword specifications
            '__:1,2d',
            '_e:1,2d',
            '_x:1,2c,3d',
            'esc_html__:1,2d',
            'esc_html_e:1,2d',
            'esc_html_x:1,2c,3d',
            'esc_attr__:1,2d',
            'esc_attr_e:1,2d',
            'esc_attr_x:1,2c,3d',
            '_ex:1,2c,3d',
            '_n:1,2,4d',
            '_nx:1,2,4c,5d',
            '_n_noop:1,2,3d',
            '_nx_noop:1,2,3c,4d'
          ]
        },
        files: [
          {
            src: [
              '**/*.php',
              '!**/node_modules/**',
            ], //all php
            expand: true
          } ]
      }
    },
    cssmin: {
      target: {
        files: [
          {
            expand: true,
            cwd: 'assets/css/dev',
            src: [ '*.css', '!*.min.css' ],
            dest: 'assets/css',
            ext: '.min.css'
          } ]
      }
    }
  } );

  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-contrib-cssmin' );

  grunt.registerTask( 'default', [] );
  grunt.registerTask( 'checktextdomain' ); // Check Missing Text Domain Strings
  grunt.registerTask( 'mincss', [  // Minify CSS
    'clean:css',
    'cssmin'
  ] );
};