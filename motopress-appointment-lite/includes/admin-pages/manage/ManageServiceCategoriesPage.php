<?php

namespace MotoPress\Appointment\AdminPages\Manage;

use MotoPress\Appointment\PostTypes\ServicePostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * @since 2.4.0
 */
class ManageServiceCategoriesPage extends ManageTermsPage {

	public function __construct() {
		parent::__construct( ServicePostType::CATEGORY_NAME );

		add_action( 'created_' . ServicePostType::CATEGORY_NAME, array( $this, 'setDefaultOrderMeta' ), 10, 1 );
		add_action( 'admin_footer-edit-tags.php', array( $this, 'enqueueQuickEditScript' ) );

		// on manage page order sorting
		add_filter(
			'terms_clauses',
			function( $clauses, $taxonomies, $args ) {
				if (
					! is_admin() ||
					empty( $args['orderby'] ) ||
					'order' !== $args['orderby'] ||
					empty( $args['taxonomy'] ) ||
					! in_array( ServicePostType::CATEGORY_NAME, (array) $args['taxonomy'], true )
				) {
					return $clauses;
				}

				global $wpdb;

				$meta_key = ServicePostType::SERVICE_CATEGORY_ORDER_META;

				$clauses['join']   .= " LEFT JOIN {$wpdb->termmeta} AS tm ON (t.term_id = tm.term_id AND tm.meta_key = '{$meta_key}')";
				$clauses['orderby'] = 'ORDER BY CAST(tm.meta_value AS SIGNED)';

				return $clauses;
			},
			10,
			3
		);
	}

	// add default order meta on create
	public function setDefaultOrderMeta( $term_id ) {
		if ( ! get_term_meta( $term_id, ServicePostType::SERVICE_CATEGORY_ORDER_META, true ) ) {
			update_term_meta( $term_id, ServicePostType::SERVICE_CATEGORY_ORDER_META, 0 );
		}
	}

	// add order value on quick edit
	public function enqueueQuickEditScript() {
		$screen = get_current_screen();
		if ( $screen->taxonomy !== $this->taxonomy ) {
			return;
		}
		?>
		<script>
			jQuery(function($) {
				const $body = $('body');
	
				const loadOrderValue = function(id) {
					const orderValue = $('#order-value-' + id).text().trim();
					$('input.category-order').val(orderValue);
				};
	
				const originalEdit = inlineEditTax.edit;
				inlineEditTax.edit = function(id) {
					originalEdit.apply(this, arguments);
	
					let termId;
					if (typeof id === 'object') {
						termId = $(id).closest('tr').attr('id').replace('tag-', '');
					} else {
						termId = id.toString().replace('tag-', '');
					}
	
					loadOrderValue(termId);
				};
			});
		</script>
		<?php
	}

	protected function customColumns() {
		return array(
			'order' => esc_html__( 'Order', 'motopress-appointment' ),
		);
	}

	protected function customSortableColumns() {
		return array(
			'order' => 'order',
		);
	}

	public function filterColumns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;

			if ( 'slug' === $key ) {
				$new_columns['order'] = esc_html__( 'Order', 'motopress-appointment' );
			}
		}

		return $new_columns;
	}

	protected function displayCustomColumn( $content, $column_name, $term_id ) {
		if ( 'order' === $column_name ) {
			$order = get_term_meta( $term_id, ServicePostType::SERVICE_CATEGORY_ORDER_META, true );
			return '<span id="order-value-' . esc_attr( $term_id ) . '">' . esc_html( $order ) . '</span>';
		}
		return $content;
	}

	public function addQuickEditField( $column_name ) {
		if ( 'order' !== $column_name ) {
			return;
		}
		?>
			<fieldset>
				<div class="inline-edit-col">
					<label>
						<span class="title"><?php esc_html_e( 'Order', 'motopress-appointment' ); ?></span>
						<span class="input-text-wrap">
							<input type="number" name="<?php echo esc_attr( ServicePostType::SERVICE_CATEGORY_ORDER_META ); ?>" class="category-order" value="" />
						</span>
					</label>
				</div>
				<?php wp_nonce_field( 'save_service_category_order', 'service_category_order_nonce' ); ?>
			</fieldset>
		<?php
	}

	public function saveQuickEditField( $term_id ) {
		if ( ! isset( $_POST['service_category_order_nonce'] ) || ! wp_verify_nonce( $_POST['service_category_order_nonce'], 'save_service_category_order' ) ) {
			return;
		}

		if ( isset( $_POST[ ServicePostType::SERVICE_CATEGORY_ORDER_META ] ) ) {
			update_term_meta( $term_id, ServicePostType::SERVICE_CATEGORY_ORDER_META, intval( $_POST[ ServicePostType::SERVICE_CATEGORY_ORDER_META ] ) );
		}
	}
}
