<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Pinterest social counter class.
 *
 * @since 1.0.0
 */
class Pinterest extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'pinterest';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Pinterest', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-pinterest',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$result = self::get_result( $this->get_profile_url() );

		if ( ! $result ) {
			return;
		}

		$pattern = '/name="pinterestapp:followers" content="(.*?)"/';

		preg_match( $pattern, $result, $matches );

		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}
	}
}
