<?php

namespace MotoPress\Appointment\AdminPages\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UpgradeToPremiumPage extends AbstractCustomPage {


	/**
	 * @return string
	 */
	protected function getPageTitle() {
		return esc_html__( 'Go Premium', 'motopress-appointment' );
	}

	/**
	 * @return string
	 */
	protected function getMenuTitle() {

		return '<span class="dashicons dashicons-superhero-alt" style="font-size:17px;vertical-align:middle;"></span> ' .
			esc_html__( 'Go Premium', 'motopress-appointment' );
	}


	public function load() {

		if ( $this->isCurrentPage() ) {

			add_action(
				'admin_head',
				function() {
					?>
					<style>
						.mpa-upgrade-to-premium {
							max-width: 1000px;
						}
						.mpa-upgrade-to-premium .mpa-column-title {
							width: 30%;
						}
						.mpa-upgrade-to-premium .mpa-column-title .mpa-title {
							font-weight: 600;
							font-size: 16px;
							margin-bottom: 5px;
							display: block;
						}
						.mpa-upgrade-to-premium .mpa-column-lite {
							width: 15%;
							text-align: center;
						}
						.mpa-upgrade-to-premium .mpa-column-pro {
							width: 30%;
							text-align: center;
						}
						.mpa-upgrade-to-premium .mpa-icon-no, .mpa-upgrade-to-premium .mpa-icon-yes {
							font-size: 30px;
							width: 30px;
							height: 30px;
						}
						.mpa-upgrade-to-premium .mpa-icon-yes {
							color: #64d519;
						}
						.mpa-upgrade-to-premium .mpa-icon-no {
							color: #eb4c3f;
						}
						.mpa-upgrade-to-premium .mpa-comparison-table th, .mpa-upgrade-to-premium .mpa-comparison-table td {
							padding: 10px 15px;
							vertical-align: middle;
							font-size: 16px;
						}
					</style>
					<?php
				}
			);
		}
	}


	public function display() {
		?>
		<div class="wrap">
			<h1 class="wp-title-inline"><?php esc_html_e( 'Upgrade to Appointment Booking Premium', 'motopress-appointment' ); ?></h1>

			<hr class="wp-header-end" />

			<div class="mpa-upgrade-to-premium">
				<p>
					<a class="button button-primary"
						href="https://motopress.com/products/appointment-booking/?utm_source=appointment-booking-lite&utm_medium=button-in-dashboard"
						target="_blank">
						<?php esc_html_e( 'Go Premium', 'motopress-appointment' ); ?>
					</a>
				</p>
				<div class="notice notice-info inline">
					<p>
					<?php

						printf(
							// translators: %1$s - <a ... link to demo website>, %2$s - </a>, %3$s - <strong>, %4$s - </strong>
							esc_html__( 'Want to see and test the PRO features first-hand on the backend? %3$s %1$s Start a free trial %2$s with the PRO plugin version. %4$s', 'motopress-appointment' ),
							sprintf(
								'<a href="%s" target="blank">',
								'https://appointment.getmotopress.com/?utm_source=motopress-appointment-lite&utm_medium=button-in-dashboard'
							),
							'</a>',
							'<strong>',
							'</strong>'
						);
					?>
					</p>
				</div>
				<p>
					<?php esc_html_e( 'Take full advantage of Appointment Booking with the premium plugin version. Compare the features:', 'motopress-appointment' ); ?>
				</p>

				<table class="widefat striped mpa-comparison-table">
					<thead>
					<tr>
						<th class="mpa-column-title"></th>
						<th class="mpa-column-lite"><strong><?php esc_html_e( 'Lite Version', 'motopress-appointment' ); ?></strong></th>
						<th class="mpa-column-pro"><strong><?php esc_html_e( 'Pro Version', 'motopress-appointment' ); ?></strong></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Technical support', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( 'Priority support via live chat, email, and ticket system.', 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><?php esc_html_e( 'FAQ', 'motopress-appointment' ); ?></td>
						<td class="mpa-column-pro"><?php esc_html_e( 'Email, tickets system (we even login to your WordPress to help)', 'motopress-appointment' ); ?></td>
					</tr>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Built-in payment gateways', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( 'Already integrated payment gateways for secure online payments and bookings.', 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><?php esc_html_e( 'Pay on-site', 'motopress-appointment' ); ?></td>
						<td class="mpa-column-pro"><?php esc_html_e( 'Direct Bank Transfer, Stripe, Apple Pay, Google Pay, PayPal, more on the way', 'motopress-appointment' ); ?></td>
					</tr>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Extendable with Appointment Booking addons', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( 'The facility to connect any extra free or premium Appointment Booking extension.', 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><span class="mpa-icon-yes dashicons dashicons-yes"></span></td>
						<td class="mpa-column-pro"><span class="mpa-icon-yes dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Bookings synchronization to Google Calendar', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( "Sync website bookings to employee's Google Calendar for automatic schedule updates and notifications.", 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><span class="mpa-icon-no dashicons dashicons-no-alt"></span></td>
						<td class="mpa-column-pro"><span class="mpa-icon-yes dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Adding bookings manually by site admins', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( 'Register new customers manually via the site admin dashboard.', 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><span class="mpa-icon-no dashicons dashicons-no-alt"></span></td>
						<td class="mpa-column-pro"><span class="mpa-icon-yes dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td class="mpa-column-title">
							<span class="mpa-title"><?php esc_html_e( 'Reminders and notifications', 'motopress-appointment' ); ?></span>
							<p class="description"><?php esc_html_e( 'Send automated reminders and other notifications before and after the appointment.', 'motopress-appointment' ); ?></p>
						</td>
						<td class="mpa-column-lite"><?php esc_html_e( 'Admin, Employee and Custom Email Addresses', 'motopress-appointment' ); ?></td>
						<td class="mpa-column-pro"><?php esc_html_e( 'Customer, Admin, Employee and Custom Email Addresses', 'motopress-appointment' ); ?></td>
					</tr>
					</tbody>
				</table>

				<p>
					<a class="button button-primary"
						href="https://motopress.com/products/appointment-booking/?utm_source=appointment-booking-lite&utm_medium=button-in-dashboard"
						target="_blank">
						<?php esc_html_e( 'Go Premium', 'motopress-appointment' ); ?>
					</a>
				</p>

			</div>
		</div>
		<?php
	}
}
