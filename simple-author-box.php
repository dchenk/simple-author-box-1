<?php
/**
 * Plugin Name: Simple Author Box
 * Plugin URI: http://wordpress.org/plugins/simple-author-box/
 * Description: Adds a responsive author box with social icons on your posts.
 * Version: 1.9
 * Author: Macho Themes
 * Author URI: https://www.machothemes.com/
 * License: GPLv2
 */

/*  Copyright 2017 Machothemes (email : office [at] machothemes [dot] com)

    THIS PROGRAM IS FREE SOFTWARE; YOU CAN REDISTRIBUTE IT AND/OR MODIFY
    IT UNDER THE TERMS OF THE GNU GENERAL PUBLIC LICENSE AS PUBLISHED BY
    THE FREE SOFTWARE FOUNDATION; EITHER VERSION 2 OF THE LICENSE, OR
    (AT YOUR OPTION) ANY LATER VERSION.

    THIS PROGRAM IS DISTRIBUTED IN THE HOPE THAT IT WILL BE USEFUL,
    BUT WITHOUT ANY WARRANTY; WITHOUT EVEN THE IMPLIED WARRANTY OF
    MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE.  SEE THE
    GNU GENERAL PUBLIC LICENSE FOR MORE DETAILS.

    YOU SHOULD HAVE RECEIVED A COPY OF THE GNU GENERAL PUBLIC LICENSE
    ALONG WITH THIS PROGRAM; IF NOT, WRITE TO THE FREE SOFTWARE
    FOUNDATION, INC., 51 FRANKLIN ST, FIFTH FLOOR, BOSTON, MA  02110-1301  USA

*/

define( 'SIMPLE_AUTHOR_BOX_LAST_UPDATE', date_i18n( 'F j, Y', '1409122800' ) );    // Defining plugin last update
define( 'SIMPLE_AUTHOR_BOX_PATH', plugin_dir_path( __FILE__ ) );                   // Defining plugin dir path
define( 'SIMPLE_AUTHOR_BOX_ASSETS', plugins_url( '/assets/', __FILE__ ) );         // Defining plugin assets url
define( 'SIMPLE_AUTHOR_BOX_SLUG', plugin_basename( __FILE__ ) );            // Defining plugin dir name
define( 'SIMPLE_AUTHOR_BOX_VERSION', 'v1.9' );                                      // Defining plugin version
define( 'SIMPLE_AUTHOR_BOX', 'Simple Author Box' );                                 // Defining plugin name
define( 'SIMPLE_AUTHOR_BOX_FOOTER', 10 );

require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box.php';

if ( class_exists( 'Simple_Author_Box' ) ) {
	Simple_Author_Box::get_instance();
}
