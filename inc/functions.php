<?php

// If this file is called directly, busted!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpsabox_check_if_show' ) ) {
	function wpsabox_check_if_show() {
		$options = get_option( 'saboxplugin_options' );

		if ( isset( $options['sab_visibility'] ) ) {
			$custom_post_types = $options['sab_visibility'];
		} else {
			$custom_post_types = Simple_Author_Box_Helper::get_custom_post_type();
		}

		foreach ( $custom_post_types as $custom_post_type => $label ) {

			switch ( $custom_post_type ) {
				case 'post':
					if ( is_single() || is_author() || is_archive() ) {
						return true;
					}
					break;
				case 'page':
					if ( is_page() ) {
						return true;
					}
					break;
				default:
					if ( is_singular( $custom_post_type ) || is_post_type_archive( $custom_post_type ) ) {
						return true;
					}
					break;
			}
		}

	}
}

/*----------------------------------------------------------------------------------------------------------
    Adding the author box to the end of your single post
-----------------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'wpsabox_author_box' ) ) {


	function wpsabox_author_box( $saboxmeta = null ) {

		if ( wpsabox_check_if_show() ) {

			ob_start();
			Simple_Author_Box_Helper::get_template();
			$saboxmeta .= ob_get_clean();

		}
		return $saboxmeta;
	}
}
