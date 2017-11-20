<?php

class Simple_Author_Box_Guest_Authors {

	function __construct() {

		// Hooks
		add_action( 'admin_menu', array( $this, 'register_guest_submenu_items' ) );
		add_action( 'admin_menu', array( $this, 'disable_add_guest_author_menu' ), 99 );
		add_action( 'init', array( $this, 'add_guest_role' ) );
		add_action( 'admin_init', array( $this, 'creat_edit_guests' ) );
		add_action( 'wp_ajax_sabox_create_user', array( $this, 'create_user' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style_and_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'author_meta_box' ) );

		add_action( 'save_post', array( $this, 'save_coauthors' ), 10, 2 );

		add_action( 'show_user_profile', array( $this, 'add_social_area' ) );
		add_action( 'edit_user_profile', array( $this, 'add_social_area' ) );

		add_action( 'personal_options_update', array( $this, 'save_social_links' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_social_links' ) );

		add_action( 'admin_footer-post.php', array( $this, 'display_guest_author_popup' ) );
		add_action( 'admin_footer-post-new.php', array( $this, 'display_guest_author_popup' ) );

		add_filter( 'users_list_table_query_args', array( $this, 'remove_guest_authors' ) );
		add_filter( 'views_users', array( $this, 'remove_guest_authors_from_roles' ) );
		add_filter( 'show_password_fields', array( $this, 'remove_guest_author_passwords' ), 10, 2 );

	}

	public function register_guest_submenu_items() {
		add_submenu_page(
			'users.php',
			esc_html__( 'Guest Authors', 'saboxplugin' ),
			esc_html__( 'Guest Authors', 'saboxplugin' ),
			'manage_options',
			'sab-guest-authors',
			array( $this, 'list_guest_authors' )
		);

		add_submenu_page(
			'users.php',
			esc_html__( 'Add Guest Author', 'saboxplugin' ),
			esc_html__( 'Add Guest Author', 'saboxplugin' ),
			'manage_options',
			'sab-add-guest-author',
			array( $this, 'add_guest_author' )
		);

	}

	public function disable_add_guest_author_menu() {
		global $submenu;

		foreach ( $submenu['users.php'] as $key => $menu ) {
			if ( 'Add Guest Author' == $menu['0'] ) {
				unset( $submenu['users.php'][ $key ] );
			}
		}

	}

	public function admin_style_and_scripts( $hook ) {

		if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
			wp_enqueue_style( 'saboxplugin-selectize-style', SIMPLE_AUTHOR_BOX_ASSETS . 'css/selectize.css' );
			wp_enqueue_style( 'saboxplugin-selectize-default-style', SIMPLE_AUTHOR_BOX_ASSETS . 'css/selectize.default.css' );
			wp_enqueue_style( 'saboxplugin-popup-style', SIMPLE_AUTHOR_BOX_ASSETS . 'css/dev/sabox-popup-style.css' );

			wp_enqueue_editor();
			wp_enqueue_script( 'sabox-selectize-js', SIMPLE_AUTHOR_BOX_ASSETS . 'js/selectize.min.js', array(), false, true );
			wp_enqueue_script( 'sabox-selectize-script-js', SIMPLE_AUTHOR_BOX_ASSETS . 'js/sabox-selectize.js', array(), false, true );

			$sabox_js_helper = array();
			$social_icons    = apply_filters( 'sabox_social_icons', Simple_Author_Box_Helper::$social_icons );
			unset( $social_icons['user_email'] );
			$sabox_js_helper['socialIcons'] = $social_icons;
			$sabox_js_helper['ajaxurl']     = admin_url( 'admin-ajax.php' );

			wp_localize_script( 'sabox-selectize-script-js', 'SABHerlper', $sabox_js_helper );
		}

	}

	public function list_guest_authors() {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		if ( ! class_exists( 'WP_Users_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php' );
		}
		require_once SIMPLE_AUTHOR_BOX_PATH . 'inc/class-simple-author-box-list-table.php';
		require SIMPLE_AUTHOR_BOX_PATH . 'inc/admin/list-guest-authors.php';
	}

	public function add_guest_author() {
		require SIMPLE_AUTHOR_BOX_PATH . 'inc/admin/add-guest-author.php';
	}

	public function edit_guest_author() {
		require_once( ABSPATH . 'wp-admin/user-edit.php' );
	}

	public function add_guest_role() {
		add_role(
			'sab-guest-author', __( 'Guest Author', 'saboxplugin' ), array(
				'read'         => true,  // true allows this capability
				'edit_posts'   => true,
				'delete_posts' => false, // Use false to explicitly deny
			)
		);
	}

	public function remove_guest_authors( $args ) {

		$args['role__not_in'] = 'sab-guest-author';
		return $args;

	}

	public function remove_guest_authors_from_roles( $views ) {

		if ( isset( $views['sab-guest-author'] ) ) {
			unset( $views['sab-guest-author'] );
		}
		return $views;

	}

	public function remove_guest_author_passwords( $return, $userprofile ) {

		if ( isset( $userprofile->roles ) && is_array( $userprofile->roles ) && in_array( 'sab-guest-author', $userprofile->roles ) ) {
			return false;
		}

		return true;
	}

	public function creat_edit_guests() {

		if ( isset( $_REQUEST['action'] ) && 'addguestauthor' == $_REQUEST['action'] ) {
			check_admin_referer( 'add-guest-author', '_wpnonce_add-guest-author' );

			global $wpdb;

			if ( ! current_user_can( 'create_users' ) ) {
				wp_die(
					'<h1>' . __( 'Cheatin&#8217; uh?', 'saboxplugin' ) . '</h1>' .
					'<p>' . __( 'Sorry, you are not allowed to create users.', 'saboxplugin' ) . '</p>',
					403
				);
			}

			if ( ! is_multisite() ) {
				$user_id = edit_user();
				if ( is_wp_error( $user_id ) ) {

					$add_user_errors = $user_id;

				} else {

					wp_redirect(
						add_query_arg(
							array(
								'page'    => 'sab-edit-guest-author',
								'user_id' => $user_id,
								'update'  => 'add',
							), 'users.php'
						)
					);
					die();
				}
			} else {
				// Adding a new user to this site
				$new_user_email = wp_unslash( $_REQUEST['email'] );
				$user_details   = wpmu_validate_user_signup( $_REQUEST['user_login'], $new_user_email );
				if ( is_wp_error( $user_details['errors'] ) && ! empty( $user_details['errors']->errors ) ) {
					$add_user_errors = $user_details['errors'];
				} else {
					$new_user_login = apply_filters( 'pre_user_login', sanitize_user( wp_unslash( $_REQUEST['user_login'] ), true ) );

					add_filter( 'wpmu_signup_user_notification', '__return_false' ); // Disable confirmation email
					add_filter( 'wpmu_welcome_user_notification', '__return_false' ); // Disable welcome email

					wpmu_signup_user(
						$new_user_login, $new_user_email, array(
							'add_to_blog' => $wpdb->blogid,
							'new_role'    => $_REQUEST['role'],
						)
					);
					$key      = $wpdb->get_var( $wpdb->prepare( "SELECT activation_key FROM {$wpdb->signups} WHERE user_login = %s AND user_email = %s", $new_user_login, $new_user_email ) );
					$new_user = wpmu_activate_signup( $key );

					if ( is_wp_error( $new_user ) ) {
						$redirect = add_query_arg(
							array(
								'page'   => 'sab-add-guest-author',
								'update' => 'addnoconfirmation',
							), 'users.php'
						);
					} else {
						$redirect = add_query_arg(
							array(
								'page'    => 'sab-edit-guest-author',
								'update'  => 'addnoconfirmation',
								'user_id' => $new_user['user_id'],
							), 'users.php'
						);
					}
					wp_redirect( $redirect );
					die();
				}
			}
		}

	}

	public function add_social_area( $profileuser ) {
		$user_id = $profileuser->data->ID;

		$social_links = Simple_Author_Box_Helper::get_user_social_links( $user_id );
		$social_icons = apply_filters( 'sabox_social_icons', Simple_Author_Box_Helper::$social_icons );
		unset( $social_icons['user_email'] );

		?>

		<h2><?php _e( 'Social Media Links', 'saboxplugin' ); ?></h2>
		<table class="form-table" id="sabox-social-table">
			<?php

			if ( ! empty( $social_links ) ) {
				foreach ( $social_links as $social_platform => $social_link ) {
					?>
					<tr>
						<th>
							<select name="sabox-social-icons[]">
								<?php foreach ( $social_icons as $sabox_social_id => $sabox_social_name ) { ?>
									<option value="<?php echo $sabox_social_id; ?>" <?php selected( $sabox_social_id, $social_platform ); ?>><?php echo $sabox_social_name; ?></option>
								<?php } ?>
							</select>
						</th>
						<td>
							<input name="sabox-social-links[]" type="text" class="regular-text" value="<?php echo esc_url( $social_link ); ?>">
							<span class="dashicons dashicons-no"></span>
						<td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<th>
						<select name="sabox-social-icons[]">
							<?php foreach ( $social_icons as $sabox_social_id => $sabox_social_name ) { ?>
								<option value="<?php echo $sabox_social_id; ?>"><?php echo $sabox_social_name; ?></option>
							<?php } ?>
						</select>
					</th>
					<td>
						<input name="sabox-social-links[]" type="text" class="regular-text" value="">
						<span class="dashicons dashicons-no"></span>
					<td>
				</tr>
				<?php
			}

			?>

		</table>

		<div class="sabox-add-social-link">
			<span class="dashicons dashicons-plus"></span>
			<span><?php esc_html_e( 'Add new social platform', 'saboxplugin' ); ?></span>
		</div>

		<?php
	}

	public function save_social_links( $user_id ) {

		if ( isset( $_POST['sabox-social-icons'] ) && isset( $_POST['sabox-social-links'] ) ) {

			$social_platforms = apply_filters( 'sabox_social_icons', Simple_Author_Box_Helper::$social_icons );
			$social_links     = array();
			foreach ( $_POST['sabox-social-links'] as $index => $social_link ) {
				if ( $social_link ) {
					$social_platform = isset( $_POST['sabox-social-icons'][ $index ] ) ? $_POST['sabox-social-icons'][ $index ] : false;
					if ( $social_platform && isset( $social_platforms[ $social_platform ] ) ) {
						$social_links[ $social_platform ] = esc_url_raw( $social_link );
					}
				}
			}

			update_user_meta( $user_id, 'sabox_social_links', $social_links );

		}

	}

	public function author_meta_box() {

		global $post_type, $post_type_object;

		if ( post_type_supports( $post_type, 'author' ) && current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			remove_meta_box( 'authordiv', 'post', 'normal' );
			add_meta_box( 'authordiv', __( 'Author', 'saboxplugin' ), array( $this, 'display_author_meta_box' ), 'post', 'normal', 'high' );
		}

	}

	public function display_author_meta_box( $post ) {
		global $user_ID;
		?>
		<p><strong><?php _e( 'Author', 'saboxplugin' ); ?></strong></p>
		<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author', 'saboxplugin' ); ?></label>
		<?php
		wp_dropdown_users(
			array(
				'who'              => 'authors',
				'name'             => 'post_author_override',
				'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
				'include_selected' => true,
				'show'             => 'display_name_with_login',
			)
		);
		?>
		<p><strong><?php _e( 'Guest Authors', 'saboxplugin' ); ?></strong></p>
		<div id="sab-coauthors">
			<?php
			wp_nonce_field( 'sabox-add-co-authors', 'sabox-co-authors-nonce' );

			$coauthors = get_post_meta( $post->ID, 'sabox-coauthors', true );
			if ( ! empty( $coauthors ) ) {
				foreach ( $coauthors as $coauthor_id ) {
					$coauthor = get_userdata( $coauthor_id );
					echo '<div class="sab-co-author"><input type="hidden" name="sabox-coauthors[]" value="' . $coauthor_id . '"><span>' . $coauthor->user_login . '</span><span class="dashicons dashicons-no"></span></div>';
				}
			}

			?>
		</div>
		<div class="sabox-coauthors-container">
		<?php
		wp_dropdown_users(
			array(
				'name'     => '',
				'id'       => 'sabox-co-authors',
				'show'     => 'user_login',
				'selected' => false,
			)
		);
		?>
		</div>
		<?php
	}

	public function display_guest_author_popup() {
		require SIMPLE_AUTHOR_BOX_PATH . 'inc/admin/add-guest-popup.php';
	}

	public function create_user() {

		if ( ! isset( $_POST['sabox-nonce'] ) || ! wp_verify_nonce( $_POST['sabox-nonce'], 'sabox-create-ajax-user' ) ) {

			echo json_encode(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Sorry, your nonce did not verify.', 'saboxplugin' ),
				)
			);
			die();

		}

		if ( ! current_user_can( 'create_users' ) ) {
			echo json_encode(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Sorry, you are not allowed to create users.', 'saboxplugin' ),
				)
			);
			die();
		}

		if ( ! isset( $_POST['sabox-user']['username'] ) || username_exists( $_POST['sabox-user']['username'] ) ) {
			echo json_encode(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Sorry, this username is used.', 'saboxplugin' ),
				)
			);
			die();
		}

		if ( ! isset( $_POST['sabox-user']['email'] ) || email_exists( $_POST['sabox-user']['email'] ) ) {
			echo json_encode(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Sorry, this email is used.', 'saboxplugin' ),
				)
			);
			die();
		}

		$userdata               = array();
		$userdata['user_pass']  = wp_generate_password( 12, false );
		$userdata['user_login'] = sanitize_text_field( $_POST['sabox-user']['username'] );
		$userdata['user_email'] = sanitize_email( $_POST['sabox-user']['email'] );
		$userdata['role']       = 'sab-guest-author';

		if ( isset( $_POST['sabox-user']['firstname'] ) ) {
			$userdata['first_name'] = sanitize_text_field( $_POST['sabox-user']['firstname'] );
		}

		if ( isset( $_POST['sabox-user']['lastname'] ) ) {
			$userdata['last_name'] = sanitize_text_field( $_POST['sabox-user']['lastname'] );
		}

		if ( isset( $_POST['sabox-user']['description'] ) ) {
			$userdata['description'] = wp_kses_post( $_POST['sabox-user']['description'] );
		}

		if ( isset( $_POST['sabox-user']['website'] ) ) {
			$userdata['user_url'] = esc_url_raw( $_POST['sabox-user']['website'] );
		}

		$user_id = wp_insert_user( $userdata );

		$social_links = array();
		if ( isset( $_POST['sabox-user']['social-platform'] ) && is_array( $_POST['sabox-user']['social-platform'] ) ) {
			foreach ( $_POST['sabox-user']['social-platform'] as $index => $platform ) {
				if ( isset( $_POST['sabox-user']['social-links'][ $index ] ) && '' != $_POST['sabox-user']['social-links'][ $index ] ) {
					$social_links[ $platform ] = esc_url_raw( $_POST['sabox-user']['social-links'][ $index ] );
				}
			}
		}

		if ( ! empty( $social_links ) ) {
			add_user_meta( $user_id, 'sabox_social_links', $social_links );
		}

		echo json_encode(
			array(
				'status'    => 'ok',
				'user_id'   => $user_id,
				'user_name' => $userdata['user_login'],
			)
		);
		die();
	}

	public function save_coauthors( $post_id, $post ) {

		if ( ! isset( $_POST['sabox-co-authors-nonce'] ) || ! wp_verify_nonce( $_POST['sabox-co-authors-nonce'], 'sabox-add-co-authors' ) ) {
			return;
		}

		$post_type = get_post_type_object( $post->post_type );

		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['sabox-coauthors'] ) && is_array( $_POST['sabox-coauthors'] ) ) {
			$coauthors = array();
			foreach ( $_POST['sabox-coauthors'] as $coauthor ) {
				$coauthors[] = absint( $coauthor );
			}

			update_post_meta( $post_id, 'sabox-coauthors', $coauthors );

		} else {
			delete_post_meta( $post_id, 'sabox-coauthors' );
		}

	}

}

new Simple_Author_Box_Guest_Authors();
