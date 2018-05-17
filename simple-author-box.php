<?php
/**
 * Plugin Name: Simple Author Box
 * Plugin URI: http://wordpress.org/plugins/simple-author-box/
 * Description: Adds a responsive author box with social icons on your posts.
 * Version: 2.0.6
 * Author: Macho Themes
 * Author URI: https://www.machothemes.com/
 * License: GPLv3
 */

/*  Copyright 2018 Machothemes (email : office [at] machothemes [dot] com)

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

define( 'SIMPLE_AUTHOR_BOX_PATH', plugin_dir_path( __FILE__ ) );
define( 'SIMPLE_AUTHOR_BOX_ASSETS', plugins_url( '/assets/', __FILE__ ) );
define( 'SIMPLE_AUTHOR_BOX_SLUG', plugin_basename( __FILE__ ) );
define( 'SIMPLE_AUTHOR_BOX_VERSION', '2.0.6' );
define( 'SIMPLE_AUTHOR_SCRIPT_DEBUG', false );


require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box.php';
Simple_Author_Box::get_instance();

// load the uninstall feedback class
require_once 'inc/feedback/class-epsilon-feedback-sab.php';
new Epsilon_Feedback_SAB( __FILE__ );

add_filter( 'amp_post_template_data', 'sab_amp_css' );

/**
 * AMP compatibility
 *
 * @param $data
 *
 * @return mixed
 */

function sab_amp_css( $data ) {

	$data['post_amp_styles'] = array(
		'.saboxplugin-wrap'                                                              => array(
			'box-sizing: border-box',
			'border: 1px solid #EEE',
			'width: 100%',
			'clear: both',
			'overflow : hidden',
			'word-wrap: break-word',
			'position: relative',
		),
		'.saboxplugin-wrap .saboxplugin-gravatar'                                        => array(
			'float: left',
			'padding: 20px',
		),
		'.saboxplugin-wrap .saboxplugin-gravatar img'                                    => array(
			'max-width: 100px',
			'height: auto',
		),
		'.saboxplugin-wrap .saboxplugin-authorname'                                      => array(
			'font-size: 18px',
			'line-height: 1',
			'margin: 20px 0 0 20px',
			'display: block',
		),
		'.saboxplugin-wrap .saboxplugin-authorname a'                                    => array(
			'text-decoration: none',
		),
		'.saboxplugin-wrap .saboxplugin-desc'                                            => array(
			'display: block',
			'margin: 5px 20px',
		),
		'.saboxplugin-wrap .saboxplugin-desc a'                                          => array(
			'text-decoration: none',
		),
		'.saboxplugin-wrap .saboxplugin-desc p'                                          => array(
			'margin: 5px 0 12px 0',
		),
		'.saboxplugin-wrap .saboxplugin-web'                                             => array(
			'margin: 0 20px 15px',
			'text-align: left',
		),
		'.saboxplugin-wrap .saboxplugin-socials'                                         => array(
			'position: relative',
			'display: block',
			'background: #fcfcfc',
			'padding: 0 15px',
			'box-shadow: 0 1px 0 0 #eee inset',
			'-webkit-box-shadow: 0 1px 0 0 #eee inset',
			'-moz-box-shadow: 0 1px 0 0 #eee inset',
		),
		'.saboxplugin-wrap .saboxplugin-socials a'                                       => array(
			'text-decoration: none',
			'box-shadow: none',
			'padding: 0',
			'margin: 0',
			'border: 0',
			'transition: opacity 0.4s',
			'-webkit-transition: opacity 0.4s',
			'-moz-transition: opacity 0.4s',
			'-o-transition: opacity 0.4s',
		),
		'.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey'                  => array(
			'font-family: \'FontAwesome\'',
			'display: inline-block',
			'vertical-align: middle',
			'margin: 10px 5px',
			'color: #444',
		),
		'.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey:before'           => array(
			'display: block',
			'text-align: center',
			'line-height: 1',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-user_email:before' => array(
			'content: \'\f0e0\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-addthis:before'    => array(
			'content: \'\f067\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-behance:before'    => array(
			'content: \'\f1b4\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-delicious:before'  => array(
			'content: \'\f1a5\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-deviantart:before' => array(
			'content: \'\f1bd\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-digg:before'       => array(
			'content: \'\f1a6\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-dribbble:before'   => array(
			'content: \'\f17d\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-facebook:before'   => array(
			'content: \'\f09a\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-flickr:before'     => array(
			'content: \'\f16e\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-github:before'     => array(
			'content: \'\f09b\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-google:before'     => array(
			'content: \'\f1a0\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-googleplus:before' => array(
			'content: \'\f0d5\'',
		),
		'.saboxplugin-socials .saboxplugin-icon-grey.saboxplugin-icon-html5:before'      => array(
			'content: \'\f13b\'',
		)
	);

	$data['font_urls'] = array(
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
	);

	return $data;
}