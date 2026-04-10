<?php
namespace CmsmastersElementor\Controls;

use CmsmastersElementor\Controls_Manager;

use Elementor\Control_Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Control_Locations_Repeater extends Control_Repeater {

	public function get_type() {
		return Controls_Manager::LOCATIONS_REPEATER;
	}

	protected function get_default_settings() {
		$settings = parent::get_default_settings();

		$settings['item_actions'] = array(
			'add' => true,
			'remove' => true,
		);

		$settings['prevent_empty'] = false;
		$settings['render_type'] = 'none';

		return $settings;
	}

	/**
	 * Render repeater control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		?>
		<label>
			<span class="elementor-control-title">{{{ data.label }}}</span>
		</label>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<div class="elementor-repeater-fields-wrapper"></div>
		<div class="elementor-repeater-fields-placeholder elementor-descriptor">
			<div class="elementor-panel-alert elementor-panel-alert-warning">{{{ data.placeholder }}}</div>
		</div>
		<# if ( ( itemActions && itemActions.add ) || ( item_actions && item_actions.add ) ) { #>
			<div class="elementor-button-wrapper">
				<button class="elementor-button elementor-button-default elementor-repeater-exception" disabled type="button"><?php
					echo __( 'Add Exception', 'cmsmasters-elementor' );
				?></button>
				<button class="elementor-button elementor-button-default elementor-repeater-add" type="button">
					<i class="eicon-plus" aria-hidden="true"></i><?php echo __( 'Add New Rule', 'cmsmasters-elementor' ); ?>
				</button>
			</div>
		<# } #>
		<?php
	}

}
