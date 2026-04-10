<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Toolbar extends Base_Field {

	public function get_name() {
		return 'toolbar';
		// return Fields_Manager::TOOLBAR;
	}

	public function get_field( $parameters ) {
		return Utils::get_if_isset( $parameters, 'raw_html' );
	}

}
