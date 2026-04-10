<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Css_Filter;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon css filter vars control.
 *
 * Customized Elementor `css-filter` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Css_Filter extends Group_Control_Css_Filter {

	/**
	 * Get css filter control type.
	 *
	 * Retrieve the control type, in this case `css-filter`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_CSS_FILTER_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process css filter control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @param array $fields css filter control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . str_replace( '_', '-', $this->get_controls_prefix() );

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			switch ( $field_name ) {
				case 'blur':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}css-filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg );",
					);

					break;
			}
		} );

		return parent::prepare_fields( $fields );
	}

}
