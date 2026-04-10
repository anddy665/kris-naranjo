<?php
namespace CmsmastersFramework\Admin\Options\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Image_Sizes handler class is responsible for different methods on image-sizes theme options page.
 *
 * @since 1.0.0
 */
class Image_Sizes extends Base\Base_Page {

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'Image Sizes', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'Image Sizes', 'cmsmasters-framework' );
	}

	/**
	 * Get sections.
	 *
	 * @since 1.0.0
	 */
	public function get_sections() {
		return array(
			'main' => array(
				'label' => '',
				'title' => '',
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
				$image_sizes_items = array(
					'width' => array(
						'label' => esc_html__( 'Width', 'cmsmasters-framework' ),
						'type' => 'number',
						'subtype' => 'email',
						'not_empty' => true,
						'min' => '1',
						'max' => '9999',
						'step' => '1',
						'postfix' => 'px',
					),
					'height' => array(
						'label' => esc_html__( 'Height', 'cmsmasters-framework' ),
						'type' => 'number',
						'not_empty' => true,
						'min' => '1',
						'max' => '9999',
						'step' => '1',
						'postfix' => 'px',
					),
					'crop' => array(
						'label' => esc_html__( 'Crop', 'cmsmasters-framework' ),
						'type' => 'checkbox',
					),
				);

				$fields['image_sizes|archive'] = array(
					'title' => esc_html__( 'Archive Image Size', 'cmsmasters-framework' ),
					'desc' => esc_html__( 'Used for the featured image in blog/archive template.', 'cmsmasters-framework' ),
					'type' => 'constructor',
					'items' => $image_sizes_items,
				);

				$fields['image_sizes|search'] = array(
					'title' => esc_html__( 'Search Image Size', 'cmsmasters-framework' ),
					'desc' => esc_html__( 'Used for the featured image in search template.', 'cmsmasters-framework' ),
					'type' => 'constructor',
					'items' => $image_sizes_items,
				);

				$fields['image_sizes|single'] = array(
					'title' => esc_html__( 'Single Image Size', 'cmsmasters-framework' ),
					'desc' => esc_html__( 'Used for the featured image in single post template.', 'cmsmasters-framework' ),
					'type' => 'constructor',
					'items' => $image_sizes_items,
				);

				$fields['image_sizes|more-posts'] = array(
					'title' => esc_html__( 'More Posts Image Size', 'cmsmasters-framework' ),
					'desc' => esc_html__( 'Used for the featured image in more posts block.', 'cmsmasters-framework' ),
					'type' => 'constructor',
					'items' => $image_sizes_items,
				);

				break;
		}

		return $fields;
	}

}
