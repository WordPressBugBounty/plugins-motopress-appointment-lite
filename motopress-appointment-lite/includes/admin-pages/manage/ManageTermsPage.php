<?php

namespace MotoPress\Appointment\AdminPages\Manage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class ManageTermsPage {

	protected $taxonomy;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
		$this->addActions();
	}

	public function addActions() {
		add_filter( "manage_edit-{$this->taxonomy}_columns", array( $this, 'filterColumns' ) );
		add_action( "manage_{$this->taxonomy}_custom_column", array( $this, 'manageCustomColumn' ), 10, 3 );
		add_filter( "manage_edit-{$this->taxonomy}_sortable_columns", array( $this, 'filterSortableColumns' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'addQuickEditField' ), 10, 3 );
		add_action( "edited_{$this->taxonomy}", array( $this, 'saveQuickEditField' ), 10, 2 );
		add_action( "create_{$this->taxonomy}", array( $this, 'saveQuickEditField' ), 10, 2 );
	}

	public function filterColumns( $columns ) {
		return array_merge( $columns, $this->customColumns() );
	}

	public function filterSortableColumns( $columns ) {
		return array_merge( $columns, $this->customSortableColumns() );
	}

	public function manageCustomColumn( $content, $column_name, $term_id ) {
		return $this->displayCustomColumn( $content, $column_name, $term_id );
	}

	protected function customColumns() {
		return array();
	}

	protected function customSortableColumns() {
		return array();
	}

	protected function displayCustomColumn( $content, $column_name, $term_id ) {
		return $content;
	}

	public function addQuickEditField( $column_name ) {
	}

	public function saveQuickEditField( $term_id ) {
	}

	protected function isCurrentPage() {
		global $pagenow;

		return is_admin() && 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] === $this->taxonomy;
	}
}
