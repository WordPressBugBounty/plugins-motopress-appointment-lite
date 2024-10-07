<?php

namespace MotoPress\Appointment\Payments\Gateways;

use MotoPress\Appointment\API\StripeAPI;
use MotoPress\Appointment\Payments\Gateways\Webhooks\StripeWebhooksListener;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.5.0
 */
class StripePaymentGateway extends AbstractPaymentGateway {

	/**
	 * Public API key.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	public $publicKey = '';

	/**
	 * Secret API key.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	public $secretKey = '';

	/**
	 * Webhook endpoint secret key.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	public $webhookKey = '';

	/**
	 * Enabled payment methods.
	 *
	 * @since 1.5.0
	 * @var string[]
	 */
	public $paymentMethods = array();

	/**
	 * Equals to <code>$paymentMethods</code> property if the currency is euro,
	 * [] or ['card'] otherwise.
	 *
	 * @since 1.5.0
	 * @var string[]
	 */
	public $allowedMethods = array();

	/**
	 * @since 1.5.0
	 * @var string
	 */
	public $checkoutLocale = 'auto';

	/**
	 * @since 1.5.0
	 * @var StripeAPI
	 */
	public $api = null;

	/**
	 * @since 1.5.0
	 * @var StripeWebhooksListener
	 */
	public $webhooks = null;

	/**
	 * @since 1.16.0
	 * @var string
	 */
	protected $accountCountry = '';

	/**
	 * @since 1.5.0
	 */
	public function __construct() {

		parent::__construct();

		$this->addListeners();
	}

	public function getId(): string {
		return 'stripe';
	}

	public function getName(): string {
		return __( 'Stripe', 'motopress-appointment' );
	}

	public function getDefaultPublicName(): string {
		return __( 'Credit card (Stripe)', 'motopress-appointment' );
	}

	protected function getDefaultDescription(): string {
		return __( 'Pay with your credit card via Stripe. Use the card number 4242424242424242 with CVC 123, a valid expiration date and random 5-digit ZIP-code to test a payment.', 'motopress-appointment' );
	}

	protected function addListeners() {}

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

	public function printBillingFields() {}

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
