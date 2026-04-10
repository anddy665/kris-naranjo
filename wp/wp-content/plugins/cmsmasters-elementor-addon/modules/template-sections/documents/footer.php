<?php
namespace CmsmastersElementor\Modules\TemplateSections\Documents;

use CmsmastersElementor\Modules\TemplateSections\Documents\Base\Header_Footer_Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters footer document.
 *
 * An class that provides the needed properties and methods to
 * manage and handle footer documents in inheriting classes.
 *
 * @since 1.0.0
 */
class Footer extends Header_Footer_Document {

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_footer';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Footer', 'cmsmasters-elementor' );
	}

}
