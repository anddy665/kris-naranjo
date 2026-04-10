<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Images extends Base_Widget {

	use Woo_Singular_Widget;

	protected $_has_template_content = false; //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore


	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product Images', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-product-images';
	}

	/**
	 * Get unique widget keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'image',
			'gallery',
			'lightbox',
		);
	}

	/**
	 * Get script depends.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get script depends.
	 */
	public function get_script_depends() {
		return array( 'zoom', 'wc-single-product' );
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
		$this->add_skin( new Skins\Product_Images_Slider( $this ) );
		$this->add_skin( new Skins\Product_Images_Grid( $this ) );
		$this->add_skin( new Skins\Product_Images_Anchor( $this ) );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'General', 'cmsmasters-elementor' ) )
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
}
