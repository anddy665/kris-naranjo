<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon behance social counter class.
 *
 * @since 1.0.0
 */
class Behance extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'behance';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Behance', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-behance',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'appreciations' => __( 'Appreciations', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$url = add_query_arg(
			array(
				'client_id' => get_option( 'elementor_behance_api_key' ),
			),
			"https://api.behance.net/v2/users/{$this->get_username()}"
		);

		$result = static::get_result_json( $url );

		if ( ! $result ) {
			return;
		}

		switch ( $this->get_type() ) {
			case 'followers':
				if ( ! empty( $result['user']['stats']['followers'] ) ) {
					return $result['user']['stats']['followers'];
				}

				break;
			case 'appreciations':
				if ( ! empty( $result['user']['stats']['appreciations'] ) ) {
					return $result['user']['stats']['appreciations'];
				}

				break;
		}

		return false;
	}

}
