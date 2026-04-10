<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Facebook social counter class.
 *
 * @since 1.0.0
 */
class Facebook extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'facebook';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Facebook', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-facebook',
			'library' => 'fa-brands',
		);
	}

	/**
	 * Get types.
	 *
	 * @since 1.0.0
	 *
	 * @return array Types.
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'likes' => __( 'Likes', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected static function get_cache_expire() {
		return HOUR_IN_SECONDS * 24;
	}

	/**
	 * Get numbers remote.
	 *
	 * @since 1.0.0
	 * @since 1.7.5 Fixed social counter.
	 *
	 * @return string/integer Numbers.
	 */
	protected function get_numbers_remote() {
		$page_id = get_option( 'elementor_facebook_page_id' );
		$access_token = get_option( 'elementor_facebook_access_token' );

		if ( empty( $page_id ) || empty( $access_token ) ) {
			return;
		}

		$url = add_query_arg(
			array(
				'fields' => 'fan_count,followers_count',
				'access_token' => $access_token,
			),
			"https://graph.facebook.com/v16.0/{$page_id}"
		);

		$result = static::get_result_json( $url );

		if ( ! $result ) {
			return;
		}

		$numbers = 0;

		switch ( $this->get_type() ) {
			case 'likes':
				if ( ! empty( $result['fan_count'] ) ) {
					$numbers = $result['fan_count'];
				}

				break;
			case 'followers':
				if ( ! empty( $result['followers_count'] ) ) {
					$numbers = $result['followers_count'];
				}

				break;
		}

		return $numbers;
	}

	/**
	 * Check if before render.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public function check_pre_show() {
		return ( ! empty( get_option( 'elementor_facebook_page_id' ) ) && ! empty( get_option( 'elementor_facebook_access_token' ) ) && $this->get_type() );
	}

}
