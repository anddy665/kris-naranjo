<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Reviews extends Base_Widget {

	use Woo_Singular_Widget;

	protected $_has_template_content = false; //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product Reviews', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-product-review';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'reviews',
			'comments',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-woocommerce',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register widget skins.
	 *
	 * This method is activated while initializing the widget base class.
	 * It is used to assign skins to widgets with `add_skin()` method.
	 *
	 * @since 1.0.0
	 * @since 1.3.8 replacing `_register_skins()` with `register_skins()`.
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Product_Reviews_Content_Template( $this ) );
		$this->add_skin( new Skins\Product_Reviews_Custom( $this ) );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Reviews', 'cmsmasters-elementor' ) )
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'custom_navigation_text_next',
				'type' => esc_html__( 'Navigation Text Next', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_navigation_text_previous',
				'type' => esc_html__( 'Navigation Text Previous', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_author_text_after',
				'type' => esc_html__( 'Author Suffix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_date_time_separator_text',
				'type' => esc_html__( 'Time Separator', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_add_review_text',
				'type' => esc_html__( ' Add Review Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_review_text',
				'type' => esc_html__( 'Review', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_name_text',
				'type' => esc_html__( 'Name', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_email_text',
				'type' => esc_html__( 'Email', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_submit_button_text',
				'type' => esc_html__( 'Submit Button', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
