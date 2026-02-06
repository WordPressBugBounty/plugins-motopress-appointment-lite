<?php

namespace MotoPress\Appointment\Bundles;

use MotoPress\Appointment\Libraries\Umpirsky\UmpirskyHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class CountriesBundle {

	/**
	 * @var array [Country code => Country label]
	 *
	 * @since 1.0
	 */
	protected $countries = array();

	/**
	 * @return array [Country code => Country label]
	 *
	 * @since 1.0
	 * @since 2.4.3 updated to get list from lib.
	 */
	public function getCountries() {

		if ( empty( $this->countries ) ) {

			$countries = UmpirskyHelper::getCountryList();

			/** @since 1.0 */
			$countries = apply_filters( 'mpa_countries', $countries );

			$this->countries = $countries;
		}

		return $this->countries;
	}

	/**
	 * @param string $code
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getLabel( $code ) {

		$countries = $this->getCountries(); // Init if not already

		return array_key_exists( $code, $countries ) ? $countries[ $code ] : '';
	}

	/**
	 * @param string $label
	 * @return string
	 *
	 * @since 1.0
	 */
	public function getCode( $label ) {

		$countries   = $this->getCountries(); // Init if not already
		$countryCode = array_search( $label, $countries );

		return $countryCode ? $countryCode : '';
	}
}
