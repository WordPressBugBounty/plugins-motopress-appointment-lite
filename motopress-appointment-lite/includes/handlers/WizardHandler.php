<?php

namespace MotoPress\Appointment\Handlers;

use MotoPress\Appointment\Fields\FieldsFactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
class WizardHandler {

	const NONCE_NAME              = 'mpa-wizard-nonce';
	const NONCE_ACTION_SKIP       = 'mpa-wizard-skip';
	const MPA_WIZARD_STATE_OPTION = 'mpa_wizard_status';

	private static $instance = null;

	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {

		if ( ! is_admin() ||
		wp_doing_ajax() ||
		wp_doing_cron() ||
		is_network_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'maybeStartWizard' ) );
		add_action( 'init', array( $this, 'checkUserAction' ) );
	}

	public function maybeStartWizard() {

		if ( apply_filters( 'mpa_enable_setup_wizard', true )
			&& ! $this->isWizardCompleted()
			&& ! $this->isAppointmentServiceAvailable()
			&& $this->checkCapabilities() ) {
			add_action( 'admin_notices', array( $this, 'mpaWizardNotice' ) );
		}
	}

	/**
	 * Display wizard notice
	 *
	 * @return void
	 */
	public function mpaWizardNotice() {

		// dont show on wizard page
		if ( $this->isMPAWizardPage() ) {
			return;
		}

		$startWizardUrl = admin_url( 'admin.php?page=mpa_wizard' );
		$skipUrl        = wp_nonce_url( add_query_arg( 'mpa_wizard_action', 'skip' ), self::NONCE_ACTION_SKIP, self::NONCE_NAME );
		?>
		<div class="notice notice-info">
			<p><strong>
				<?php
					printf(
						// translators: %s is the plugin name
						esc_html__( '%s Setup Wizard', 'mpa-video-conferencing' ),
						esc_html( mpapp()->getName() )
					)
				?>
			</strong></p>
			<p><?php esc_html_e( 'Set up your Appointment Booking plugin in just a few simple steps! This wizard will guide you through adding your first service with the necessary settings. You can add unlimited services, staff members, and locations later.', 'motopress-appointment' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $startWizardUrl ); ?>" class="button-primary"><?php esc_html_e( 'Run Wizard', 'motopress-appointment' ); ?></a>
				<a href="<?php echo esc_url( $skipUrl ); ?>" class="button-secondary"><?php esc_html_e( 'Skip', 'motopress-appointment' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 *
	 * @return void
	 */
	public function registerWizardPageHooks() {
		add_action( 'wp_ajax_mpa_finish_wizard', array( $this, 'mpaFinishWizardHandler' ) );
		add_action( 'wp_ajax_mpa_skip_wizard', array( $this, 'mpaSkipWizardHandler' ) );
	}

	/**
	 *
	 * @return boolean
	 */
	public function checkCapabilities() {
		return current_user_can( 'manage_options' ) && current_user_can( 'publish_pages' );
	}

	/**
	 * Check if services are available to prevent start wizard
	 *
	 * @return boolean
	 */
	public function isAppointmentServiceAvailable() {
		$services = get_posts(
			array(
				'post_type'      => 'mpa_service',
				'posts_per_page' => 1,
			)
		);

		return ! empty( $services );
	}

	/**
	 * Check if wizard page is current
	 *
	 * @return boolean
	 */
	public function isMPAWizardPage() {
		if ( ! is_admin() ) {
			return false;
		}

		$currentScreen = get_current_screen();

		return $currentScreen && 'admin_page_mpa_wizard' === $currentScreen->id;
	}

	public function checkUserAction() {
		if ( isset( $_GET['mpa_wizard_action'] ) ) {
			switch ( $_GET['mpa_wizard_action'] ) {
				case 'skip':
					if ( isset( $_GET[ self::NONCE_NAME ] ) &&
						wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ self::NONCE_NAME ] ) ), self::NONCE_ACTION_SKIP ) ) {

						$this->completeWizard();
					}
					break;
			}
		}
	}

	/**
	 * Check if wizard is completed
	 *
	 * @return boolean
	 */
	public function isWizardCompleted() {
		return get_option( self::MPA_WIZARD_STATE_OPTION, false );
	}

	/**
	 * Complete Wizard
	 *
	 * @return void
	 */
	public function completeWizard() {
		add_option( self::MPA_WIZARD_STATE_OPTION, true, '', 'yes' );
	}


	/**
	 * Handle skip wizard
	 *
	 * @return void
	 */
	public function mpaSkipWizardHandler() {
		if ( ! isset( $_POST['mpa_wizard_nonce'] ) || ! wp_verify_nonce( $_POST['mpa_wizard_nonce'], 'mpa_wizard_nonce_action' ) ) {
			wp_send_json_error( 'Nonce verification failed.' );
		}

		$this->completeWizard();
		wp_send_json_success( array( 'redirect_url' => admin_url( 'admin.php?page=mpa_settings' ) ) );
	}

	/**
	 * Handle finish wizard
	 *
	 * @return void
	 */
	public function mpaFinishWizardHandler() {
		if ( ! isset( $_POST['mpa_wizard_nonce'] ) || ! wp_verify_nonce( $_POST['mpa_wizard_nonce'], 'mpa_wizard_nonce_action' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to do this action.', 'motopress-appointment' ) );
		}

		if ( ! isset( $_POST['location_name'], $_POST['employee_name'], $_POST['service_name'], $_POST['service_price'] ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to do this action.', 'motopress-appointment' ) );
		}

		if ( ! $this->checkCapabilities() ) {
			wp_send_json_error( esc_html__( 'You do not have permission to do this action.', 'motopress-appointment' ) );
		}

		$location_name = sanitize_text_field( $_POST['location_name'] );
		$employee_name = sanitize_text_field( $_POST['employee_name'] );
		$service_name  = sanitize_text_field( $_POST['service_name'] );

		$service_price = floatval( $_POST['service_price'] );
		// if negative, set price to 0
		$service_price = max( 0, $service_price );

		$should_create_page = rest_sanitize_boolean( $_POST['should_create_form_page'] );

		try {
			$location_id = $this->createOrGetPost( 'mpa_location', $location_name );

			if ( ! $location_id ) {
				throw new \Exception( __( 'Failed to create a location.', 'motopress-appointment' ) );
			}

			$employee_id = $this->createOrGetPost( 'mpa_employee', $employee_name );
			if ( ! $employee_id ) {
				throw new \Exception( __( 'Failed to create an employee.', 'motopress-appointment' ) );
			}

			$this->createEmployeeSchedule( $employee_id, $location_id );

			$service_id = $this->createService( $service_name, $service_price, $employee_id );
			if ( ! $service_id ) {
				throw new \Exception( __( 'Failed to create a service.', 'motopress-appointment' ) );
			}

			$formPage = false;

			if ( $service_id && $should_create_page ) {
				$formPage = $this->createFormPage();
			}

			// Update settings if provided
			if ( isset( $_POST['mpa_currency'] ) ) {
				update_option( 'mpa_currency', sanitize_text_field( $_POST['mpa_currency'] ) );
			}
			if ( isset( $_POST['mpa_currency_position'] ) ) {
				update_option( 'mpa_currency_position', sanitize_text_field( $_POST['mpa_currency_position'] ) );
			}
			if ( isset( $_POST['mpa_confirmation_mode'] ) ) {
				$confirmation_mode = sanitize_text_field( $_POST['mpa_confirmation_mode'] );
				update_option( 'mpa_confirmation_mode', $confirmation_mode );

				if ( 'payment' === $confirmation_mode ) {
					update_option( 'mpa_cash_payment_gateway_enable', 1 );
				}
			}

			// complete wizard
			$this->completeWizard();

			$payload = null;

			if ( $formPage ) {
				$payload = array(
					'form_page' => get_permalink( $formPage ),
				);
			}

			$this->sendResponse( true, $payload );

		} catch ( \Throwable $e ) {
			$this->sendResponse( false, $e->getMessage() );
		}
	}

	/**
	 * Send response
	 *
	 * @param boolean $success
	 * @param mixed $payload
	 *
	 * @return void
	 */
	private function sendResponse( $success, $payload = null ) {
		if ( $success ) {
			wp_send_json_success( $payload );
		} else {
			wp_send_json_error( $payload );
		}
	}

	/**
	 * Create or get post
	 *
	 * @param string $post_type
	 * @param string $post_title
	 *
	 * @return int
	 */
	private function createOrGetPost( $post_type, $post_title ) {
		$existing_post_id = $this->findExistingPost( $post_type, $post_title );

		if ( $existing_post_id ) {
			return $existing_post_id;
		}

		return wp_insert_post(
			array(
				'post_title'   => $post_title,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => $post_type,
			)
		);
	}

	/**
	 * Create employee schedule
	 *
	 * @param int $employee_id
	 * @param int $location_id
	 *
	 * @return void
	 */
	private function createEmployeeSchedule( $employee_id, $location_id ) {
		$schedule_id = $this->createOrGetPost( 'mpa_schedule', 'Schedule for ' . get_the_title( $employee_id ) );

		if ( $schedule_id && ! is_wp_error( $schedule_id ) ) {
			update_post_meta( $schedule_id, '_mpa_main_location', $location_id );
			update_post_meta( $schedule_id, '_mpa_employee', $employee_id );
			update_post_meta( $schedule_id, '_mpa_timetable', $this->getDefaultSchedule( $location_id ) );
		}

		return $schedule_id;
	}

	/**
	 * Find existing post
	 *
	 * @param string $post_type
	 * @param string $post_title
	 *
	 * @return void
	 */
	private function findExistingPost( $post_type, $post_title ) {
		$query = new \WP_Query(
			array(
				'post_type'      => $post_type,
				'title'          => $post_title,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			)
		);

		if ( $query->have_posts() ) {
			return $query->posts[0]->ID;
		}

		return false;
	}

	/**
	 * Get default schedule
	 *
	 * @param int $location_id
	 *
	 * @return array
	 */
	private function getDefaultSchedule( $location_id ) {
		$days     = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday' );
		$schedule = array();

		foreach ( $days as $day ) {
			$schedule[] = array(
				'day'      => $day,
				'start'    => 540, // 9:00 AM
				'end'      => 1080, // 6:00 PM
				'activity' => 'work',
				'location' => $location_id,
			);
		}

		return $schedule;
	}

	/**
	 * Create service
	 *
	 * @param string $service_name
	 * @param float  $service_price
	 * @param int    $employee_id
	 *
	 * @return void
	 */
	private function createService( $service_name, $service_price, $employee_id ) {
		$service_id = $this->createOrGetPost( 'mpa_service', $service_name );

		if ( $service_id && ! is_wp_error( $service_id ) ) {
			update_post_meta( $service_id, '_mpa_price', $service_price );
			update_post_meta( $service_id, '_mpa_duration', 60 );
			update_post_meta( $service_id, '_mpa_employees', array( $employee_id ) );
		}

		return $service_id;
	}

	/**
	 * Create form page
	 *
	 * @return void
	 */
	public function createFormPage() {

		$page_title   = 'Appointment form page';
		$page_content = '<!-- wp:motopress-appointment/appointment-form /-->';
		$page_slug    = 'appointment-form-page';

		// Check if the page already exists
		$existing_page = get_page_by_path( $page_slug );
		if ( $existing_page ) {
			return $existing_page->ID;
		}

		// Create the page
		$page_args = array(
			'post_title'   => $page_title,
			'post_content' => $page_content,
			'post_status'  => 'draft',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id(),
			'post_name'    => $page_slug,
		);

		$page_id = wp_insert_post( $page_args );

		return $page_id ? $page_id : false;
	}

	/**
	 * Render location form
	 *
	 * @return string
	 */
	public function renderWelcomeNotice() {

		$output  = '<h1>' . esc_html__( 'Welcome to MotoPress Appointment Booking!', 'motopress-appointment' ) . '</h1>';
		$output .= '<p>' . esc_html__(
			'It\'s great to have you here with us! We\'ll be guiding you through the setup process to help you get started easily.',
			'motopress-appointment'
		) . '</p>';

		return $output;
	}

	/**
	 * Render location form
	 *
	 * @return void
	 */
	public function renderLocationForm() {
		$fields = array(
			FieldsFactory::createField(
				'location_name',
				array(
					'type'     => 'text',
					'label'    => esc_html__( 'Location name', 'motopress-appointment' ),
					'required' => true,
				),
			),
		);

		$output  = '<h2>' . esc_html__( 'Where does an appointment take place?', 'motopress-appointment' ) . '</h2>';
		$output .= '<p>' . esc_html__( 'A location is where an appointment takes place, such as a city, area, building, room, or online. Adding a location is required, though you can choose to hide it in the booking form later.', 'motopress-appointment' ) . '</p>';
		$output .= mpa_render_template( 'private/fields/form-table.php', array( 'fields' => $fields ) );

		return $output;
	}

	/**
	 * Render employee form
	 *
	 * @return void
	 */
	public function renderEmployeeForm() {

		$fields = array(

			FieldsFactory::createField(
				'employee_name',
				array(
					'type'     => 'text',
					'label'    => esc_html__( 'Employee name', 'motopress-appointment' ),
					'required' => true,
				),
			),
		);

		$output  = '<h2>' . esc_html__( 'Who provides a service?', 'motopress-appointment' ) . '</h2>';
		$output .= '<p>' . esc_html__( 'An employee is the service provider responsible for the appointment, typically listed by name. Adding an employee is required, though you can choose to hide it in the booking form later.', 'motopress-appointment' ) . '</p>';
		$output .= mpa_render_template( 'private/fields/form-table.php', array( 'fields' => $fields ) );
		$output .= '<p class="description">' . esc_html__( 'A schedule for this employee is generated automatically and can be edited later in the Schedules menu.', 'motopress-appointment' ) . '</p>';

		return $output;
	}

	/**
	 * Render service form
	 *
	 * @return void
	 */
	public function renderServiceForm() {

		$fields = array(
			FieldsFactory::createField(
				'service_name',
				array(
					'type'     => 'text',
					'label'    => esc_html__( 'Service name', 'motopress-appointment' ),
					'required' => true,
				),
			),
			FieldsFactory::createField(
				'service_price',
				array(
					'type'     => 'number',
					'label'    => esc_html__( 'Service price', 'motopress-appointment' ),
					'min'      => 0,
					'default'  => 49,
					'size'     => 'small',
					'required' => true,
				),
			),
		);

		$output  = '<h2>' . esc_html__( 'Describe your service', 'motopress-appointment' ) . '</h2>';
		$output .= '<p>' . esc_html__( 'A service is the specific offering provided during an appointment. It can be a haircut, consultation, medical checkup, training session, or any other activity booked by a customer.', 'motopress-appointment' ) . '</p>';
		$output .= mpa_render_template( 'private/fields/form-table.php', array( 'fields' => $fields ) );

		return $output;
	}

	/**
	 * Render settings form
	 *
	 * @return void
	 */
	public function renderSettingsForm() {

		//  options with defaults
		if ( false !== get_option( 'mpa_currency', false ) ) {
			$getOptionCurrency = get_option( 'mpa_currency', false );
		} else {
			$getOptionCurrency = 'EUR';
		}
		if ( false !== get_option( 'mpa_currency_position', false ) ) {
			$getOptionCurrencyPosition = get_option( 'mpa_currency_position', false );
		} else {
			$getOptionCurrencyPosition = 'before';
		}
		if ( false !== get_option( 'mpa_confirmation_mode', false ) ) {
			$getOptionConfirmationMode = get_option( 'mpa_confirmation_mode', false );
		} else {
			$getOptionConfirmationMode = 'auto';
		}

		$fields = array(

			// misc
			FieldsFactory::createField(
				'mpa_currency',
				array(
					'type'    => 'select',
					'label'   => esc_html__( 'Currency', 'motopress-appointment' ),
					'options' => mpapp()->bundles()->currencies()->getCurrencies(),
					'size'    => 'regular',
				),
				$getOptionCurrency
			),
			FieldsFactory::createField(
				'mpa_currency_position',
				array(
					'type'    => 'select',
					'label'   => esc_html__( 'Currency Position', 'motopress-appointment' ),
					'options' => mpapp()->bundles()->currencies()->getPositions(),
					'default' => 'before',
					'size'    => 'regular',
				),
				$getOptionCurrencyPosition
			),

			// conf mode
			FieldsFactory::createField(
				'mpa_confirmation_mode',
				array(
					'type'    => 'radio',
					'label'   => esc_html__( 'Confirmation Mode', 'motopress-appointment' ),
					'options' => array(
						'auto'    => esc_html__( 'Confirm automatically', 'motopress-appointment' ),
						'manual'  => esc_html__( 'By admin manually', 'motopress-appointment' ),
						'payment' => esc_html__( 'Confirmation upon payment', 'motopress-appointment' ),
					),
				),
				$getOptionConfirmationMode
			),
			FieldsFactory::createField(
				'should_create_form_page',
				array(
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Booking Page', 'motopress-appointment' ),
					'label2'  => esc_html__( 'Create a draft website page with an appointment booking form.', 'motopress-appointment' ),
					'default' => true,
				)
			),

		);

		$output  = '<h2>' . esc_html__( 'General Settings', 'motopress-appointment' ) . '</h2>';
		$output .= '<p>' . esc_html__(
			'Define some key configurations for your appointment booking system. The Confirmation Mode option specifies how booking requests are approved.',
			'motopress-appointment'
		) . '</p>';
		$output .= mpa_render_template( 'private/fields/form-table.php', array( 'fields' => $fields ) );

		return $output;
	}

	/**
	 * Render location form
	 *
	 * @return string
	 */
	public function renderCompleteNotice() {
		$output  = '<h1>' . esc_html__( 'Setup Complete – Ready to Go!', 'motopress-appointment' ) . '</h1>';
		$output .= '<p>' . esc_html__( 'Your appointment system is set up with default settings and ready for bookings. You can now also add more services, employees, locations, and customize settings and schedules.', 'motopress-appointment' ) . '</p>';
		// translators: %1$s is Placeholder for the draft page link.
		$output .= '<p id="draft-page-link" style="display: none;">' . sprintf( esc_html__( 'Your draft page with the form: %1$s', 'motopress-appointment' ), '<a href="" target="_blank"></a>' ) . '</p>';
		return $output;
	}
}
