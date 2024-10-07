<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require 'functions/array.php';
require 'functions/conditional.php';
require 'functions/date.php';
require 'functions/entity.php';
require 'functions/filter.php';
require 'functions/format.php';
require 'functions/i18n.php';
require 'functions/misc.php';
require 'functions/plugin.php';
require 'functions/post-type.php';
require 'functions/regex.php';
require 'functions/request.php';
require 'functions/rest.php';
require 'functions/template.php';
require 'functions/time.php';
require 'functions/wordpress.php';


function mpa_get_upgrade_to_premium_page_url() {
	return admin_url( 'admin.php?page=mpa_premium' );
}

/**
 * @param string $wrapper - span by default. Pass the empty value to remove the wrapper
 */
function mpa_get_upgrade_to_premium_html_message( string $wrapper = 'p', string $wrapperClass = 'description' ): string {

	$message = sprintf(
		// translators: %s - is link to the Upgrade to Premium page in admin area.
		__( '<a href="%s">Upgrade to Premium</a> to enable this feature.', 'motopress-appointment' ),
		esc_url( mpa_get_upgrade_to_premium_page_url() )
	);

	if ( ! empty( $wrapper ) ) {

		$message = '<' . $wrapper . ' class="' . esc_attr( $wrapperClass ) . '">' . $message . '</' . $wrapper . '>';
	}

	return $message;
}

add_action(
	'admin_footer',
	function() {
		global $pagenow, $typenow;

		if ( \MotoPress\Appointment\PostTypes\BookingPostType::POST_TYPE !== $typenow ) {
			return;
		}

		if ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow ) {
			return;
		}

		$upgradeToPremiumMessage = sprintf(
			'<span class="page-title-action button-disabled">%s</span>&nbsp;&nbsp;%s',
			mpapp()->postTypes()->booking()->getAddNewLabel(),
			mpa_get_upgrade_to_premium_html_message( 'span' )
		);

		?>
        <script type="text/javascript">
            (function ($) {
                var b = $("#wpbody-content > .wrap > .wp-heading-inline");
                b.after('<?php echo wp_kses_post( $upgradeToPremiumMessage ); ?>');
            })(jQuery);
        </script>
		<?php
	},
	999
);

add_action(
	'admin_footer_text',
	function ( $text ) {
		global $current_screen;

		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'mpa_' ) !== false ) {
			$url  = 'https://wordpress.org/support/plugin/motopress-appointment-lite/reviews/?filter=5#new-post';
			$text = sprintf(
				wp_kses( /* translators: $1$s - Plugin name: Appointment Booking Lite, $2$s - WP.org review link, $3$s - WP.org review link. */
					__( 'Please rate %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to make it available to more users. Thank you!', 'motopress-appointment' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				'<strong>' . mpapp()->getName() . '</strong>',
				$url,
				$url
			);
		}

		return $text;
	},
	999
);

add_filter(
	'plugin_action_links_' . plugin_basename( \MotoPress\Appointment\PLUGIN_FILE ),
	function( $links ) {
		$links[] = '<a'
		. ' id="mpa-upgrade-plugin-link"'
		. ' href="' . esc_url( mpa_get_upgrade_to_premium_page_url() ) . '"'
		. ' style="color: #008000;"'
		. '>' . __( 'Upgrade', 'motopress-appointment' ) . '</a>';

		return $links;
	}
);

add_action(
	'admin_head',
	function() {
		?>
		<style>
			#adminmenu #toplevel_page_mpa_appointment_menu li:not(.current) a[href="admin.php?page=mpa_premium"] {
				color: #F8C130;
			}
		</style>
		<?php
	}
);
