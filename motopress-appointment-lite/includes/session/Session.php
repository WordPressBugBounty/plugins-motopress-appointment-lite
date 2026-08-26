<?php

declare(strict_types=1);

namespace MotoPress\Appointment\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Session {
	private ?WP_Session $session = null;

	public function __construct() {
		define( __NAMESPACE__ . '\WP_SESSION_COOKIE', 'mpa_session' );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Retrieves a session variable.
	 *
	 * @return mixed Session variable if set or null otherwise.
	 */
	public function get( string $key ) {
		if ( ! is_null( $this->session ) ) {
			$key = sanitize_key( $key );

			return isset( $this->session[ $key ] )
				? maybe_unserialize( $this->session[ $key ] )
				: null;

		} else {
			return null;
		}
	}

	public function getSessionId(): string {
		if ( ! is_null( $this->session ) ) {
			return $this->session->session_id;
		} else {
			return '';
		}
	}

	public function init(): void {
		if ( is_null( $this->session ) ) {
			$this->session = WP_Session::get_instance();
		}
	}

	/**
	 * Sets a session variable.
	 */
	public function set( string $key, $value ): void {
		if ( is_null( $this->session ) ) {
			return;
		}

		$key = sanitize_key( $key );

		if ( is_scalar( $value ) ) {
			$this->session[ $key ] = $value;
		} else {
			$this->session[ $key ] = serialize( $value );
		}
	}

	public function toArray(): array {
		if ( ! is_null( $this->session ) ) {
			return array_map( 'maybe_unserialize', $this->session->toArray() );
		} else {
			return array();
		}
	}
}
