<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="feature-section sab-support">
	<div class="row two-col center-support">
		<div class="col">
			<h3><i class="dashicons dashicons-sos" style="display: inline-block;vertical-align: middle;margin-right: 5px"></i><?php esc_html_e( 'Contact Support', 'saboxplugin' ); ?></h3>
			<p>
				<i><?php esc_html_e( 'We offer excellent support through our advanced ticketing system.', 'saboxplugin' ); ?></i>
			</p>
			<p><a target="_blank" class="button button-hero button-primary" href="<?php echo esc_url( 'https://www.machothemes.com/support/?utm_source=sab&utm_medium=about-page&utm_campaign=support-button' ); ?>"><?php esc_html_e( 'Contact Support', 'saboxplugin' ); ?></a>
			</p>
		</div><!--/.col-->
	</div>
	<div class="row">
		<h1 class="sab-title">Looking for better WP hosting ?</h1>
	</div>
	<div class="row sab-blog three-col">
		<div class="col">
			<h3><i class="dashicons dashicons-performance" style="display: inline-block;vertical-align: middle;margin-right: 5px"></i><?php esc_html_e( 'Our Bluehost Hosting Review', 'saboxplugin' ); ?></h3>
			<p>
				<i><?php esc_html_e( 'Despite its popularity, though, Bluehost often carries a negative perception among WordPress professionals. So as we dig into this Bluehost review, we\'ll be looking to figure out whether Bluehost\'s performance and features actually justify that reputation.', 'saboxplugin' ); ?></i>
			</p>
			<p><a target="_blank" href="<?php echo esc_url( 'https://www.machothemes.com/blog/bluehost-review/?utm_source=sab&utm_medium=about-page&utm_campaign=blog-links' ); ?>"><?php esc_html_e( 'Read more', 'saboxplugin' ); ?></a>
			</p>
		</div><!--/.col-->

		<div class="col">
			<h3><i class="dashicons dashicons-performance" style="display: inline-block;vertical-align: middle;margin-right: 5px"></i><?php esc_html_e( 'Our InMotion Hosting Review', 'saboxplugin' ); ?></h3>
			<p>
				<i><?php esc_html_e( 'InMotion Hosting is a popular independent web host that serves over 300,000 customers. They\'re notably not a part of the EIG behemoth (the parent company behind Bluehost, HostGator, and more), which is a plus in my book.', 'saboxplugin' ); ?></i>
			</p>
			<p>
				<a target="_blank" href="<?php echo esc_url( 'https://www.machothemes.com/blog/inmotion-hosting-review/?utm_source=sab&utm_medium=about-page&utm_campaign=blog-links' ); ?>"><?php esc_html_e( 'Read more', 'saboxplugin' ); ?></a>
			</p>
		</div><!--/.col-->

		<div class="col">
			<h3><i class="dashicons dashicons-performance" style="display: inline-block;vertical-align: middle;margin-right: 5px"></i><?php esc_html_e( 'Our A2 Hosting Review', 'saboxplugin' ); ?></h3>
			<p>
				<i><?php esc_html_e( 'When it comes to affordable WordPress hosting, A2 Hosting is a name that often comes up in various WordPress groups for offering quick-loading performance that belies its low price tag.', 'saboxplugin' ); ?></i>
			</p>
			<p>
				<a target="_blank" href="<?php echo esc_url( 'https://www.machothemes.com/blog/a2-hosting-review/?utm_source=sab&utm_medium=about-page&utm_campaign=blog-links' ); ?>"><?php esc_html_e( 'Read more', 'saboxplugin' ); ?></a>
			</p>
		</div><!--/.col-->
	</div>
</div><!--/.feature-section-->

<div class="col-fulwidth feedback-box">
	<h3>
		<?php esc_html_e( 'Lend a hand & share your thoughts', 'saboxplugin' ); ?>
		<img src="<?php echo SIMPLE_AUTHOR_BOX_ASSETS; ?>/img/handshake.png">
	</h3>
	<p>
		<?php
		echo vsprintf( // Translators: 1 is Theme Name, 2 is opening Anchor, 3 is closing.
			__( 'We\'ve been working hard on making %1$s the best one out there. We\'re interested in hearing your thoughts about %1$s and what we could do to <u>make it even better</u>.<br/> <br/> %2$sHave your say%3$s', 'saboxplugin' ), array(
			'Simple Author Box',
			'<a class="button button-feedback" target="_blank" href="http://bit.ly/feedback-simple-author-box">',
			'</a>',
		) );
		?>
	</p>
</div>