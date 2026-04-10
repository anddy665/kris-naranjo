<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Base;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Rule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


abstract class Base_Archive extends Base_Rule {

	/**
	 * @since 1.0.0
	 */
	final public static function get_group() {
		return 'archive';
	}

}
