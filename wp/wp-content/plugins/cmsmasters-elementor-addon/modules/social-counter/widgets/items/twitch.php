<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Twitch social counter class.
 *
 * @since 1.0.0
 */
class Twitch extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'twitch';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Twitch', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-twitch',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	private static function get_remote_args() {
		return array(
			'headers' => array(
				'Client-ID' => get_option( 'elementor_twitch_client_id' ),
				'Authorization' => 'Bearer ' . get_option( 'elementor_twitch_access_token' ),
			),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_user_id() {
		$username = $this->get_username();

		if ( ! $username ) {
			return;
		}

		$user = static::get_result_json( "https://api.twitch.tv/helix/users?login={$username}", self::get_remote_args() );

		if ( empty( $user['data'][0]['id'] ) ) {
			return;
		}

		return $user['data'][0]['id'];
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$user_id = $this->get_user_id();

		if ( ! $user_id ) {
			return;
		}

		$result = static::get_result_json( "https://api.twitch.tv/helix/users/follows?to_id={$user_id}", self::get_remote_args() );

		if ( empty( $result['total'] ) ) {
			return;
		}

		return $result['total'];
	}
}
