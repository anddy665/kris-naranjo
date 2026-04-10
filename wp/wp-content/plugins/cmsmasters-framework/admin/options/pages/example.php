<?php
namespace CmsmastersFramework\Admin\Options\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Example handler class is responsible for different methods on example theme options page.
 *
 * @since 1.0.0
 */
class Example extends Base\Base_Page {

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'Theme Example Options', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'Example', 'cmsmasters-framework' );
	}

	/**
	 * Default section.
	 *
	 * @since 1.0.0
	 */
	public $default_section = 'main';

	/**
	 * Get sections.
	 *
	 * @since 1.0.0
	 */
	public function get_sections() {
		return array(
			'main' => array(
				'label' => esc_attr__( 'Main', 'cmsmasters-framework' ),
				'title' => esc_attr__( 'Main Options', 'cmsmasters-framework' ),
			),
			'second' => array(
				'label' => esc_attr__( 'Second', 'cmsmasters-framework' ),
				'title' => esc_html__( 'Second Options', 'cmsmasters-framework' ),
			),
			'third' => array(
				'label' => esc_attr__( 'Third', 'cmsmasters-framework' ),
				'title' => esc_html__( 'Third Options', 'cmsmasters-framework' ),
			),
		);
	}

	/**
	 * Get fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Current section.
	 *
	 * @return array Fields.
	 */
	public function get_fields( $section = '' ) {
		$fields = array();

		switch ( $section ) {
			case 'main':
				$fields['test_arr_field|first'] = array(
					'title' => esc_html__( 'Arr Text Field First', 'cmsmasters-framework' ),
					'desc' => 'descriptions',
					'type' => 'text',
					'subtype' => 'email',
					'std' => '',
				);

				$fields['test_arr_field|second'] = array(
					'title' => esc_html__( 'Arr Text Field Second', 'cmsmasters-framework' ),
					'desc' => 'descriptions',
					'type' => 'text',
					'std' => '',
				);

				$fields['test_text_field'] = array(
					'title' => esc_html__( 'Test Text Field', 'cmsmasters-framework' ),
					'desc' => 'descriptions',
					'type' => 'text',
					'std' => '',
					'class' => 'nohtml',
				);

				$fields['test_second_field'] = array(
					'title' => esc_html__( 'Test Second Field', 'cmsmasters-framework' ),
					'desc' => 'descriptions',
					'type' => 'text',
					'subtype' => 'email',
					'std' => '',
					'class' => 'nohtml',
				);

				break;
			case 'second':
				$fields['test_third_field'] = array(
					'title' => esc_html__( 'Test third Field', 'cmsmasters-framework' ),
					'desc' => 'descriptions',
					'type' => 'text',
					'std' => '',
					'class' => 'nohtml',
				);

				break;
		}

		return $fields;
	}

}
