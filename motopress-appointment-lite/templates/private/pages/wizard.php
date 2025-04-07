<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$steps = array(
	0 => __( 'Welcome', 'motopress-appointment' ),
	1 => __( 'Location', 'motopress-appointment' ),
	2 => __( 'Employee', 'motopress-appointment' ),
	3 => __( 'Service', 'motopress-appointment' ),
	4 => __( 'Settings', 'motopress-appointment' ),
	5 => __( 'Complete', 'motopress-appointment' ),
);
?>
<div id="wizard-loader">
	<div class="wizard-spinner"></div>
</div>
<div class="mpa-wizard">

	<ul class="mpa-wizard-steps">
		<?php foreach ( $steps as $index => $stepName ) : ?>
			<li class="step-tab" data-step="<?php echo esc_attr( $index ); ?>">
				<?php echo esc_html( $stepName ); ?>
			</li>
			<div class="step-divider"></div>
		<?php endforeach; ?>
	</ul>

	<div class="mpa-wizard-content">
		<form id="mpa-wizard-form">
			<?php wp_nonce_field( 'mpa_wizard_nonce_action', 'mpa_wizard_nonce' ); ?>
			<?php foreach ( $steps as $index => $stepName ) : ?>
				<div class="mpa-wizard-step" id="step-<?php echo esc_attr( $index ); ?>">

					<?php
					switch ( $index ) {
						case 0:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderWelcomeNotice();
							break;
						case 1:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderLocationForm();
							break;
						case 2:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderEmployeeForm();
							break;
						case 3:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderServiceForm();
							break;
						case 4:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderSettingsForm();
							break;
						case 5:
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $wizard->renderCompleteNotice();
							break;
					}

					if ( $index < 5 ) :
						?>
					<div class="mpa-wizard-buttons">
						<?php if ( $index > 0 ) : ?>
							<button type="button" class="previous-step button-link"><?php esc_html_e( 'Back', 'motopress-appointment' ); ?></button>
						<?php endif; ?>
						<?php if ( $index < 4 ) : ?>
							<button type="button" class="next-step button-primary" <?php disabled( $index > 0 ); ?>><?php esc_html_e( 'Continue', 'motopress-appointment' ); ?></button>
						<?php endif; ?>
						<?php if ( 4 === $index ) : ?>
							<button type="button" class="finish button-primary"><?php esc_html_e( 'Finish Setup', 'motopress-appointment' ); ?></button>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</form>
	</div>
</div>
<div class="skip-wizard-button-wrapper">
	<button type="button" class="skip-wizard button-link"><?php esc_html_e( 'Skip Wizard', 'motopress-appointment' ); ?></button>
</div>
