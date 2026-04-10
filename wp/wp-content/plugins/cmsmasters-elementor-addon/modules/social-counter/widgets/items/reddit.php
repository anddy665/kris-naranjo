<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Reddit social counter class.
 *
 * @since 1.0.0
 */
class Reddit extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'reddit';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Reddit', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-reddit',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'members' => __( 'Members', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$result = static::get_result_json( "{$this->get_profile_url()}/about.json" );

		if ( ! $result ) {
			return;
		}

		if ( empty( $result['data']['subscribers'] ) ) {
			return;
		}

		return $result['data']['subscribers'];
	}
}
