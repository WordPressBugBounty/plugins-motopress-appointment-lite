<?php

namespace MotoPress\Appointment\AdminPages\Custom;

use MotoPress\Appointment\Handlers\WizardHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
class WizardPage extends AbstractCustomPage {

	protected $wizardHandler;

	public function __construct( $name, $args = array() ) {
		parent::__construct( $name, $args );

		$this->wizardHandler = WizardHandler::getInstance();
		$this->wizardHandler->registerWizardPageHooks();
	}

	protected function enqueueScripts() {
		mpa_assets()->enqueueStyle( 'mpa-admin' );

		wp_enqueue_script(
			'mpa-wizard-page',
			mpapp()->getPluginUrl( 'assets/js/wizard-page.min.js' ),
			array(),
			mpapp()->getVersion(),
			true
		);

		wp_enqueue_style(
			'mpa-wizard-page-styles',
			mpapp()->getPluginUrl( 'assets/css/wizard-page.min.css' ),
			array(),
			mpapp()->getVersion()
		);
	}

	public function load() {
		if ( ! $this->isCurrentPage() ) {
			return;
		}
	}

	public function display() {
		?>
			<div class="wrap">
				<?php
				mpa_display_template(
					'private/pages/wizard.php',
					array(
						'wizard' => $this->wizardHandler,
					)
				);
				?>
			</div>
		<?php
	}

	/**
	 * @return string
	 */
	protected function getPageTitle() {
		return esc_html__( 'Wizard', 'motopress-appointment' );
	}

	/**
	 * @return string
	 */
	protected function getMenuTitle() {
		return esc_html__( 'Wizard', 'motopress-appointment' );
	}
}
