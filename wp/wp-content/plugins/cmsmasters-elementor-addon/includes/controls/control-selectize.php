<?php
namespace CmsmastersElementor\Controls;

use CmsmastersElementor\Controls_Manager;

use Elementor\Base_Data_Control;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Control_Selectize extends Base_Data_Control {

	public function get_type() {
		return Controls_Manager::SELECTIZE;
	}

	protected function get_default_settings() {
		$default_settings = parent::get_default_settings();

		$default_settings = array_merge( $default_settings, array(
			'label_block' => true,
			'options' => array(),
			'multiple' => false,
			'control_options' => array(
				'plugins' => array(
					'remove_button',
					'drag_drop',
				),
			),
		) );

		return $default_settings;
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();

		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">

				<# var multiple = ( data.multiple ) ? ' multiple' : ''; #>
				<select id="<?php echo esc_attr( $control_uid ); ?>" class="data-options"{{ multiple }} data-setting="{{ data.name }}">
					<#
					var printOptions = function( options ) {
						optionsSorted = _.union( data.controlValue, Object.keys( options ) );

						_.each( optionsSorted, function( value ) {
							if ( ! options[ value ] ) {
								return;
							}

							var selectedCondition = false,
								selected = '';

							if ( _.isString( data.controlValue ) ) {
								selectedCondition = value === data.controlValue;
							} else if ( ! _.isNull( data.controlValue ) ) {
								selectedCondition = -1 !== _.values( data.controlValue ).indexOf( value );
							}

							if ( selectedCondition ) {
								selected = ' selected';
							}
							#>
							<option value="{{ value }}"{{ selected }}>{{{ options[ value ] }}}</option>
							<#
						} );
					};

					if ( data.groups ) {
						for ( var groupIndex in data.groups ) {
							var groupArgs = data.groups[ groupIndex ];

							if ( groupArgs.options ) { #>
								<optgroup label="{{ groupArgs.label }}">
									<# printOptions( groupArgs.options ); #>
								</optgroup>
							<# }
						}
					} else {
						printOptions( data.options );
					}
					#>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
