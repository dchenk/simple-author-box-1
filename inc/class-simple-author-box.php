<?php

/**
*
*/
class Simple_Author_Box {

	private static $instance = null;
	private $options;

	function __construct() {

		$this->options = get_option( 'saboxplugin_options', array() );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		Simple_Author_Box_Helper::get_custom_post_type();

	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function load_dependencies() {

		require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box-helper.php';
		require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/functions.php';

		if ( is_admin() ) {
			require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box-admin-page.php';
			require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box-guest-authors.php';
		}
	}

	private function set_locale() {
		load_plugin_textdomain( 'saboxplugin', false, SIMPLE_AUTHOR_BOX_PATH . 'lang/' );
	}

	private function define_admin_hooks() {

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style_and_scripts' ) );
		add_filter( 'user_contactmethods', array( $this, 'add_extra_fields' ) );
		add_filter( 'plugin_action_links_' . SIMPLE_AUTHOR_BOX_SLUG, array( $this, 'settings_link' ) );

		// Custom Profile Image
		add_action( 'show_user_profile', array( $this, 'add_profile_image' ), 9, 1 );
		add_action( 'edit_user_profile', array( $this, 'add_profile_image' ), 9, 1 );

		add_action( 'personal_options_update', array( $this, 'save_profile_image' ), 9, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_image' ), 9, 1 );

		// Allow HTML in user description.
		remove_filter( 'pre_user_description', 'wp_filter_kses' );
		add_filter( 'pre_user_description', 'wp_kses_post' );

	}

	private function define_public_hooks() {

		if ( ! isset( $this->options['sab_autoinsert'] ) ) {
			add_filter( 'the_content', 'wpsabox_author_box' );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'saboxplugin_author_box_style' ) );

		if ( isset( $this->options['sab_footer_inline_style'] ) ) {
			add_action(
				'wp_footer', array(
					$this,
					'inline_style',
				), 13
			);
		} else {
			add_action( 'wp_head', array( $this, 'inline_style' ), 15 );
		}

		add_shortcode( 'simple-author-box', array( $this, 'shortcode' ) );

	}

	public function settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=simple-author-box-options' ) . '">' . __( 'Settings', 'saboxplugin' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function admin_style_and_scripts( $hook ) {

		if ( 'toplevel_page_simple-author-box-options' == $hook ) {

			// Styles
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-ui', SIMPLE_AUTHOR_BOX_ASSETS . 'css/jquery-ui.min.css' );
			wp_enqueue_style( 'saboxplugin-admin-style', SIMPLE_AUTHOR_BOX_ASSETS . 'css/sabox-admin-style.min.css' );

			// Scripts
			wp_enqueue_script( 'sabox-admin-js', SIMPLE_AUTHOR_BOX_ASSETS . 'js/sabox-admin.js', array( 'jquery-ui-slider', 'wp-color-picker' ), false, true );

		} elseif ( 'profile.php' == $hook || 'user-edit.php' == $hook ) {

			wp_enqueue_style( 'saboxplugin-admin-style', SIMPLE_AUTHOR_BOX_ASSETS . 'css/dev/sabox-admin-style.css' );

			wp_enqueue_media();
			wp_enqueue_editor();
			wp_enqueue_script( 'sabox-admin-editor-js', SIMPLE_AUTHOR_BOX_ASSETS . 'js/sabox-editor.js', array(), false, true );
			$sabox_js_helper = array();
			$social_icons    = apply_filters( 'sabox_social_icons', Simple_Author_Box_Helper::$social_icons );
			unset( $social_icons['user_email'] );
			$sabox_js_helper['socialIcons'] = $social_icons;

			wp_localize_script( 'sabox-admin-editor-js', 'SABHerlper', $sabox_js_helper );

		}

	}

	public function add_extra_fields( $extra_fields ) {

		unset( $extra_fields['aim'] );
		unset( $extra_fields['jabber'] );
		unset( $extra_fields['yim'] );

		return $extra_fields;

	}

	/*----------------------------------------------------------------------------------------------------------
		Adding the author box main CSS
	-----------------------------------------------------------------------------------------------------------*/
	public function saboxplugin_author_box_style() {

		if ( ! is_single() and ! is_page() and ! is_author() and ! is_archive() ) {
			return;
		}

		$sab_protocol   = is_ssl() ? 'https' : 'http';
		$sab_box_subset = get_option( 'sab_box_subset' );
		if ( 'none' != $sab_box_subset ) {
			$sab_subset = '&amp;subset=' . $sab_box_subset;
		} else {
			$sab_subset = '&amp;subset=latin';
		}

		$sab_author_font = get_option( 'sab_box_name_font' );
		$sab_desc_font   = get_option( 'sab_box_desc_font' );
		$sab_web_font    = get_option( 'sab_box_web_font' );

		$google_fonts = array();

		if ( $sab_author_font ) {
			$google_fonts[] = str_replace( ' ', '+', esc_attr( $sab_author_font ) ) . ':400,700,400italic,700italic';
		}

		if ( $sab_desc_font ) {
			$google_fonts[] = str_replace( ' ', '+', esc_attr( $sab_desc_font ) ) . ':400,700,400italic,700italic';
		}

		if ( isset( $this->options['sab_web'] ) && $sab_web_font ) {
			$google_fonts[] = str_replace( ' ', '+', esc_attr( $sab_web_font ) ) . ':400,700,400italic,700italic';
		}

		if ( ! empty( $google_fonts ) ) {
			wp_enqueue_style( 'sab-font', $sab_protocol . '://fonts.googleapis.com/css?family=' . implode( '|', $google_fonts ) . $sab_subset, array(), null );
		}

		if ( ! isset( $this->options['sab_load_fa'] ) ) {
			wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		}

		wp_enqueue_style( 'sab-plugin', SIMPLE_AUTHOR_BOX_ASSETS . 'css/dev/simple-author-box.css', false, SIMPLE_AUTHOR_BOX_VERSION );

	}

	public function inline_style() {

		if ( ! is_single() and ! is_page() and ! is_author() and ! is_archive() ) {
			return;
		}

		if ( get_option( 'sab_box_margin_top' ) ) {
			$sabox_top_margin = get_option( 'sab_box_margin_top' );
		} else {
			$sabox_top_margin = 0;
		}

		if ( get_option( 'sab_box_margin_bottom' ) ) {
			$sabox_bottom_margin = get_option( 'sab_box_margin_bottom' );
		} else {
			$sabox_bottom_margin = 0;
		}

		if ( get_option( 'sab_box_name_size' ) ) {
			$sabox_name_size = get_option( 'sab_box_name_size' );
		} else {
			$sabox_name_size = 18;
		}

		if ( isset( $this->options['sab_web'] ) and get_option( 'sab_box_web_size' ) ) {
			$sabox_web_size = get_option( 'sab_box_web_size' );
		} else {
			$sabox_web_size = 14;
		}

		if ( get_option( 'sab_box_desc_size' ) ) {
			$sabox_desc_size = get_option( 'sab_box_desc_size' );
		} else {
			$sabox_desc_size = 14;
		}

		if ( get_option( 'sab_box_icon_size' ) ) {
			$sabox_icon_size = get_option( 'sab_box_icon_size' );
		} else {
			$sabox_icon_size = 14;
		}

		$style = '<style type="text/css">';

		// Border color of Simple Author Box
		if ( isset( $this->options['sab_box_border'] ) && ! empty( $this->options['sab_box_border'] ) ) {
			$style .= '.saboxplugin-wrap {border-color:' . esc_html( $this->options['sab_box_border'] ) . ';}';
			$style .= '.saboxplugin-wrap .saboxplugin-socials {-webkit-box-shadow: 0 0.05em 0 0 ' . esc_html( $this->options['sab_box_border'] ) . ' inset; -moz-box-shadow:0 0.05em 0 0 ' . esc_html( $this->options['sab_box_border'] ) . ' inset;box-shadow:0 0.05em 0 0 ' . esc_html( $this->options['sab_box_border'] ) . ' inset;}';
		}
		// Avatar image style
		if ( isset( $this->options['sab_avatar_style'] ) && '0' != $this->options['sab_avatar_style'] ) {
			$style .= '.saboxplugin-wrap .saboxplugin-gravatar img {-webkit-border-radius:50%;-moz-border-radius:50%;-ms-border-radius:50%;-o-border-radius:50%;border-radius:50%;}';
		}
		// Social icons style
		if ( isset( $this->options['sab_colored'] ) && '0' != $this->options['sab_colored'] && isset( $this->options['sab_icons_style'] ) && '0' != $this->options['sab_icons_style'] ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color {-webkit-border-radius:50%;-moz-border-radius:50%;-ms-border-radius:50%;-o-border-radius:50%;border-radius:50%;}';
		}
		// Long Shadow
		if ( isset( $this->options['sab_colored'] ) && '0' != $this->options['sab_colored'] && ! isset( $this->options['sab_box_long_shadow'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color:before {text-shadow: none;}';
		}
		// Avatar hover effect
		if ( isset( $this->options['sab_avatar_style'] ) && '0' != $this->options['sab_avatar_style'] && isset( $this->options['sab_avatar_hover'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-gravatar img {-webkit-transition:all .5s ease;-moz-transition:all .5s ease;-o-transition:all .5s ease;transition:all .5s ease;}';
			$style .= '.saboxplugin-wrap .saboxplugin-gravatar img:hover {-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-o-transform:rotate(45deg);-ms-transform:rotate(45deg);transform:rotate(45deg);}';
		}
		// Social icons hover effect
		if ( isset( $this->options['sab_icons_style'] ) && '0' != $this->options['sab_icons_style'] && isset( $this->options['sab_social_hover'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color, .saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey {-webkit-transition: all 0.3s ease-in-out;-moz-transition: all 0.3s ease-in-out;-o-transition: all 0.3s ease-in-out;-ms-transition: all 0.3s ease-in-out;transition: all 0.3s ease-in-out;}.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color:hover,.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey:hover {-webkit-transform: rotate(360deg);-moz-transform: rotate(360deg);-o-transform: rotate(360deg);-ms-transform: rotate(360deg);transform: rotate(360deg);}';
		}
		// Thin border
		if ( isset( $this->options['sab_colored'] ) && '0' != $this->options['sab_colored'] && ! isset( $this->options['sab_box_thin_border'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color {border: medium none !important;}';
		}
		// Background color of social icons bar
		if ( isset( $this->options['sab_box_icons_back'] ) && ! empty( $this->options['sab_box_icons_back'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials{background-color:' . esc_html( $this->options['sab_box_icons_back'] ) . ';}';
		}
		// Color of social icons (for symbols only):
		if ( isset( $this->options['sab_box_icons_color'] ) && ! empty( $this->options['sab_box_icons_color'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey {color:' . esc_html( $this->options['sab_box_icons_color'] ) . ';}';
		}
		// Author name color
		if ( isset( $this->options['sab_box_author_color'] ) && ! empty( $this->options['sab_box_author_color'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-authorname a {color:' . esc_html( $this->options['sab_box_author_color'] ) . ';}';
		}

		// Author web color
		if ( isset( $this->options['sab_web'] ) && isset( $this->options['sab_box_web_color'] ) && ! empty( $this->options['sab_box_web_color'] ) ) {
			$style .= '.saboxplugin-wrap .saboxplugin-web a {color:' . esc_html( $this->options['sab_box_web_color'] ) . ';}';
		}

		// Author name font family
		if ( get_option( 'sab_box_name_font' ) != 'none' ) {
			$author_name_font = get_option( 'sab_box_name_font' );
			$style           .= '.saboxplugin-wrap .saboxplugin-authorname {font-family:"' . esc_html( $author_name_font ) . '";}';
		}

		// Author description font family
		if ( get_option( 'sab_box_desc_font' ) != 'none' ) {
			$author_desc_font = get_option( 'sab_box_desc_font' );
			$style           .= '.saboxplugin-wrap .saboxplugin-desc {font-family:' . esc_html( $author_desc_font ) . ';}';
		}

		// Author web font family
		if ( isset( $this->options['sab_web'] ) && get_option( 'sab_box_web_font' ) != 'none' ) {
			$author_web_font = get_option( 'sab_box_web_font' );
			$style          .= '.saboxplugin-wrap .saboxplugin-web {font-family:"' . esc_html( $author_web_font ) . '";}';
		}

		// Author description font style
		if ( isset( $this->options['sab_desc_style'] ) && '0' != $this->options['sab_desc_style'] ) {
			$style .= '.saboxplugin-wrap .saboxplugin-desc {font-style:italic;}';
		}
		// Margin top
		$style .= '.saboxplugin-wrap {margin-top:' . absint( $sabox_top_margin ) . 'px;}';
		// Margin bottom
		$style .= '.saboxplugin-wrap {margin-bottom:' . absint( $sabox_bottom_margin ) . 'px;}';
		// Author name text size
		$style .= '.saboxplugin-wrap .saboxplugin-authorname {font-size:' . absint( $sabox_name_size ) . 'px; line-height:' . absint( $sabox_name_size + 7 ) . 'px;}';
		// Author description font size
		$style .= '.saboxplugin-wrap .saboxplugin-desc {font-size:' . absint( $sabox_desc_size ) . 'px; line-height:' . absint( $sabox_desc_size + 7 ) . 'px;}';
		// Author website text size
		$style .= '.saboxplugin-wrap .saboxplugin-web {font-size:' . absint( $sabox_web_size ) . 'px;}';
		// Icons size
		$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color {font-size:' . absint( $sabox_icon_size + 3 ) . 'px;}';
		$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-color:before {width:' . absint( $sabox_icon_size + $sabox_icon_size ) . 'px; height:' . absint( $sabox_icon_size + $sabox_icon_size ) . 'px; line-height:' . absint( $sabox_icon_size + $sabox_icon_size + 1 ) . 'px; }';
		$style .= '.saboxplugin-wrap .saboxplugin-socials .saboxplugin-icon-grey {font-size:' . absint( $sabox_icon_size ) . 'px;}';
		$style .= '</style>';

		echo $style;
	}

	public function shortcode( $atts ) {
		$html = wpsabox_author_box();
		return $html;
	}

	public function add_profile_image( $user ) {

		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$default_url = SIMPLE_AUTHOR_BOX_ASSETS . 'img/default.png';
		$image_url   = get_user_meta( $user->ID, 'sabox-profile-image', true );

		?>

		<div id="sabox-custom-profile-image">
			<h3><?php _e( 'Custom User Profile Image', 'saboxplugin' ); ?></h3>
			<table class="form-table">
				<tr>
					<th><label for="cupp_meta"><?php _e( 'Profile Image', 'saboxplugin' ); ?></label></th>
					<td>
						<div id="sab-current-image">
							<?php wp_nonce_field( 'sabox-profile-image', 'sabox-profile-nonce' ); ?>
							<input type="hidden" name="sabox-custom-image" id="sabox-custom-image" value="<?php echo esc_attr( $image_url ); ?>">
							<img data-default="<?php echo esc_url_raw( $default_url ); ?>" src="<?php echo '' != $image_url ? esc_url_raw( $image_url ) : esc_url_raw( $default_url ); ?>">
						</div>
						<div class="actions">
							<a href="#" class="button-secondary" id="sabox-remove-image"><?php _e( 'Remove Image', 'saboxplugin' ); ?></a>
							<a href="#" class="button-primary" id="sabox-add-image"><?php _e( 'Upload Image', 'saboxplugin' ); ?></a>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<?php
	}

	public function save_profile_image( $user_id ) {

		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST['sabox-profile-nonce'] ) || ! wp_verify_nonce( $_POST['sabox-profile-nonce'], 'sabox-profile-image' ) ) {
			return;
		}

		if ( isset( $_POST['sabox-custom-image'] ) && '' != $_POST['sabox-custom-image'] ) {
			update_user_meta( $user_id, 'sabox-profile-image', esc_url_raw( $_POST['sabox-custom-image'] ) );
		} else {
			delete_user_meta( $user_id, 'sabox-profile-image' );
		}

	}

}
