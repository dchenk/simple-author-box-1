<?php

class Simple_Author_Box_List_Table extends WP_Users_List_Table {

	public function prepare_items() {
		global $role, $usersearch;
		$usersearch     = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$per_page       = ( $this->is_site_users ) ? 'site_users_network_per_page' : 'users_per_page';
		$users_per_page = $this->get_items_per_page( $per_page );
		$paged          = $this->get_pagenum();

		$args = array(
			'number' => $users_per_page,
			'offset' => ( $paged - 1 ) * $users_per_page,
			'role'   => 'sab-guest-author',
			'search' => $usersearch,
			'fields' => 'all_with_meta',
		);

		if ( '' !== $args['search'] ) {
			$args['search'] = '*' . $args['search'] . '*';
		}
		if ( $this->is_site_users ) {
			$args['blog_id'] = $this->site_id;
		}
		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}
		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );
		$this->items    = $wp_user_search->get_results();
		$this->set_pagination_args(
			array(
				'total_items' => $wp_user_search->get_total(),
				'per_page'    => $users_per_page,
			)
		);
	}

	public function get_columns() {
		$c = array(
			'cb'       => '<input type="checkbox" />',
			'username' => __( 'Username', 'saboxplugin' ),
			'name'     => __( 'Name', 'saboxplugin' ),
			'email'    => __( 'Email', 'saboxplugin' ),
			'posts'    => __( 'Posts', 'saboxplugin' ),
		);
		if ( $this->is_site_users ) {
			unset( $c['posts'] );
		}
		return $c;
	}

	public function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		if ( ! ( $user_object instanceof WP_User ) ) {
			$user_object = get_userdata( (int) $user_object );
		}
		$user_object->filter = 'display';
		$email               = $user_object->user_email;

		$user_roles = $this->get_role_list( $user_object );
		// Set up the hover actions for this user
		$actions     = array();
		$checkbox    = '';
		$super_admin = '';
		if ( is_multisite() && current_user_can( 'manage_network_users' ) ) {
			if ( in_array( $user_object->user_login, get_super_admins(), true ) ) {
				$super_admin = ' &mdash; ' . __( 'Super Admin', 'saboxplugin' );
			}
		}
		// Check if the user for this row is editable
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link
			$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_object->ID ) ) );
			if ( current_user_can( 'edit_user', $user_object->ID ) ) {
				$edit            = "<strong><a href=\"{$edit_link}\">{$user_object->user_login}</a>{$super_admin}</strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit', 'saboxplugin' ) . '</a>';
			} else {
				$edit = "<strong>{$user_object->user_login}{$super_admin}</strong><br />";
			}
			if ( ! is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) ) {
				$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?page=sab-guest-authors&amp;action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Delete', 'saboxplugin' ) . '</a>';
			}
			if ( is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'remove_user', $user_object->ID ) ) {
				$actions['remove'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( 'Remove', 'saboxplugin' ) . '</a>';
			}
			/**
			 * Filters the action links displayed under each user in the Users list table.
			 *
			 * @since 2.8.0
			 *
			 * @param array   $actions     An array of action links to be displayed.
			 *                             Default 'Edit', 'Delete' for single site, and
			 *                             'Edit', 'Remove' for Multisite.
			 * @param WP_User $user_object WP_User object for the currently-listed user.
			 */
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );
			// Role classes.
			$role_classes = esc_attr( implode( ' ', array_keys( $user_roles ) ) );
			// Set up the checkbox ( because the user is editable, otherwise it's empty )
			$checkbox = '<label class="screen-reader-text" for="user_' . $user_object->ID . '">' . sprintf( __( 'Select %s', 'saboxplugin' ), $user_object->user_login ) . '</label>'
						. "<input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='{$role_classes}' value='{$user_object->ID}' />";
		} else {
			$edit = "<strong>{$user_object->user_login}{$super_admin}</strong>";
		}
		$avatar = get_avatar( $user_object->ID, 32 );
		// Comma-separated list of user roles.
		$roles_list = implode( ', ', $user_roles );
		$r          = "<tr id='user-$user_object->ID'>";
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}
			if ( 'posts' === $column_name ) {
				$classes .= ' num'; // Special case for that column
			}
			if ( in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}
			$data       = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';
			$attributes = "class='$classes' $data";
			if ( 'cb' === $column_name ) {
				$r .= "<th scope='row' class='check-column'>$checkbox</th>";
			} else {
				$r .= "<td $attributes>";
				switch ( $column_name ) {
					case 'username':
						$r .= "$avatar $edit";
						break;
					case 'name':
						if ( $user_object->first_name && $user_object->last_name ) {
							$r .= "$user_object->first_name $user_object->last_name";
						} else {
							$r .= '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . _x( 'Unknown', 'name', 'saboxplugin' ) . '</span>';
						}
						break;
					case 'email':
						$r .= "<a href='" . esc_url( "mailto:$email" ) . "'>$email</a>";
						break;
					case 'role':
						$r .= esc_html( $roles_list );
						break;
					case 'posts':
						if ( $numposts > 0 ) {
							$r .= "<a href='edit.php?author=$user_object->ID' class='edit'>";
							$r .= '<span aria-hidden="true">' . $numposts . '</span>';
							$r .= '<span class="screen-reader-text">' . sprintf( _n( '%s post by this author', '%s posts by this author', $numposts, 'saboxplugin' ), number_format_i18n( $numposts ) ) . '</span>';
							$r .= '</a>';
						} else {
							$r .= 0;
						}
						break;
					default:
						/**
						 * Filters the display output of custom columns in the Users list table.
						 *
						 * @since 2.8.0
						 *
						 * @param string $output      Custom column output. Default empty.
						 * @param string $column_name Column name.
						 * @param int    $user_id     ID of the currently-listed user.
						 */
						$r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
				}
				if ( $primary === $column_name ) {
					$r .= $this->row_actions( $actions );
				}
				$r .= '</td>';
			}
		}
		$r .= '</tr>';
		return $r;
	}

}
