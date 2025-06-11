<?php

namespace MotoPress\Appointment\AdminPages\Manage;

use MotoPress\Appointment\Entities\Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ManageServicesPage extends ManagePostsPage {

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function customColumns() {
		return array(
			'price'      => esc_html__( 'Price', 'motopress-appointment' ),
			'duration'   => esc_html__( 'Duration', 'motopress-appointment' ),
			'menu_order' => esc_html__( 'Order', 'motopress-appointment' ),
		);
	}

	/**
	 * @return array
	 *
	 * @since 2.4.0
	 */
	protected function customSortableColumns() {
		return array(
			'menu_order' => 'menu_order',
		);
	}

	/**
	 * @param string $columnName
	 * @param Service $entity
	 *
	 * @since 1.0
	 */
	protected function displayValue( $columnName, $entity ) {
		switch ( $columnName ) {
			case 'price':
				echo mpa_tmpl_price( $entity->getPrice() );
				break;
			case 'duration':
				echo mpa_minutes_to_duration( $entity->getDuration() );
				break;
			// @since 2.4.0
			case 'menu_order':
				echo esc_html( get_post_field( 'menu_order', $entity->getId() ) );
				break;
		}
	}
}
