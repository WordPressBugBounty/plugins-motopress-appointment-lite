<?php

namespace MotoPress\Appointment\Payments\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.6.0
 */
class PayPalPaymentGateway extends AbstractPaymentGateway {

	/**
	 * @since 1.6.0
	 */
	const OPTION_NAME_PAYPAL_WEBHOOK_ID = 'mpa_paypal_webhook_id';

	/**
	 * @since 1.6.0
	 */
	private $supportedCurrenciesAndTheirDecimals = array(
		'AUD' => 2,
		'BRL' => 2,
		'CAD' => 2,
		'CNY' => 2,
		'CZK' => 2,
		'DKK' => 2,
		'EUR' => 2,
		'HKD' => 2,
		'HUF' => 0,
		'ILS' => 2,
		'JPY' => 0,
		'MYR' => 2,
		'MXN' => 2,
		'TWD' => 0,
		'NZD' => 2,
		'NOK' => 2,
		'PHP' => 2,
		'PLN' => 2,
		'GBP' => 2,
		'RUB' => 2,
		'SGD' => 2,
		'SEK' => 2,
		'CHF' => 2,
		'THB' => 2,
		'USD' => 2,
	);

	/**
	 * @since 1.6.0
	 */
	private $paypalClientId = '';

	/**
	 * @since 1.6.0
	 */
	private $paypalSecret = '';

	/**
	 * @since 1.6.0
	 */
	private $webhookListener = null;

	/**
	 * @since 1.6.0
	 */
	public function __construct() {

		parent::__construct();

		$this->paypalClientId = $this->getOption( 'client_id', $this->paypalClientId );
		$this->paypalSecret   = $this->getOption( 'secret', $this->paypalSecret );
	}

	public function getId(): string {
		return 'paypal';
	}

	public function getName(): string {
		return __( 'PayPal', 'motopress-appointment' );
	}

	public function getDefaultPublicName(): string {
		return __( 'Pay by PayPal', 'motopress-appointment' );
	}

	protected function getAdminDescription() {

		return wp_kses_post( mpa_get_upgrade_to_premium_html_message( '' ) );
	}

	public function enqueueScripts() {}

	/**
	 * @since 1.6.0
	 */
	private function isCurrencyFromSettingsSupported() {
		return in_array( mpapp()->settings()->getCurrency(), array_keys( $this->supportedCurrenciesAndTheirDecimals ) );
	}

	/**
	 * @since 1.6.0
	 */
	public function getFrontendData() {

		$paypalPriceDecimals = 2;

		if ( $this->isCurrencyFromSettingsSupported() ) {

			$paypalPriceDecimals = $this->supportedCurrenciesAndTheirDecimals[ mpapp()->settings()->getCurrency() ];
		}

		return parent::getFrontendData() + array(
			'paypal_price_decimals' => $paypalPriceDecimals,
			'paypal_error_message'  => esc_html__( 'Something went wrong, please try again.', 'motopress-appointment' ),
		);
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

	/**
	 * @since 1.6.0
	 */
	public function printBillingFields() {

		echo '<div class="mpa-paypal-error mpa-hide"></div>
            <div class="mpa-paypal-container"></div>';
	}

	public function processPayment( $payment, $paymentDetails ) {
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
