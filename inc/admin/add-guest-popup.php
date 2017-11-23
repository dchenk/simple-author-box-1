<div class="sabox-popup-guest-author" id="sabox-add-user" style="display:none;">
	<div class="sabox-popup-container">

		<div class="sabox-popup-header">
			<h2><?php esc_html_e( 'Create Guest Author', 'saboxplugin' ); ?></h2>
			<div class="close-popup">
				<span class="dashicons dashicons-no"></span>
			</div>
		</div>

		<form>
			<div class="sabox-popup-body">
				<div class="error-notice"></div>
				<div class="personal-info">
					<h3><?php esc_html_e( 'Personal Information', 'saboxplugin' ); ?></h3>
					<div class="input-container">
						<div class="input-col">
							<label for="sabox-usernam"><?php esc_html_e( 'Username', 'saboxplugin' ); ?></label>
							<input type="text" name="sabox-user[username]" class="sabox-input" value="" id="sabox-username">
						</div>
						<div class="input-col">
							<label for="sabox-usernam"><?php esc_html_e( 'First Name', 'saboxplugin' ); ?></label>
							<input type="text" name="sabox-user[firstname]" class="sabox-input" value="" id="sabox-firstname">
						</div>
						<div class="input-col">
							<label for="sabox-usernam"><?php esc_html_e( 'Last Name', 'saboxplugin' ); ?></label>
							<input type="text" name="sabox-user[lastname]" class="sabox-input" value="" id="sabox-lastname">
						</div>
					</div>
				</div>
				<div class="contact-info">
					<h3><?php esc_html_e( 'Contact Information', 'saboxplugin' ); ?></h3>
					<div class="input-container">
						<div class="input-col">
							<label for="sabox-usernam"><?php esc_html_e( 'Email', 'saboxplugin' ); ?></label>
							<input type="text" name="sabox-user[email]" class="sabox-input" value="" id="sabox-email">
						</div>
						<div class="input-col">
							<label for="sabox-usernam"><?php esc_html_e( 'Website', 'saboxplugin' ); ?></label>
							<input type="text" name="sabox-user[website]" class="sabox-input" value="" id="sabox-website">
						</div>
					</div>
				</div>
				<div class="biographical-info">
					<h3><?php esc_html_e( 'Biographical Info', 'saboxplugin' ); ?></h3>
					<div class="input-container">
						<div class="input-col">
							<textarea type="text" name="sabox-user[description]" value="" id="sabox-description"></textarea>
						</div>
					</div>
				</div>

				<?php do_action( 'sabox_guest_author_popup_fields' ); ?>

			</div>
			<div class="sabox-popup-foter">
				<?php wp_nonce_field( 'sabox-create-ajax-user', 'sabox-nonce' ); ?>
				<div class="sabox-button-container">
					<div class="spinner"></div>
					<?php submit_button( __( 'Add Guest Author', 'saboxplugin' ), 'secondary', 'saboxcreateguestauthor', true, array( 'id' => 'sabox-submit' ) ); ?>
				</div>
			</div>
		</form>
	</div>
</div>
