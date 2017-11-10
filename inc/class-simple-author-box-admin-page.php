<?php

class Simple_Author_Box_Admin_Page {

	private $tab;
	private $options;
	private $sections;
	private $views_path = SIMPLE_AUTHOR_BOX_PATH . 'inc/admin/';

	function __construct() {
		$this->tab        = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general-options';
		$default_sections = array(
			'general-options'       => array(
				'label' => __( 'General', 'saboxplugin' ),
			),
			'color-options'         => array(
				'label' => __( 'Color', 'saboxplugin' ),
			),
			'typography-options'    => array(
				'label' => __( 'Typography', 'saboxplugin' ),
			),
			'miscellaneous-options' => array(
				'label' => __( 'Miscellaneous', 'saboxplugin' ),
			),
		);

		$settings = array(
			'general-options'       => array(
				'sab_autoinsert'        => array(
					'label' => __( 'Manually insert the Simple Author Box:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'plugin_sshortcode'     => array(
					'label'     => __( 'If you want to manually insert the Simple Author Box in your template file (single post view), you can use the following code snippet:', 'saboxplugin' ),
					'type'      => 'readonly',
					'value'     => '&lt;?php if ( function_exists( \'wpsabox_author_box\' ) ) echo wpsabox_author_box(); ?&gt;',
					'condition' => 'sab_autoinsert',
				),
				'sab_box_margin_top'    => array(
					'label'   => __( 'Top margin of author box:', 'saboxplugin' ),
					'type'    => 'slider',
					'choices' => array(
						'min'       => 0,
						'max'       => 100,
						'increment' => 1,
					),
					'default' => '0',
				),
				'sab_box_margin_bottom' => array(
					'label'   => __( 'Bottom margin of author box:', 'saboxplugin' ),
					'type'    => 'slider',
					'choices' => array(
						'min'       => 0,
						'max'       => 100,
						'increment' => 1,
					),
					'default' => '0',
				),
				'sab_no_description'    => array(
					'label' => __( 'Hide the author box if author description is empty:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_avatar_style'      => array(
					'label' => __( 'Author avatar image style:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_avatar_hover'      => array(
					'label' => __( 'Rotate effect on author avatar hover:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_author_link'       => array(
					'label'   => __( 'Author name should link to:', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => array(
						'author-page'    => __( 'Author\'s Page', 'saboxplugin' ),
						'author-website' => __( 'Author\'s Website', 'saboxplugin' ),
						'none'           => __( 'None', 'saboxplugin' ),
					),
					'default' => 'author-page',
					'group'   => 'saboxplugin_options',
				),
				'sab_web'               => array(
					'label' => __( 'Show author website:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_web_position'      => array(
					'label'     => __( 'Author website position:', 'saboxplugin' ),
					'type'      => 'select',
					'choices'   => array(
						0 => __( 'Left', 'saboxplugin' ),
						1 => __( 'Right', 'saboxplugin' ),
					),
					'default'   => '0',
					'condition' => 'sab_web',
					'group'     => 'saboxplugin_options',
				),
				'sab_web_target'        => array(
					'label'     => __( 'Open author website link in a new tab:', 'saboxplugin' ),
					'type'      => 'toggle',
					'condition' => 'sab_web',
					'group'     => 'saboxplugin_options',
				),
				'sab_web_rel'           => array(
					'label'     => __( 'Add "nofollow" attribute on author website link:', 'saboxplugin' ),
					'type'      => 'toggle',
					'condition' => 'sab_web',
					'group'     => 'saboxplugin_options',
				),
				'sab_email'             => array(
					'label' => __( 'Show author email:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_colored'           => array(
					'label'   => __( 'Social icons type (colored background or symbols only):', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => array(
						0 => __( 'Symbols', 'saboxplugin' ),
						1 => __( 'Colored', 'saboxplugin' ),
					),
					'default' => '0',
					'group'   => 'saboxplugin_options',
				),
				'sab_icons_style'       => array(
					'label'     => __( 'Social icons style:', 'saboxplugin' ),
					'type'      => 'select',
					'choices'   => array(
						0 => __( 'Squares', 'saboxplugin' ),
						1 => __( 'Circle', 'saboxplugin' ),
					),
					'default'   => '0',
					'condition' => 'sab_colored',
					'group'     => 'saboxplugin_options',
				),
				'sab_social_hover'      => array(
					'label'     => __( 'Rotate effect on social icons hover:', 'saboxplugin' ),
					'type'      => 'toggle',
					'condition' => 'sab_colored',
					'group'     => 'saboxplugin_options',
				),
				'sab_box_long_shadow'   => array(
					'label'     => __( 'Use flat long shadow effect:', 'saboxplugin' ),
					'type'      => 'toggle',
					'condition' => 'sab_colored',
					'group'     => 'saboxplugin_options',
				),
				'sab_box_thin_border'   => array(
					'label'     => __( 'Show a thin border on colored social icons:', 'saboxplugin' ),
					'type'      => 'toggle',
					'condition' => 'sab_colored',
					'group'     => 'saboxplugin_options',
				),
				'sab_link_target'       => array(
					'label' => __( 'Open social icon links in a new tab:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_hide_socials'      => array(
					'label' => __( 'Hide the social icons on author box:', 'saboxplugin' ),
					'type'  => 'toggle',
					'group' => 'saboxplugin_options',
				),
				'sab_visibility'        => array(
					'label'   => __( 'Show author box on:', 'saboxplugin' ),
					'type'    => 'multiplecheckbox',
					'choices' => Simple_Author_Box_Helper::get_custom_post_type(),
					'default' => array_keys( Simple_Author_Box_Helper::get_custom_post_type() ),
					'group'   => 'saboxplugin_options',
				),
			),
			'color-options'         => array(
				'sab_box_author_color' => array(
					'label' => __( 'Author name color:', 'saboxplugin' ),
					'type'  => 'color',
					'group' => 'saboxplugin_options',
				),
				'sab_box_web_color'    => array(
					'label' => __( 'Author website link color:', 'saboxplugin' ),
					'type'  => 'color',
					'group' => 'saboxplugin_options',
				),
				'sab_box_border'       => array(
					'label' => __( 'Border color of Simple Author Box:', 'saboxplugin' ),
					'type'  => 'color',
					'group' => 'saboxplugin_options',
				),
				'sab_box_icons_back'   => array(
					'label' => __( 'Background color of social icons bar:', 'saboxplugin' ),
					'type'  => 'color',
					'group' => 'saboxplugin_options',
				),
				'sab_box_icons_color'  => array(
					'label' => __( 'Social icons color (for symbols only):', 'saboxplugin' ),
					'type'  => 'color',
					'group' => 'saboxplugin_options',
				),
			),
			'typography-options'    => array(
				'sab_box_subset'    => array(
					'label'       => __( 'Google font characters subset:', 'saboxplugin' ),
					'description' => __( 'Please note that some Google fonts does not support particular subsets!', 'saboxplugin' ),
					'type'        => 'select',
					'choices'     => Simple_Author_Box_Helper::get_google_font_subsets(),
					'default'     => 'none',
				),
				'sab_box_name_font' => array(
					'label'   => __( 'Author name font family:', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => Simple_Author_Box_Helper::get_google_fonts(),
					'default' => 'None',
				),
				'sab_box_web_font'  => array(
					'label'   => __( 'Author website font family:', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => Simple_Author_Box_Helper::get_google_fonts(),
					'default' => 'None',
				),
				'sab_box_desc_font' => array(
					'label'   => __( 'Author description font family:', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => Simple_Author_Box_Helper::get_google_fonts(),
					'default' => 'None',
				),
				'sab_box_name_size' => array(
					'label'       => __( 'Author name font size:', 'saboxplugin' ),
					'description' => __( 'Default font size of author name is 18px.', 'saboxplugin' ),
					'type'        => 'slider',
					'choices'     => array(
						'min'       => 10,
						'max'       => 50,
						'increment' => 1,
					),
					'default'     => '18',
				),
				'sab_box_web_size'  => array(
					'label'       => __( 'Author website font size:', 'saboxplugin' ),
					'description' => __( 'Default font size of author website is 14px.', 'saboxplugin' ),
					'type'        => 'slider',
					'choices'     => array(
						'min'       => 10,
						'max'       => 50,
						'increment' => 1,
					),
					'default'     => '14',
				),
				'sab_box_desc_size' => array(
					'label'       => __( 'Author description font size:', 'saboxplugin' ),
					'description' => __( 'Default font size of author description is 14px.', 'saboxplugin' ),
					'type'        => 'slider',
					'choices'     => array(
						'min'       => 10,
						'max'       => 50,
						'increment' => 1,
					),
					'default'     => '14',
				),
				'sab_box_icon_size' => array(
					'label'       => __( 'Size of social icons:', 'saboxplugin' ),
					'description' => __( 'Default font size of social icons is 18px.', 'saboxplugin' ),
					'type'        => 'slider',
					'choices'     => array(
						'min'       => 10,
						'max'       => 50,
						'increment' => 1,
					),
					'default'     => '18',
				),
				'sab_desc_style'    => array(
					'label'   => __( 'Author description font style:', 'saboxplugin' ),
					'type'    => 'select',
					'choices' => array(
						0 => __( 'Normal', 'saboxplugin' ),
						1 => __( 'Italic', 'saboxplugin' ),
					),
					'default' => '0',
					'group'   => 'saboxplugin_options',
				),
			),
			'miscellaneous-options' => array(
				'sab_load_fa'             => array(
					'label'       => __( 'Disable Font Awesome stylesheet:', 'saboxplugin' ),
					'description' => __( 'Switch to "Yes" to prevent Font Awesome from loading its stylesheet, ONLY if your theme or another plugin already does.', 'saboxplugin' ),
					'type'        => 'toggle',
					'group'       => 'saboxplugin_options',
				),
				'sab_footer_inline_style' => array(
					'label'       => __( 'Load generated inline style to footer:', 'saboxplugin' ),
					'description' => __( 'This option is useful ONLY if you run a plugin that optimizes your CSS delivery or moves your stylesheets to the footer, to get a higher score on speed testing services. However, the plugin style is loaded only on single post and single page.', 'saboxplugin' ),
					'type'        => 'toggle',
					'group'       => 'saboxplugin_options',
				),
			),
		);

		$this->settings = apply_filters( 'sabox_admin_settings', $settings );
		$this->sections = apply_filters( 'sabox_admin_tabs', $default_sections );

		$this->get_all_options();

		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	private function get_all_options() {

		$this->options = get_option( 'saboxplugin_options', array() );

		$sab_box_margin_top = get_option( 'sab_box_margin_top' );
		if ( $sab_box_margin_top ) {
			$this->options['sab_box_margin_top'] = $sab_box_margin_top;
		}
		$sab_box_margin_bottom = get_option( 'sab_box_margin_bottom' );
		if ( $sab_box_margin_bottom ) {
			$this->options['sab_box_margin_bottom'] = $sab_box_margin_bottom;
		}
		$sab_box_icon_size = get_option( 'sab_box_icon_size' );
		if ( $sab_box_icon_size ) {
			$this->options['sab_box_icon_size'] = $sab_box_icon_size;
		}
		$sab_box_web_size = get_option( 'sab_box_web_size' );
		if ( $sab_box_web_size ) {
			$this->options['sab_box_web_size'] = $sab_box_web_size;
		}
		$sab_box_name_font = get_option( 'sab_box_name_font' );
		if ( $sab_box_name_font ) {
			$this->options['sab_box_name_font'] = $sab_box_name_font;
		}
		$sab_box_subset = get_option( 'sab_box_subset' );
		if ( $sab_box_subset ) {
			$this->options['sab_box_subset'] = $sab_box_subset;
		}
		$sab_box_desc_font = get_option( 'sab_box_desc_font' );
		if ( $sab_box_desc_font ) {
			$this->options['sab_box_desc_font'] = $sab_box_desc_font;
		}
		$sab_box_web_font = get_option( 'sab_box_web_font' );
		if ( $sab_box_web_font ) {
			$this->options['sab_box_web_font'] = $sab_box_web_font;
		}
		$sab_box_desc_size = get_option( 'sab_box_desc_size' );
		if ( $sab_box_desc_size ) {
			$this->options['sab_box_desc_size'] = $sab_box_desc_size;
		}

	}

	public function menu_page() {
		add_menu_page( __( 'Simple Author Box', 'saboxplugin' ), __( 'Simple Author', 'saboxplugin' ), 'manage_options', 'simple-author-box-options', array( $this, 'setting_page' ), 'dashicons-id' );
	}

	public function setting_page() {
		?>

		<div class="wrap about-wrap epsilon-wrap">
			<h1>
				<?php
				/* Translators: Welcome Screen Title. */
				echo esc_html__( 'Welcome to Simple Author Box', 'saboxplugin' );
				?>
			</h1>
			<div class="about-text">
				<?php
				/* Translators: Welcome Screen Description. */
				echo esc_html__( 'Simple Author Box is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using %1$s and that is why we gathered here all the necessary information for you. We hope you will enjoy using %1$s, as much as we enjoy creating great products.', 'saboxplugin' );
				?>
			</div>
			<div class="wp-badge epsilon-welcome-logo"></div>

			<h2 class="nav-tab-wrapper wp-clearfix">
				<?php foreach ( $this->sections as $id => $section ) { ?>
					<a class="nav-tab <?php echo $id === $this->tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $this->generate_admin_url( $id ) ); ?>"><?php echo wp_kses_post( $section['label'] ); ?></a>
				<?php } ?>
			</h2>
			<form method="post" id="sabox-cotnainer">
			<?php

			wp_nonce_field( 'sabox-plugin-settings', 'sabox_plugin_settings_page' );
			echo '<input type="hidden" name="sabox-setting-tab" value="' . $this->tab . '">';

			if ( isset( $this->sections[ $this->tab ]['path'] ) ) {
				require_once $this->sections[ $this->tab ]['path'];
			} else {
				echo '<table class="form-table sabox-table">';
				foreach ( $this->settings[ $this->tab ] as $field_name => $field ) {
					$this->generate_setting_field( $field_name, $field );
				}
				echo '</table>';
				submit_button();
			}

			?>
			</form>
		</div>

		<?php
	}

	public function save_settings() {

		if ( isset( $_POST['sabox_plugin_settings_page'] ) && wp_verify_nonce( $_POST['sabox_plugin_settings_page'], 'sabox-plugin-settings' ) ) {
			$tab      = $_POST['sabox-setting-tab'];
			$settings = $_POST['sabox-settings'];
			$groups   = array();

			foreach ( $this->settings[ $tab ] as $key => $setting ) {
				if ( isset( $setting['group'] ) ) {

					if ( ! isset( $groups[ $setting['group'] ] ) ) {
						$groups[ $setting['group'] ] = get_option( $setting['group'], array() );
					}

					if ( ! isset( $settings[ $setting['group'] ][ $key ] ) && isset( $groups[ $setting['group'] ][ $key ] ) ) {
						unset( $groups[ $setting['group'] ][ $key ] );
					}

					if ( isset( $settings[ $setting['group'] ][ $key ] ) ) {
						$groups[ $setting['group'] ][ $key ] = $this->saniteze_fields( $setting, $settings[ $setting['group'] ][ $key ] );
					}
				} else {

					$current_value = get_option( $key );
					if ( isset( $settings[ $key ] ) ) {
						$value = $this->saniteze_fields( $setting, $settings[ $key ] );
						if ( $current_value != $value ) {
							update_option( $key, $value );
						}
					}
				}
			}

			foreach ( $groups as $key => $values ) {
				update_option( $key, $values );
			}

			do_action( 'sabox_save_settings' );

			$this->get_all_options();

		}

	}

	private function saniteze_fields( $setting, $value ) {
		$default_sanitizers = array(
			'toggle' => 'absint',
			'slider' => 'absint',
			'color'  => 'sanitize_hex_color',
		);

		if ( isset( $setting['sanitize'] ) && function_exists( $setting['sanitize'] ) ) {
			$value = call_user_func( $setting['sanitize'], $value );
		} elseif ( isset( $default_sanitizers[ $setting['type'] ] ) && function_exists( $default_sanitizers[ $setting['type'] ] ) ) {
			$value = call_user_func( $default_sanitizers[ $setting['type'] ], $value );
		} elseif ( 'select' == $setting['type'] ) {
			if ( isset( $setting['choices'][ $value ] ) ) {
				$value = $value;
			} else {
				$value = $setting['default'];
			}
		} elseif ( 'multiplecheckbox' == $setting['type'] ) {
			foreach ( $value as $key ) {
				if ( ! isset( $setting['choices'][ $key ] ) ) {
					unset( $value[ $key ] );
				}
			}
		} else {
			$value = sanitize_text_field( $value );
		}

		return $value;

	}

	private function generate_admin_url( $id ) {
		$url = 'admin.php?page=simple-author-box-options&tab=%1$s';

		return admin_url( sprintf( $url, $id ) );
	}

	private function generate_admin_path( $id ) {
		return $this->views_path . $id . '.php';
	}

	private function generate_setting_field( $field_name, $field ) {
		$class = '';
		$name  = 'sabox-settings[';
		if ( isset( $field['group'] ) ) {
			$name .= $field['group'] . '][' . esc_attr( $field_name ) . ']';
		} else {
			$name .= esc_attr( $field_name ) . ']';
		}
		if ( isset( $field['condition'] ) ) {
			$class = 'show_if_' . $field['condition'] . ' hide';
		}
		echo '<tr valign="top" class="' . esc_attr( $class ) . '">';
		echo '<th scope="row">';
		echo esc_html( $field['label'] );
		if ( isset( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}
		echo '</th>';
		echo '<td>';
		switch ( $field['type'] ) {
			case 'toggle':
				echo '<div class="checkbox_switch">';
					echo '<div class="onoffswitch">';
						echo '<input type="checkbox" id="' . esc_attr( $field_name ) . '" name="' . esc_attr( $name ) . '" class="onoffswitch-checkbox saboxfield" ' . checked( 1, isset( $this->options[ $field_name ] ), false ) . ' value="1">';
						echo '<label class="onoffswitch-label" for="' . esc_attr( $field_name ) . '"></label>';
					echo '</div>';
				echo '</div>';
				break;
			case 'select':
				$value = isset( $this->options[ $field_name ] ) ? $this->options[ $field_name ] : $field['default'];
				echo '<select id="' . esc_attr( $field_name ) . '" name="' . esc_attr( $name ) . '" class="saboxfield">';
				foreach ( $field['choices'] as $key => $choice ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $value, false ) . '>' . esc_html( $choice ) . '</option>';
				}
				echo '</select>';
				break;
			case 'readonly':
				echo '<textarea clas="regular-text" rows="3" cols="50" onclick="this.focus();this.select();" readonly="readonly">' . esc_attr( $field['value'] ) . '</textarea>';
				break;
			case 'slider':
				$value = isset( $this->options[ $field_name ] ) ? $this->options[ $field_name ] : $field['default'];
				echo '<div class="sabox-slider-container">';
				echo '<div class="sabox-slider"><div class="custom-handle ui-slider-handle"></div></div>';
				echo '<input id="' . esc_attr( $field_name ) . '" class="saboxfield" name="' . esc_attr( $name ) . '" data-min="' . absint( $field['choices']['min'] ) . '" data-max="' . absint( $field['choices']['max'] ) . '" data-step="' . absint( $field['choices']['increment'] ) . '" value="' . esc_attr( $value ) . '">';
				echo '</div>';
				break;
			case 'color':
				$value = isset( $this->options[ $field_name ] ) ? $this->options[ $field_name ] : '';
				echo '<div class="sadbox-colorpicker">';
				echo '<input id="' . esc_attr( $field_name ) . '" class="saboxfield sabox-color" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
				echo '</div>';
				break;
			case 'multiplecheckbox':
				$values = isset( $this->options[ $field_name ] ) ? $this->options[ $field_name ] : $field['default'];
				echo '<div class="sabox-multicheckbox">';
				foreach ( $field['choices'] as $key => $choice ) {
					echo '<div>';
					echo '<input type="checkbox" value="' . $key . '" ' . checked( 1, in_array( $key, $values ), false ) . ' name="' . esc_attr( $name ) . '[]"><span>' . $choice . '</span>';
					echo '</div>';
				}
				echo '</div>';
				break;
			default:
				do_action( "sabox_field_{$field['type']}_output", $field_name, $field );
				break;
		}
		echo '</td>';
		echo '</tr>';
	}

}

new Simple_Author_Box_Admin_Page();
