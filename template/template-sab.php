<?php
global $post;
$options = get_option( 'saboxplugin_options' );

$author_id = $post->post_author;

if ( isset( $options['sab_colored'] ) ) {
	$sabox_color = 'sabox-colored';
} else {
	$sabox_color = '';
}

if ( isset( $options['sab_web_position'] ) && '0' != $options['sab_web_position'] ) {
	$sab_web_align = 'sab-web-position';
} else {
	$sab_web_align = '';
}

if ( isset( $options['sab_web_target'] ) ) {
	$sab_web_target = '_blank';
} else {
	$sab_web_target = '_self';
}

if ( isset( $options['sab_web_rel'] ) ) {
	$sab_web_rel = 'rel="nofollow"';
} else {
	$sab_web_rel = '';
}

if ( isset( $options['sab_author_link'] ) ) {
	if ( 'author-page' == $options['sab_author_link'] ) {
		$sab_author_link = sprintf( '<a href="%s">%s</a>', get_author_posts_url( $author_id ), get_the_author_meta( 'display_name', $author_id ) );
	} elseif ( 'author-website' == $options['sab_author_link'] ) {
		$sab_author_link = sprintf( '<a href="%s">%s</a>', get_the_author_meta( 'user_url', $author_id ), get_the_author_meta( 'display_name', $author_id ) );
	} else {
		$sab_author_link = sprintf( '<span>%s</span>', get_the_author_meta( 'display_name', $author_id ) );
	}
} else {
	$sab_author_link = sprintf( '<a href="%s">%s</a>', get_author_posts_url( $author_id ), get_the_author_meta( 'display_name', $author_id ) );
}

if ( get_the_author_meta( 'description' ) != '' || ! isset( $options['sab_no_description'] ) ) { // hide the author box if no description is provided

	echo '<div class="saboxplugin-wrap">'; // start saboxplugin-wrap div

	// author box gravatar
	echo '<div class="saboxplugin-gravatar">';
	echo get_avatar( get_the_author_meta( 'user_email', $author_id ), '100' );
	echo '</div>';

	// author box name
	echo '<div class="saboxplugin-authorname">';
	echo $sab_author_link;
	echo '</div>';


	// author box description
	echo '<div class="saboxplugin-desc">';
	echo '<div class="vcard author"><span class="fn">';
	echo  get_the_author_meta( 'description', $author_id );
	echo '</span></div>';
	echo '</div>';

	if ( is_single() ) {
		if ( get_the_author_meta( 'user_url' ) != '' and isset( $options['sab_web'] ) ) { // author website on single
			echo '<div class="saboxplugin-web ' . $sab_web_align . '">';
			echo '<a href="' . get_the_author_meta( 'user_url', $author_id ) . '" target="' . $sab_web_target . '" ' . $sab_web_rel . '>' . get_the_author_meta( 'user_url', $author_id ) . '</a>';
			echo '</div>';
		}
	}


	if ( is_author() or is_archive() ) {
		if ( get_the_author_meta( 'user_url' ) != '' ) { // force show author website on author.php or archive.php
			echo '<div class="saboxplugin-web ' . $sab_web_align . '">';
			echo '<a href="' . get_the_author_meta( 'user_url', $author_id ) . '" target="' . $sab_web_target . '" ' . $sab_web_rel . '>' . get_the_author_meta( 'user_url', $author_id ) . '</a>';
			echo '</div>';
		}
	}



	// author box clearfix
	echo '<div class="clearfix"></div>';

	// author box social icons
	if ( ! isset( $options['sab_hide_socials'] ) ) { // hide social icons div option
		echo '<div class="saboxplugin-socials ' . $sabox_color . '">';
		$social_icons = apply_filters( 'sabox_social_icons', Simple_Author_Box_Helper::$social_icons );
		foreach ( $social_icons as $sabox_social_id => $sabox_social_name ) {

						$sabox_icon_url = get_the_author_meta( $sabox_social_id );

			if ( 'user_email' == $sabox_social_id ) {
				if ( ! isset( $options['sab_email'] ) ) {
					continue;
				} else {
					$sabox_icon_url = 'mailto:' . antispambot( $sabox_icon_url );
				}
			}

			if ( ! empty( $sabox_icon_url ) ) {
				echo Simple_Author_Box_Helper::get_sabox_social_icon( $sabox_icon_url, $sabox_social_id );
			}
		}

		echo '</div>';
	} // end of social icons
	echo '</div>'; // end of saboxplugin-wrap div
}
