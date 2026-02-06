<?php

namespace MotoPress\Appointment\Bundles;

use MotoPress\Appointment\Libraries\Umpirsky\UmpirskyHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class CurrenciesBundle {

	private $currencyDecimals = array(
		'EUR' => 2,
		'USD' => 2,
		'GBP' => 2,
		'AED' => 2,
		'AFN' => 2,
		'ALL' => 2,
		'AMD' => 2,
		'ANG' => 2,
		'AOA' => 2,
		'ARS' => 2,
		'AUD' => 2,
		'AWG' => 2,
		'AZN' => 2,
		'BAM' => 2,
		'BBD' => 2,
		'BDT' => 2,
		'BGN' => 2,
		'BHD' => 3,
		'BIF' => 2,
		'BMD' => 2,
		'BND' => 2,
		'BOB' => 2,
		'BRL' => 2,
		'BSD' => 2,
		'BTC' => 2,
		'BTN' => 2,
		'BWP' => 2,
		'BYR' => 2,
		'BYN' => 2,
		'BZD' => 2,
		'CAD' => 2,
		'CDF' => 2,
		'CHF' => 2,
		'CLP' => 2,
		'CNY' => 2,
		'COP' => 2,
		'CRC' => 2,
		'CUC' => 2,
		'CUP' => 2,
		'CVE' => 0,
		'CZK' => 2,
		'DJF' => 0,
		'DKK' => 2,
		'DOP' => 2,
		'DZD' => 2,
		'EGP' => 2,
		'ERN' => 2,
		'ETB' => 2,
		'FJD' => 2,
		'FKP' => 2,
		'GEL' => 2,
		'GGP' => 2,
		'GHS' => 2,
		'GIP' => 2,
		'GMD' => 2,
		'GNF' => 0,
		'GTQ' => 2,
		'GYD' => 2,
		'HKD' => 2,
		'HNL' => 2,
		'HRK' => 2,
		'HTG' => 2,
		'HUF' => 2,
		'IDR' => 0,
		'ILS' => 2,
		'IMP' => 2,
		'INR' => 2,
		'IQD' => 3,
		'IRR' => 2,
		'IRT' => 2,
		'ISK' => 2,
		'JEP' => 2,
		'JMD' => 2,
		'JOD' => 3,
		'JPY' => 0,
		'KES' => 2,
		'KGS' => 2,
		'KHR' => 2,
		'KMF' => 0,
		'KPW' => 2,
		'KRW' => 0,
		'KWD' => 3,
		'KYD' => 2,
		'KZT' => 2,
		'LAK' => 2,
		'LBP' => 2,
		'LKR' => 2,
		'LRD' => 2,
		'LSL' => 2,
		'LYD' => 3,
		'MAD' => 2,
		'MDL' => 2,
		'MGA' => 2,
		'MKD' => 2,
		'MMK' => 2,
		'MNT' => 2,
		'MOP' => 2,
		'MRO' => 2,
		'MUR' => 2,
		'MVR' => 2,
		'MWK' => 2,
		'MXN' => 2,
		'MYR' => 2,
		'MZN' => 2,
		'NAD' => 2,
		'NGN' => 2,
		'NIO' => 2,
		'NOK' => 2,
		'NPR' => 2,
		'NZD' => 2,
		'OMR' => 3,
		'PAB' => 2,
		'PEN' => 2,
		'PGK' => 2,
		'PHP' => 2,
		'PKR' => 2,
		'PLN' => 2,
		'PRB' => 2,
		'PYG' => 0,
		'QAR' => 2,
		'RON' => 2,
		'RSD' => 2,
		'RUB' => 2,
		'RWF' => 0,
		'SAR' => 2,
		'SBD' => 2,
		'SCR' => 2,
		'SDG' => 2,
		'SEK' => 2,
		'SGD' => 2,
		'SHP' => 2,
		'SLL' => 2,
		'SOS' => 2,
		'SRD' => 2,
		'SSP' => 2,
		'STD' => 2,
		'SYP' => 2,
		'SZL' => 2,
		'THB' => 2,
		'TJS' => 2,
		'TMT' => 2,
		'TND' => 3,
		'TOP' => 2,
		'TRY' => 2,
		'TTD' => 2,
		'TWD' => 2,
		'TZS' => 2,
		'UAH' => 2,
		'UGX' => 0,
		'UYU' => 2,
		'UZS' => 2,
		'VEF' => 2,
		'VES' => 2,
		'VND' => 0,
		'VUV' => 0,
		'WST' => 2,
		'XAF' => 0,
		'XCD' => 2,
		'XOF' => 0,
		'XPF' => 0,
		'YER' => 2,
		'ZAR' => 2,
		'ZMW' => 2,
	);

	/**
	 * @var array [Currency code => Currency label (with symbol)]
	 *
	 * @since 1.0
	 */
	protected $currencies = array();

	/**
	 * @var array [Currency code => Currency symbol]
	 *
	 * @since 1.0
	 */
	protected $symbols = array();

	/**
	 * @return array [Currency code => Currency label (with symbol)]
	 *
	 * @since 1.0
	 */
	public function getCurrencies() {

		if ( empty( $this->currencies ) ) {

			$currencies = $this->currenciesList();
			$symbols    = $this->getSymbols();

			// Add symbols
			foreach ( $currencies as $code => &$label ) {
				$label .= ' (' . $symbols[ $code ] . ')';
			}

			unset( $label );

			/** @since 1.0 */
			$currencies = apply_filters( 'mpa_currencies', $currencies );

			$this->currencies = $currencies;
		}

		return $this->currencies;
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function getPositions() {
		return array(
			'before'            => esc_html__( 'Before', 'motopress-appointment' ),
			'after'             => esc_html__( 'After', 'motopress-appointment' ),
			'before_with_space' => esc_html__( 'Before with space', 'motopress-appointment' ),
			'after_with_space'  => esc_html__( 'After with space', 'motopress-appointment' ),
		);
	}

	/**
	 * @return array [Currency code => Currency symbol]
	 *
	 * @since 1.0
	 */
	public function getSymbols() {
		if ( empty( $this->symbols ) ) {
			$symbols = $this->symbolsList();

			/**
			 * @param array $symbols [Currency code => Currency symbol]
			 */
			$symbols = apply_filters( 'mpa_currency_symbols', $symbols );

			$this->symbols = $symbols;
		}

		return $this->symbols;
	}

	/**
	 * @param string $code Currency code, like 'EUR'.
	 * @return Currency symbol or '' if no such currency.
	 *
	 * @since 1.0
	 */
	public function getSymbol( $code ) {
		$symbols = $this->getSymbols();

		return array_key_exists( $code, $symbols ) ? $symbols[ $code ] : '';
	}

	/**
	 * Converts, for example dollars to cents: 100 -> 10000
	 */
	public function getAmountInSmallestCurrencyUnits( string $currencyCode, float $amount ): int {

		$currencyDecimals = 2;

		if ( ! empty( $currencyDecimals[ $currencyCode ] ) ) {

			$currencyDecimals = $this->currencyDecimals[ $currencyCode ];
		}

		return absint( $amount * ( 10 ** $currencyDecimals ) );
	}


	/**
	 * @return array [Currency code => Currency label]
	 * @since 1.0
	 * @since 2.4.3 updated to get list from lib.
	 */
	protected static function currenciesList(): array {

		$currency_list    = UmpirskyHelper::getCurrencyList();
		$currency_symbols = static::symbolsList();

		$currencies = array(
			// Prioritize EUR, USD and GBP currencies:
			// [EUR, USD, GBP, ... all other currencies]
			'EUR' => 'Euro',
			'USD' => 'US Dollar',
			'GBP' => 'British Pound',
		);

		// Collect only those currencies that have a symbol defined.
		foreach ( $currency_list as $code => $label ) {
			if ( array_key_exists( $code, $currency_symbols ) ) {
				$currencies[ $code ] = $label;
			}
		}

		return $currencies;
	}

	/**
	 * @return array [Currency code => Currency symbol]
	 *
	 * @since 1.0
	 */
	protected static function symbolsList() {
		return array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => '&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
	}
}
