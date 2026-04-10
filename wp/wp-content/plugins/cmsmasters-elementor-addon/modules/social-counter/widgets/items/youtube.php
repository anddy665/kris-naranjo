<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Youtube social counter class.
 *
 * @since 1.0.0
 */
class Youtube extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'youtube';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'YouTube', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-youtube',
			'library' => 'fa-brands',
		);
	}

	/**
	 * Check if is user.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_url_user() {
		return (bool) preg_match( '/\/user\//', $this->get_profile_url() );
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$username = $this->get_username();
		$parameters = array(
			'key' => get_option( 'elementor_google_api_key' ),
			'part' => 'statistics',
			'fields' => 'items/statistics/subscriberCount',
		);

		if ( $this->is_url_user() ) {
			$parameters['forUsername'] = $username;
		} else {
			$parameters['id'] = $username;
		}

		$result = static::get_result_json( add_query_arg(
			$parameters,
			'https://www.googleapis.com/youtube/v3/channels'
		) );

		if ( empty( $result['items'][0]['statistics']['subscriberCount'] ) ) {
			return false;
		}

		return $result['items'][0]['statistics']['subscriberCount'];
	}
}
