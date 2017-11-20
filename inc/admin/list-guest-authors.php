<?php

$wp_list_table = new Simple_Author_Box_List_Table( array( 'screen' => 'users' ) );
$wp_list_table->prepare_items();

?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e( 'Guest Authors', 'saboxplugin' ); ?> </h1>
	<?php if ( current_user_can( 'create_users' ) ) { ?>
		<a href="<?php echo admin_url( 'users.php?page=sab-add-guest-author' ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'guest author', 'saboxplugin' ); ?></a>
	<?php } ?>

<form method="get">

<?php $wp_list_table->search_box( __( 'Search Users', 'saboxplugin' ), 'user' ); ?>

<?php $wp_list_table->display(); ?>
</form>




</div>
