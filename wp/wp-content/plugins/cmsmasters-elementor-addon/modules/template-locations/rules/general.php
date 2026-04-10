<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Rule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * General template locations rule class.
 *
 * @since 1.0.0
 * @since 1.0.1 Methods get_group & get_priority moved to base class.
 */
class General extends Base_Rule {

	protected $child_rules = array(
		'archive',
		'singular',
	);

	public function get_name() {
		return 'general';
	}

	public function get_title() {
		return __( 'General', 'cmsmasters-elementor' );
	}

	public function get_multiple_title() {
		return __( 'Sitewide', 'cmsmasters-elementor' );
	}

	public function verify_expression() {
		return true;
	}

}
