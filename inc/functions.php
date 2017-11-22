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
			$custom_post_types = array_keys( Simple_Author_Box_Helper::get_custom_post_type() );
		}

		foreach ( $custom_post_types as $custom_post_type ) {

			switch ( $custom_post_type ) {
				case 'post':
					if ( is_singular( 'post' ) || is_author() || is_archive() ) {
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

		return false;

	}
}

/*----------------------------------------------------------------------------------------------------------
    Adding the author box to the end of your single post
-----------------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'wpsabox_author_box' ) ) {


	function wpsabox_author_box( $saboxmeta = null ) {

		if ( wpsabox_check_if_show() ) {

			global $post;
			$template = Simple_Author_Box_Helper::get_template();

			ob_start();
			$sabox_options      = get_option( 'saboxplugin_options' );
			$co_authors         = get_post_meta( $post->ID, 'sabox-coauthors', true );
			$sabox_guest_author = false;

			if ( ! empty( $co_authors ) ) {
				if ( isset( $sabox_options['co_authors'] ) ) {
					$sabox_author_id = $post->post_author;
					include( $template );
				}
			} else {
				$sabox_author_id = $post->post_author;
				include( $template );
			}

			if ( ! empty( $co_authors ) ) {
				if ( isset( $sabox_options['co_authors'] ) ) {
					echo '<h2 class="sabox-guest-authors">' . esc_html__( 'Co Authors :', 'saboxplugin' ) . '</h2>';
				} else {
					$sabox_guest_author = true;
				}

				foreach ( $co_authors as $co_author ) {
					$sabox_author_id = $co_author;
					include( $template );
				}
			}

			$saboxmeta .= ob_get_clean();

		}
		return $saboxmeta;
	}
}
