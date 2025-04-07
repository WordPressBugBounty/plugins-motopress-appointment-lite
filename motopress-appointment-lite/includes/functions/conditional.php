<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string $functionName
 * @return bool
 *
 * @since 1.0
 */
function mpa_is_function_disabled( $functionName ) {
	$disabledFunctions = explode( ',', ini_get( 'disable_functions' ) );

	return in_array( $functionName, $disabledFunctions );
}

/**
 * @param mixed $value
 * @return bool
 *
 * @since 1.0
 */
function mpa_is_operator( $value ) {
	return is_string( $value ) && in_array(
		strtoupper( $value ),
		array(
			'=',
			'!=',
			'>',
			'>=',
			'<',
			'<=',
			'LIKE',
			'NOT LIKE',
			'IN',
			'NOT IN',
			'BETWEEN',
			'NOT BETWEEN',
			'REGEXP',
			'NOT REGEXP',
			'RLIKE',
			'EXISTS',
			'NOT EXISTS',
		)
	);
}

/**
 * @param int|\WP_Post $post
 * @return bool
 *
 * @since 1.0
 */
function mpa_is_post_autosave( $post ) {
	return defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_autosave( $post ) );
}

/**
 * @param int|\WP_Post $post
 * @return bool
 *
 * @since 1.0
 */
function mpa_is_post_revision( $post ) {
	return is_int( wp_is_post_revision( $post ) );
}

/**
 * @param string $language Language like "en", "uk", "ru" etc.
 * @return bool
 *
 * @since 1.2.1
 */
function mpa_is_flatpickr_l10n( $language ) {
	// "en" skipped, since it's localization by default
	return in_array(
		$language,
		array(
			'ar',
			'at',
			'az',
			'be',
			'bg',
			'bn',
			'bs',
			'cat',
			'cs',
			'cy',
			'da',
			'de',
			'eo',
			'es',
			'et',
			'fa',
			'fi',
			'fo',
			'fr',
			'ga',
			'gr',
			'he',
			'hi',
			'hr',
			'hu',
			'id',
			'is',
			'it',
			'ja',
			'ka',
			'km',
			'ko',
			'kz',
			'lt',
			'lv',
			'mk',
			'mn',
			'ms',
			'my',
			'nl',
			'no',
			'pa',
			'pl',
			'pt',
			'ro',
			'ru',
			'si',
			'sk',
			'sl',
			'sq',
			'sr',
			'sr-cyr',
			'sv',
			'th',
			'tr',
			'uk',
			'vn',
			'zh',
			'zh-tw',
		)
	);
}

/**
 * @param string $wpLocale Converts a WordPress locale to a Flatpickr language code.
 * @return string
 *
 * @since 2.3.0
 */
function mpa_wp2flatpickr_l10n( $wpLocale ) {

	$languages = [
		'sq' => 'sq',
		'ar' => 'ar',
		'az' => 'az',
		'bel' => 'be',
		'bn_BD' => 'bn',
		'bs_BA' => 'bs',
		'bg_BG' => 'bg',
		'ca' => 'cat',
		'zh_CN' => 'zh',
		'zh_TW' => 'zh-tw',
		'hr' => 'hr',
		'cs_CZ' => 'cs',
		'da_DK' => 'da',
		'nl_NL' => 'nl',
		'eo' => 'eo',
		'et' => 'et',
		'fo' => 'fo',
		'fi' => 'fi',
		'fr_FR' => 'fr',
		'ka_GE' => 'ka',
		'de_DE' => 'de',
		'de_AT' => 'at',
		'el' => 'gr',
		'he_IL' => 'he',
		'hi_IN' => 'hi',
		'hu_HU' => 'hu',
		'is_IS' => 'is',
		'id_ID' => 'id',
		'ga' => 'ga',
		'it_IT' => 'it',
		'ja' => 'ja',
		'kk' => 'kz',
		'km' => 'km',
		'ko_KR' => 'ko',
		'lv' => 'lv',
		'lt_LT' => 'lt',
		'mk_MK' => 'mk',
		'ms_MY' => 'ms',
		'mn' => 'mn',
		'my_MM' => 'my',
		'nb_NO' => 'no',
		'pa_IN' => 'pa',
		'fa_IR' => 'fa',
		'pl_PL' => 'pl',
		'pt_PT' => 'pt',
		'ro_RO' => 'ro',
		'ru_RU' => 'ru',
		'sr_RS' => 'sr',
		'si_LK' => 'si',
		'sk_SK' => 'sk',
		'sl_SI' => 'sl',
		'es_ES' => 'es',
		'sv_SE' => 'sv',
		'th' => 'th',
		'tr_TR' => 'tr',
		'uk' => 'uk',
		'vi' => 'vn',
		'cy' => 'cy',
		//looks the same?
		'zh_HK' => 'zh',
		'zh_SG' => 'zh',
		'fr_BE' => 'fr',
		'fr_CA' => 'fr',
		'de_CH' => 'de',
		'pt_AO' => 'pt',
		'pt_BR' => 'pt',
		'es_AR' => 'es',
		'es_CL' => 'es',
		'es_CO' => 'es',
		'es_CR' => 'es',
		'es_DO' => 'es',
		'es_EC' => 'es',
		'es_GT' => 'es',
		'es_HN' => 'es',
		'es_MX' => 'es',
		'es_PE' => 'es',
		'es_PR' => 'es',
		'es_UY' => 'es',
		'es_VE' => 'es',
	];

	if ( array_key_exists( $wpLocale, $languages ) ) {

		return $languages[$wpLocale];
	}

	return $wpLocale;
}
