<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Dribbble social counter class.
 *
 * @since 1.0.0
 */
class Dribbble extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'dribbble';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Dribbble', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-dribbble',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'shots' => __( 'Shots', 'cmsmasters-elementor' ),
			'likes' => __( 'Likes', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		$type = $this->get_type();
		$url = $this->get_profile_url();

		switch ( $type ) {
			case 'followers':
				$url .= '/about';

				break;
		}

		$result = self::get_result( $url );

		if ( ! $result ) {
			return;
		}

		switch ( $type ) {
			case 'followers':
				$xpath = '//*[contains(concat(" ",normalize-space(@class)," ")," about-content-main ")]//a[@href="/' . $this->get_username() . '/following"]//*[contains(concat(" ",normalize-space(@class)," ")," count ")]';

				break;
			case 'shots':
				$xpath = '//*[contains(concat(" ",normalize-space(@class)," ")," profile-subnav-menu ")]//*[contains(concat(" ",normalize-space(@class)," ")," shots ")]//*[contains(concat(" ",normalize-space(@class)," ")," count ")]';

				break;
			case 'likes':
				$xpath = '//*[contains(concat(" ",normalize-space(@class)," ")," profile-subnav-menu ")]//*[contains(concat(" ",normalize-space(@class)," ")," liked ")]//*[contains(concat(" ",normalize-space(@class)," ")," count ")]';

				break;
		}

		return $this->get_html_by_xpath( $result, $xpath );
	}
}
