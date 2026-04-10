<?php
namespace CmsmastersElementor\Upgrader\Versions;

use CmsmastersElementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Version upgrade.
 *
 * @since 1.11.2
 */
class Version_1_11_2 {

	/**
	 * Version upgrade constructor.
	 *
	 * @since 1.11.2
	 */
	public function __construct() {
		add_action( 'admin_init', function() {
			Utils::rewrite_widgets_external_media_url( 'image' );
			Utils::rewrite_widgets_external_media_url( 'media' );
		}, 200 );
	}

}
