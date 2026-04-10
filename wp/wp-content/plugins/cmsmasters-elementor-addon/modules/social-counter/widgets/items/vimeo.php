<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Vimeo social counter class.
 *
 * @since 1.0.0
 */
class Vimeo extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'vimeo';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'Vimeo', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-vimeo',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'videos' => __( 'Videos', 'cmsmasters-elementor' ),
			'likes' => __( 'Likes', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_numbers_remote() {
		switch ( $this->get_type() ) {
			case 'followers':
				$xpath = '//a[contains(@href,"following/followers")]';
				$url_relative = '/following/followers/';

				break;
			case 'videos':
				$xpath = '//a[contains(@href,"videos/sort")]';
				$url_relative = '/videos';

				break;
			case 'likes':
				$xpath = '//a[contains(@href,"/likes")]//p[contains(concat(" ",normalize-space(@class)," ")," super_link_list_title ")]';
				$url_relative = '/likes';

				break;
		}

		$result = self::get_result( $this->get_profile_url() . $url_relative );

		if ( ! $result ) {
			return;
		}

		$dom_node_list = $this->get_html_by_xpath( $result, $xpath );

		if ( ! $dom_node_list ) {
			return;
		}

		return preg_replace( '/\D/', '', $dom_node_list );
	}
}
