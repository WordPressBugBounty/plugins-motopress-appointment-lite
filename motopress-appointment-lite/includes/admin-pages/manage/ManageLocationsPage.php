<?php

namespace MotoPress\Appointment\AdminPages\Manage;

use MotoPress\Appointment\Entities\Location;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ManageLocationsPage extends ManagePostsPage {

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function customColumns() {
		return array(
			// @since 2.4.0
			'menu_order' => __( 'Order', 'motopress-appointment' ),
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
	 * @param Location $entity
	 *
	 * @since 1.0
	 */
	protected function displayValue( $columnName, $entity ) {
		switch ( $columnName ) {
			// @since 2.4.0
			case 'menu_order':
				echo esc_html( get_post_field( 'menu_order', $entity->getId() ) );
				break;

			default:
				parent::displayValue( $columnName, $entity );
				break;
		}
	}
}
