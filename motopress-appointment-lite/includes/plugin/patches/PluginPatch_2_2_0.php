<?php

namespace MotoPress\Appointment\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class PluginPatch_2_2_0 extends AbstractPluginPatch {


	public static function getVersion(): string {
		return '2.2.0';
	}

	public static function execute(): bool {

		$encodingOptions = array(
			'mpa_stripe_payment_gateway_public_key',
			'mpa_stripe_payment_gateway_secret_key',
			'mpa_stripe_payment_gateway_webhook_key',
			'mpa_paypal_payment_gateway_secret',
			'mpa_google_calendar_client_secret',
		);

		foreach ( $encodingOptions as $encodingOption ) {

			$optionValue = get_option( $encodingOption, false );

			if ( ! empty( $optionValue ) ) {

				$optionValue = \MotoPress\Appointment\Helpers\StringEncryptHelper::encryptString( $optionValue );

				update_option( $encodingOption, $optionValue, true );
			}
		}

		return true;
	}
}
