<?php

namespace MotoPress\Appointment\Handlers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handler uses https://github.com/googleapis/google-api-php-client/tree/v2.1.3
 * @since 1.10.0
 */
class GoogleCalendarSyncHandler {

	const MPA_EMPLOYEE_META_KEY_GOOGLE_CALENDAR_TOKEN       = 'mpa_google_calendar_token';
	const MPA_RESERVATION_META_KEY_GOOGLE_CALENDAR_EVENT_ID = 'mpa_google_calendar_event_id';


	public function __construct() {}


	public static function isGoogleCalendarConnectedToEmployee( int $mpaEmployeeId ): bool {
		return false;
	}


	public static function disconnectGoogleCalendarFromEmployee( int $mpaEmployeeId ) {}


	public static function getGoogleCalendarConnectionURL( int $mpaEmployeeId ): string {
		return mpa_get_upgrade_to_premium_page_url();
	}
}
