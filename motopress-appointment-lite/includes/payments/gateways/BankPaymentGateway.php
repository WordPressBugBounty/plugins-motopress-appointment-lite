<?php

namespace MotoPress\Appointment\Payments\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.5.0
 */
class BankPaymentGateway extends AbstractInstructionPaymentGateway {

	public function getId(): string {
		return 'bank';
	}

	public function getName(): string {
		return __( 'Direct Bank Transfer', 'motopress-appointment' );
	}

	public function getDefaultPublicName(): string {
		return __( 'Direct bank transfer', 'motopress-appointment' );
	}

	protected function getDefaultDescription(): string {
		return __( 'Make your payment directly into our bank account. Please use your Booking ID as the payment reference.', 'motopress-appointment' );
	}

	public function isSupportsSandbox(): bool {
		return false;
	}


	protected function getAdminDescription() {
		return wp_kses_post( mpa_get_upgrade_to_premium_html_message( '' ) );
	}

	public function getFields() {
		return array( parent::getFields()[ $this->getOptionNameRaw( 'group' ) ] );
	}

	public function isEnabled(): bool {
		return false;
	}

	public function isActive() {
		return false;
	}

	public function processPayment( $payment, $args ) {
		return true;
	}

	/**
	 * @since 1.18.0
	 *
	 * @return true
	 */
	public function isOnlyPremiumAvailable(): bool {
		return true;
	}
}
