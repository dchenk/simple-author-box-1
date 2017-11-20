<?php

if ( is_multisite() ) {
	if ( ! current_user_can( 'create_users' ) && ! current_user_can( 'promote_users' ) ) {
		wp_die(
			'<h1>' . __( 'Cheatin&#8217; uh?', 'saboxplugin' ) . '</h1>' .
			'<p>' . __( 'Sorry, you are not allowed to add users to this network.', 'saboxplugin' ) . '</p>',
			403
		);
	}
} elseif ( ! current_user_can( 'create_users' ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?', 'saboxplugin' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to create users.', 'saboxplugin' ) . '</p>',
		403
	);
}

wp_enqueue_script( 'wp-ajax-response' );
wp_enqueue_script( 'user-profile' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( is_multisite() ) {
		$edit_link = '';
		if ( ( isset( $_GET['user_id'] ) ) ) {
			$user_id_new = absint( $_GET['user_id'] );
			if ( $user_id_new ) {
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_id_new ) ) );
			}
		}

		switch ( $_GET['update'] ) {
			case 'addexisting':
				$messages[] = __( 'That user is already a member of this site.', 'saboxplugin' );
				break;
			case 'enter_email':
				$messages[] = __( 'Please enter a valid email address.', 'saboxplugin' );
				break;
		}
	}
}

$creating       = isset( $_POST['createguestauthor'] );
$new_user_login = $creating && isset( $_POST['user_login'] ) ? wp_unslash( $_POST['user_login'] ) : '';
$new_user_email = $creating && isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';

?>
<div class="wrap">
	<h1 id="add-new-user"><?php _e( 'Add Guest Author', 'saboxplugin' ); ?> </h1>

<form method="post" name="adduser" id="adduser" class="validate" novalidate="novalidate" >
<input name="action" type="hidden" value="addguestauthor" />
<input name="role" type="hidden" value="sab-guest-author" />
<?php wp_nonce_field( 'add-guest-author', '_wpnonce_add-guest-author' ); ?>

<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e( 'Username', 'saboxplugin' ); ?> <span class="description"><?php _e( '(required)', 'saboxplugin' ); ?></span></label></th>
		<td><input name="user_login" type="text" id="user_login" value="<?php echo esc_attr( $new_user_login ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="email"><?php _e( 'Email', 'saboxplugin' ); ?> <span class="description"><?php _e( '(required)', 'saboxplugin' ); ?></span></label></th>
		<td><input name="email" type="email" id="email" value="<?php echo esc_attr( $new_user_email ); ?>" /></td>
	</tr>
</table>
<?php submit_button( __( 'Add Guest Author', 'saboxplugin' ), 'primary', 'createguestauthor', true, array( 'id' => 'addusersub' ) ); ?>
</form>
</div>
<?php
