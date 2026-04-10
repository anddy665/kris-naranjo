<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Soundcloud social counter class.
 *
 * @since 1.0.0
 */
class Soundcloud extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'soundcloud';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Soundcloud', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-soundcloud',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'tracks' => __( 'Tracks', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$result = self::get_result( $this->get_profile_url() );

		if ( ! $result ) {
			return $result;
		}

		switch ( $this->get_type() ) {
			case 'followers':
				$meta_property = 'follower_count';

				break;
			case 'tracks':
				$meta_property = 'sound_count';

				break;
		}

		$pattern = "/<meta property=\"soundcloud:{$meta_property}\" content=\"(.*?)\">/";

		preg_match( $pattern, $result, $matches );

		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}
	}
}
