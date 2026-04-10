<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon WooCommerce `shopping cart` widget.
 *
 * Addon widget that display WooCommerce shopping cart.
 *
 * @since 1.0.0
 */
class Cart extends Base_Widget {

	use Woo_Widget;

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

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
		return __( 'Dynamic Cart', 'cmsmasters-elementor' );
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
		return 'cmsicon-woo-cart';
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
			'product',
			'cart',
			'canvas',
		);
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array(
			Base_Document::SITE_WIDGETS_CATEGORY,
			Base_Document::WOO_WIDGETS_CATEGORY,
		);
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed applying border none controls. Fixed `Gap Between` for Cart Products.
	 * Added `Spacing Top` control for subtotal.
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		$script_depends = parent::get_script_depends();

		$script_depends[] = 'perfect-scrollbar-js';

		return $script_depends;
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.10.0 Added `Color`, `Background`, `Border Color`, `Border Radius` and `Box Shadow` controls for button counter in Dynamic Cart widget.
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.10.1 Added `Size`, `Spacing` and `Min Size` controls for trigger image. Added top icon position in `Position` control.
	 * Added `Background Color` control for cart type canvas.
	 * @since 1.16.4 Added `Color` and `Gap` controls for subtotal amount.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_main_settings',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'cart_type',
			array(
				'label' => __( 'Type of Cart', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
						'description' => 'Link type of cart',
					),
					'popup' => array(
						'title' => __( 'Popup', 'cmsmasters-elementor' ),
						'description' => 'Popup type of cart',
					),
					'canvas' => array(
						'title' => __( 'Canvas', 'cmsmasters-elementor' ),
						'description' => 'Canvas type of cart',
					),
				),
				'toggle' => false,
				'default' => 'popup',
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-woo-cart-type-',
			)
		);

		$this->add_control(
			'canvas_position',
			array(
				'label' => __( 'OffCanvas Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'frontend_available' => true,
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->add_control(
			'cart_url',
			array(
				'label' => __( 'Cart Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => wp_make_link_relative( wc_get_cart_url() ),
				'render_type' => 'template',
				'condition' => array( 'cart_type' => 'link' ),
			)
		);

		$this->add_control(
			'show_cart_on',
			array(
				'label' => __( 'Show Cart on', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'hover' => array(
						'title' => __( 'Hover', 'cmsmasters-elementor' ),
						'description' => 'Show cart by hover',
					),
					'click' => array(
						'title' => __( 'Click', 'cmsmasters-elementor' ),
						'description' => 'Show cart by click',
					),
				),
				'toggle' => false,
				'default' => 'hover',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array( 'cart_type' => 'popup' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_settings',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'render_type' => 'template',
				'selectors' => array(
					'.elementor-widget-cmsmasters-woo-cart__button-container' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Cart has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Cart has only text',
					),
					'text-icon' => array(
						'title' => __( 'Text and Icon', 'cmsmasters-elementor' ),
						'description' => 'Cart has text and icon',
					),
				),
				'toggle' => false,
				'default' => 'text-icon',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-woo-cart-button-',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Cart', 'cmsmasters-elementor' ),
				'condition' => array( 'button_style!' => 'icon' ),
			)
		);

		$this->add_control(
			'button_icon_type',
			array(
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Icon Type', 'cmsmasters-elementor' ),
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'icon' => 'eicon-star',
					),
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'icon' => 'eicon-image-bold',
					),
				),
				'default' => 'icon',
				'condition' => array( 'button_style!' => 'text' ),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'button_icon_type' => 'icon',
					'button_style!' => 'text',
				),
			)
		);

		$this->add_control(
			'button_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'button_icon_type' => 'image',
					'button_style!' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'button_image',
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'default' => 'full',
				'condition' => array(
					'button_icon_type' => 'image',
					'button_style!' => 'text',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'frontend_available' => true,
				'condition' => array( 'button_style' => 'text-icon' ),
			)
		);

		$this->add_responsive_control(
			'counter',
			array(
				'label' => __( 'Counter', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
						'description' => 'Hide counter',
					),
					'absolute' => array(
						'title' => __( 'Absolute', 'cmsmasters-elementor' ),
						'description' => 'Absolute position counter',
					),
					'relative' => array(
						'title' => __( 'Relative', 'cmsmasters-elementor' ),
						'description' => 'Relative position counter',
					),
				),
				'default' => 'absolute',
				'toggle' => false,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'counter_type',
			array(
				'label' => __( 'Counter Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'plain' => array(
						'title' => __( 'Plain', 'cmsmasters-elementor' ),
					),
					'after' => array(
						'title' => __( 'After Button', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'plain',
				'toggle' => false,
				'condition' => array( 'counter' => 'relative' ),
			)
		);

		$this->add_control(
			'counter_position',
			array(
				'label' => __( 'Counter Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'frontend_available' => true,
				'condition' => array(
					'counter' => 'relative',
					'counter_type' => 'plain',
				),
			)
		);

		$this->add_control(
			'empty',
			array(
				'label' => __( 'Hide Empty Counter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => '',
				'prefix_class' => 'cmsmasters-woo-cart-hide-empty-',
				'condition' => array(
					'counter!' => 'none',
				),
			)
		);

		$this->add_control(
			'subtotal',
			array(
				'label' => __( 'Subtotal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_advance_settings',
			array(
				'label' => __( 'Advance', 'cmsmasters-elementor' ),
				'condition' => array( 'cart_type!' => 'link' ),
			)
		);

		$this->add_control(
			'cart_title',
			array(
				'label' => __( 'Cart Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Title Text', 'cmsmasters-elementor' ),
				'condition' => array( 'show_cart_on!' => 'none' ),
			)
		);

		$this->add_control(
			'cart_message',
			array(
				'label' => __( 'Cart Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'Cart Message...', 'cmsmasters-elementor' ),
				'condition' => array( 'show_cart_on!' => 'none' ),
			)
		);

		$this->add_control(
			'overlay_close',
			array(
				'label' => __( 'Close by overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->add_control(
			'esc_close',
			array(
				'label' => __( 'Close by ESC', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->add_control(
			'close_show',
			array(
				'label' => __( 'Close Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->add_control(
			'disable_scroll',
			array(
				'label' => __( 'Disable page scroll', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_close_settings',
			array(
				'label' => __( 'Close Button', 'cmsmasters-elementor' ),
				'condition' => array(
					'cart_type' => 'canvas',
					'show_cart_on!' => 'none',
					'close_show' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'close_position',
			array(
				'label' => __( 'Close Button Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inside' => array(
						'title' => __( 'Inside', 'cmsmasters-elementor' ),
						'description' => 'Button will be inside box',
					),
					'outside' => array(
						'title' => __( 'Outside', 'cmsmasters-elementor' ),
						'description' => 'Button will be outside box',
					),
				),
				'default' => 'inside',
				'toggle' => false,
			)
		);

		$this->add_control(
			'close_content',
			array(
				'label' => __( 'Close Content', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Close button has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Close button has only text',
					),
					'icon-text' => array(
						'title' => __( 'Icon and Text', 'cmsmasters-elementor' ),
						'description' => 'Close button has icon and text',
					),
				),
				'default' => 'icon',
				'toggle' => false,
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'close_alignment',
			array(
				'label' => __( 'Close Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => '',
				'condition' => array(
					'close_position' => 'inside',
				),
			)
		);

		$this->add_control(
			'icon_close_position',
			array(
				'label' => __( 'Close Icon Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'condition' => array( 'close_content' => 'icon-text' ),
			)
		);

		$this->add_control(
			'close_label',
			array(
				'label' => __( 'Close Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Label Trigger',
				'default' => 'Close',
				'condition' => array( 'close_content!' => 'icon' ),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label' => __( 'Close Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'far fa-times-circle',
					'library' => 'fa-regular',
				),
				'recommended' => array(
					'fa-regular' => array(
						'times-circle',
						'window-close',
					),
					'fa-solid' => array(
						'window-close',
					),
				),
				'label_block' => true,
				'file' => '',
				'condition' => array( 'close_content!' => 'text' ),
			)
		);

		$this->add_control(
			'close_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'close_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
				),
				'default' => 'square',
				'condition' => array( 'close_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'close_icon_gap',
			array(
				'label' => __( 'Icon Close Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'default' => array(
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-close .elementor-widget-cmsmasters-woo-cart__cart-close-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-close.cmsmasters-icon-right .elementor-widget-cmsmasters-woo-cart__cart-close-icon' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				),
				'condition' => array( 'close_content' => 'icon-text' ),
			)
		);

		$this->add_responsive_control(
			'close_size',
			array(
				'label' => __( 'Close Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 20,
					),
				),
				'default' => array(
					'size' => 40,
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_content' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'close_height',
			array(
				'label' => __( 'Close Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 20,
					),
				),
				'default' => array(
					'size' => 40,
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-close' => 'width: max-content; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_content!' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'close_padding',
			array(
				'label' => __( 'Close Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'allowed_dimensions' => 'horizontal',
				'default' => array(
					'left' => '10',
					'right' => '10',
				),
				'placeholder' => array(
					'top' => 'auto',
					'right' => '',
					'bottom' => 'auto',
					'left' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-close' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
				),
				'condition' => array( 'close_content!' => 'icon' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_section_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content',
				'condition' => array( 'button_style!' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'button_tab_style' );

		$this->start_controls_tab(
			'button_tab_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon svg path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'button_border_border!' => '' ),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-icon path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'button_border_border!' => '' ),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_text_spacing',
			array(
				'label' => __( 'Subtotal Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-text' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'button_style!' => 'icon',
					'subtotal' => 'true',
				),
			)
		);

		$this->add_control(
			'button_image_heading',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'button_style!' => 'text',
					'button_icon_type' => 'image',
				),
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'button_style!' => 'text',
					'button_icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon svg path' => 'fill: {{VALUE}} !important;',
				),
				'condition' => array(
					'button_style!' => 'text',
					'button_icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-icon svg' => 'fill: {{VALUE}} !important;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-icon svg path' => 'fill: {{VALUE}} !important;',
				),
				'condition' => array(
					'button_style!' => 'text',
					'button_icon_type' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon svg' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-icon path' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-image img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_style!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'button_icon_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-right .elementor-widget-cmsmasters-woo-cart__button-icon-wrap' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: auto;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-left .elementor-widget-cmsmasters-woo-cart__button-icon-wrap' => 'margin-left: auto; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-top .elementor-widget-cmsmasters-woo-cart__button-icon-wrap' => 'margin-left: auto; margin-right: auto; margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_style' => 'text-icon' ),
			)
		);

		$this->add_control(
			'button_counter_heading',
			array(
				'label' => __( 'Counter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'counter_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter',
			)
		);

		$this->start_controls_tabs( 'button_counter_tab_style' );

		$this->start_controls_tab(
			'button_counter_tab_style_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_counter_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'color: {{VALUE}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_control(
			'button_counter_bg',
			array(
				'label' => __( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter:before' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'counter_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'counter_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_counter_tab_style_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_counter_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter' => 'color: {{VALUE}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_control(
			'button_counter_bg_hover',
			array(
				'label' => __( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter:before' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_control(
			'button_counter_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'counter!' => 'none',
					'counter_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'counter_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'counter_box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content:hover .elementor-widget-cmsmasters-woo-cart__button-counter',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_counter_size',
			array(
				'label' => __( 'Min Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 16,
						'max' => 30,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-counter-absolute .elementor-widget-cmsmasters-woo-cart__button-counter' => '--cmsmasters-button-counter-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'counter' => 'absolute' ),
			)
		);

		$this->add_responsive_control(
			'counter_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'button_counter_spacing',
			array(
				'label' => __( 'Counter Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-counter-position-left .elementor-widget-cmsmasters-woo-cart__button-counter' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-counter-position-right .elementor-widget-cmsmasters-woo-cart__button-counter' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'counter' => 'relative' ),
			)
		);

		$this->add_responsive_control(
			'button_counter_spacing_vertical',
			array(
				'label' => __( 'Spacing Vertical', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'counter' => 'absolute' ),
			)
		);

		$this->add_responsive_control(
			'button_counter_spacing_horizontal',
			array(
				'label' => __( 'Spacing Horizontal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-right .elementor-widget-cmsmasters-woo-cart__button-counter' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-left .elementor-widget-cmsmasters-woo-cart__button-counter' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content.cmsmasters-icon-position-top .elementor-widget-cmsmasters-woo-cart__button-counter' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
				'condition' => array( 'counter' => 'absolute' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'counter_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__button-content .elementor-widget-cmsmasters-woo-cart__button-counter',
				'condition' => array( 'counter!' => 'none' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_section_style',
			array(
				'label' => __( 'Cart', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'cart_type!' => 'link',
					'show_cart_on!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'cart_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-woo-cart-bg: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'offcanvas_bg',
			array(
				'label' => __( 'Background Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-container' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'cart_type' => 'canvas' ),
			)
		);

		$this->add_responsive_control(
			'cart_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 300,
						'max' => 700,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-popup .elementor-widget-cmsmasters-woo-cart__cart-container' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'cart_type' => 'popup' ),
			)
		);

		$this->add_responsive_control(
			'cart_width_canvas',
			array(
				'label' => __( 'Width Offcanvas', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'min' => 300,
						'max' => 1000,
						'step' => 5,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-woo-cart-type-canvas .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cart_type' => 'canvas',
				),
			)
		);

		$this->add_control(
			'cart_vertical_align',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'space-between' => array(
						'title' => __( 'Space Between', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-cart-vertical-align: {{VALUE}};',
				),
				'condition' => array( 'cart_type' => 'canvas' ),
			)
		);

		$this->add_responsive_control(
			'cart_margin_top',
			array(
				'label' => __( 'Margin Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'cart_type' => 'popup' ),
			)
		);

		$this->add_responsive_control(
			'cart_margin_right',
			array(
				'label' => __( 'Margin Right', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'cart_type' => 'popup' ),
			)
		);

		$this->add_responsive_control(
			'cart_margin_left',
			array(
				'label' => __( 'Margin Left', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'cart_type' => 'popup' ),
			)
		);

		$this->add_responsive_control(
			'cart_padding',
			array(
				'label' => __( 'Cart Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cart_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'cart_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'cart_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-wrapper',
			)
		);

		$this->add_control(
			'empty_cart_heading',
			array(
				'label' => __( 'Empty Cart', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'empty_cart_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-empty-description' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'empty_cart_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-empty-description',
			)
		);

		$this->add_control(
			'empty_cart_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-empty-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_title_section_style',
			array(
				'label' => __( 'Cart Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'title_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'title_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title',
			)
		);

		$this->add_responsive_control(
			'title_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label' => __( 'Title Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label' => __( 'Bottom Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_subtotal_section_style',
			array(
				'label' => __( 'Cart Subtotal', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'cart_type!' => 'link',
					'show_cart_on!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'subtotal_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'space-between',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'subtotal_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal',
			)
		);

		$this->add_control(
			'subtotal_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subtotal_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'subtotal_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal',
			)
		);

		$this->add_responsive_control(
			'subtotal_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtotal_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtotal_spacing_top',
			array(
				'label' => __( 'Spacing Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__subtotal' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'subtotal_amount_heading',
			array(
				'label' => __( 'Amount', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'subtotal_amount_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--subtotal-amount-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtotal_amount_top',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--subtotal-amount-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_message_section_style',
			array(
				'label' => __( 'Cart Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'description_position',
			array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'-1' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'0' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => '0',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-title' => 'order: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'description_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'description_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description',
			)
		);

		$this->add_responsive_control(
			'description_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_spacing_top',
			array(
				'label' => __( 'Spacing Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_spacing_bottom',
			array(
				'label' => __( 'Spacing Bottom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .elementor-widget-cmsmasters-woo-cart__cart-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'description_position' => '-1' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_product_section_style',
			array(
				'label' => __( 'Cart Product', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'cart_type!' => 'link',
					'show_cart_on!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'product_max_height',
			array(
				'label' => __( 'Max Height Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 130,
						'max' => 700,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__products' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_container_padding',
			array(
				'label' => __( 'Container Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__products' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'product_typography',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product a',
			)
		);

		$this->add_control(
			'product_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product__inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_padding',
			array(
				'label' => __( 'Product Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_separator_spacing',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product:not(:first-of-type)' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-top: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
				'condition' => array( 'product_separator_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_image_toggle',
			array(
				'label' => __( 'Product Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'separator' => 'before',
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'product_image_position',
			array(
				'label' => __( 'Image Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'condition' => array( 'product_image_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_image_width',
			array(
				'label' => __( 'Image Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-image' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'product_image_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-image img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'product_image_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_image_spacing_right',
			array(
				'label' => __( 'Right Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-text-container, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-text-container' => 'padding-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'product_image_position' => 'left',
					'product_image_toggle' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'product_image_spacing_left',
			array(
				'label' => __( 'Left Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-text-container, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-text-container' => 'padding-left: 0; padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'product_image_position' => 'right',
					'product_image_toggle' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'product_title_toggle',
			array(
				'label' => __( 'Product Title Styles', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'separator' => 'before',
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'product_title_normal_heading',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'product_title_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-name a' => 'color: {{VALUE}};',
				),
				'condition' => array( 'product_title_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_title_hover_heading',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'product_title_color_hover',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-name a:hover' => 'color: {{VALUE}};',
				),
				'condition' => array( 'product_title_toggle' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'product_title_typography',
				'label' => __( 'Product Title Typography', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-name a',
			)
		);

		$this->add_control(
			'product_price_toggle',
			array(
				'label' => __( 'Product Price Styles', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'separator' => 'before',
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'product_price_type',
			array(
				'label' => __( 'Price Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => 'Inline type of price',
					),
					'block' => array(
						'title' => __( 'Block', 'cmsmasters-elementor' ),
						'description' => 'Block type of price',
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'inline',
				'condition' => array( 'product_price_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_price_weight',
			array(
				'label' => __( 'Price Weight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'100' => __( '100', 'cmsmasters-elementor' ),
					'200' => __( '200', 'cmsmasters-elementor' ),
					'300' => __( '300', 'cmsmasters-elementor' ),
					'400' => __( '400', 'cmsmasters-elementor' ),
					'500' => __( '500', 'cmsmasters-elementor' ),
					'600' => __( '600', 'cmsmasters-elementor' ),
					'700' => __( '700', 'cmsmasters-elementor' ),
					'800' => __( '800', 'cmsmasters-elementor' ),
					'900' => __( '900', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-price .amount' => 'font-weight: {{VALUE}};',
				),
				'condition' => array( 'product_price_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_price_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .variation' => 'color: {{VALUE}};',
				),
				'condition' => array( 'product_price_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_price_spacing',
			array(
				'label' => __( 'Top Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-price' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'product_price_type' => 'block',
					'product_price_toggle' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'product_price_typography',
				'label' => __( 'Product Price Typography', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-price, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .variation dt, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .variation dd',
				'condition' => array( 'product_price_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'remove_icon_toggle',
			array(
				'label' => __( 'Remove Icon Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'separator' => 'before',
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'product_remove_normal_heading',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'product_remove_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:after, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:before' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_remove_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'product_remove_border_border!' => '',
					'remove_icon_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'product_remove_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_remove_hover_heading',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'product_remove_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:hover:after, {{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:hover:before' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_remove_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'product_remove_border_border!' => '',
					'remove_icon_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'product_remove_bg_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'product_remove_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove',
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_remove_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_remove_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product .cmsmasters-menu-cart__product-remove' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'remove_icon_toggle' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->add_control(
			'product_separator_toggle',
			array(
				'label' => __( 'Product Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'product_separator_style',
			array(
				'label' => __( 'Separator Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'outset' => __( 'Outset', 'cmsmasters-elementor' ),
				),
				'default' => 'solid',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product:not(:first-of-type)' => 'border-top-style: {{VALUE}};',
				),
				'condition' => array( 'product_separator_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'product_separator_color',
			array(
				'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product:not(:first-of-type)' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array( 'product_separator_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'product_separator_width',
			array(
				'label' => __( 'Separator Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__product:not(:first-of-type)' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'product_separator_toggle' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'cart_buttons_section_style',
			array(
				'label' => __( 'Cart Buttons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'cart_type!' => 'link',
					'show_cart_on!' => 'none',
				),
			)
		);

		$this->add_control(
			'cart_buttons_type',
			array(
				'label' => __( 'Buttons Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => 'Inline type of cart button',
					),
					'stacked' => array(
						'title' => __( 'Stacked', 'cmsmasters-elementor' ),
						'description' => 'Stacked type of cart button',
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'inline',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'cart_buttons_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a',
			)
		);

		$this->start_controls_tabs( 'buttons_tab_style' );

		$this->start_controls_tab(
			'cart_buttons_tab_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'cart_buttons_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_buttons_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'cart_buttons_border_border!' => '' ),
			)
		);

		$this->add_control(
			'cart_buttons_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_buttons_tab_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'cart_buttons_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_buttons_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'cart_buttons_border_border!' => '' ),
			)
		);

		$this->add_control(
			'cart_buttons_bg_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'cart_buttons_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a',
			)
		);

		$this->add_responsive_control(
			'cart_buttons_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cart_buttons_padding',
			array(
				'label' => __( 'Buttons Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cart_buttons_spacing_top',
			array(
				'label' => __( 'Spacing Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons' => 'padding-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cart_buttons_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container.cmsmasters-button-type-stacked .cmsmasters-menu-cart__footer-buttons' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container.cmsmasters-button-type-inline .cmsmasters-menu-cart__footer-buttons' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'view_cart_toggle',
			array(
				'label' => __( 'View Cart Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'view_cart_normal_heading',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'button_view_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart' => 'color: {{VALUE}};',
				),
				'condition' => array( 'view_cart_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'button_view_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_view_border_border!' => '',
					'view_cart_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_view_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'view_cart_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'view_cart_hover_heading',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_view_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart:hover' => 'color: {{VALUE}};',
				),
				'condition' => array( 'view_cart_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'button_view_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_view_border_border!' => '',
					'view_cart_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_view_bg_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'view_cart_toggle' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_view_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart',
				'condition' => array( 'view_cart_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'button_view_padding',
			array(
				'label' => __( 'View Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--view-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'cart_buttons_type' => 'stacked',
					'view_cart_toggle' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'checkout_toggle',
			array(
				'label' => __( 'Checkout Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

		$this->add_control(
			'checkout_heading',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_checkout_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout' => 'color: {{VALUE}};',
				),
				'condition' => array( 'checkout_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'button_checkout_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_checkout_border_border!' => '',
					'checkout_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_checkout_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'checkout_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'checkout_hover_heading',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_checkout_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout:hover' => 'color: {{VALUE}};',
				),
				'condition' => array( 'checkout_toggle' => 'yes' ),
			)
		);

		$this->add_control(
			'button_checkout_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_checkout_border_border!' => '',
					'checkout_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_checkout_bg_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'checkout_toggle' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_checkout_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout',
				'condition' => array( 'checkout_toggle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'button_checkout_padding',
			array(
				'label' => __( 'Checkout Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-container .cmsmasters-menu-cart__footer-buttons > a.elementor-button--checkout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'cart_buttons_type' => 'stacked',
					'checkout_toggle' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close',
			array(
				'label' => __( 'Canvas Close', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'cart_type' => 'canvas' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'close_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close .elementor-widget-cmsmasters-woo-cart__close-label',
				'condition' => array( 'close_content!' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'tabs_close_style' );

		$this->start_controls_tab(
			'tab_close_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close .cmsmasters-wrap-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_content!' => 'text' ),
			)
		);

		$this->add_control(
			'close_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'close_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'close_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'close_icon_size_hover',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close i:hover' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'close_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'close_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'close_box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'close_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close',
			)
		);

		$this->add_responsive_control(
			'close_margin_top',
			array(
				'label' => __( 'Margin Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'close_position' => 'inside',
					'close_alignment' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'close_margin_left',
			array(
				'label' => __( 'Margin Left', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'close_position' => 'inside',
					'close_alignment' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'close_margin_outside_left',
			array(
				'label' => __( 'Margin Left', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'close_position' => 'outside',
					'canvas_position' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'close_margin_right',
			array(
				'label' => __( 'Margin Right', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'close_position' => 'inside',
					'canvas_position' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'close_margin_outside_right',
			array(
				'label' => __( 'Margin Right', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'close_position' => 'outside',
					'canvas_position' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'close_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-cart__cart-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get icon cart.
	 *
	 * @return string
	 */
	public function get_icon_cart() {
		$settings = $this->get_settings_for_display();

		ob_start();

		if ( 'text' !== $settings['button_style'] ) {
			$button_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $settings['button_style'] ) {
				$button_icon_att = array_merge(
					$button_icon_att,
					array( 'aria-label' => 'Cart' ),
				);
			}

			if ( 'icon' === $settings['button_icon_type'] && ! empty( $settings['button_icon'] ) ) {
				echo "<span class='elementor-widget-cmsmasters-woo-cart__button-icon'>";
					Icons_Manager::render_icon( $settings['button_icon'], $button_icon_att );
				echo '</span>';
			} elseif ( 'image' === $settings['button_icon_type'] && ! empty( $settings['button_image']['url'] ) ) {
				echo '<span class="elementor-widget-cmsmasters-woo-cart__button-image">' .
					Group_Control_Image_Size::get_attachment_image_html( $settings, 'button_image', 'button_image' ) .
				'</span>';
			}
		}

		return ob_get_clean();
	}

	/**
	 * Get counter.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_counter() {
		$settings = $this->get_settings_for_display();

		$out = '';

		if ( 'none' !== $settings['counter'] ) {
			$out .= CmsmastersUtils::get_ob_html( array( __CLASS__, 'get_counter_inner' ) );
		}

		return $out;
	}

	/**
	 * Get counter insides.
	 *
	 * @since 1.0.0
	 * @since 1.3.8 Fixed hide products counter for empty cart.
	 */
	public static function get_counter_inner() {
		$product_counter = self::product_counter();

		echo '<span class="elementor-widget-cmsmasters-woo-cart__button-counter" data-counter="' . esc_attr( $product_counter ) . '">' .
			esc_html( $product_counter ) .
		'</span>';
	}

	/**
	 * Get products counter.
	 *
	 * @since 1.3.8
	 */
	public static function product_counter() {
		return WC()->cart->get_cart_contents_count();
	}

	/**
	 * Get button.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_button_text() {
		$settings = $this->get_settings_for_display();

		$out = '';

		if ( isset( $settings['button_text'] ) ) {
			$button_text = esc_html( $settings['button_text'] );

			if ( '' === $settings['button_text'] ) {
				$button_text = esc_html__( 'Cart', 'cmsmasters-elementor' );
			}

			$out .= '<span class="elementor-widget-cmsmasters-woo-cart__button-text">' . $button_text . '</span>';
		}

		return $out;
	}


	/**
	 * Get subtotal.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_subtotal() {
		$settings = $this->get_settings_for_display();

		$out = '';

		if ( 'true' === $settings['subtotal'] ) {
			$out .= '<span class="elementor-widget-cmsmasters-woo-cart__button-subtotal">' .
				CmsmastersUtils::get_ob_html( array( __CLASS__, 'get_subtotal_inner' ) ) .
			'</span>';
		}

		return $out;
	}


	/**
	 * Get subtotal insides.
	 *
	 * @since 1.0.0
	 */
	public static function get_subtotal_inner() {
		?>
		<span class="elementor-widget-cmsmasters-woo-cart__button-subtotal">
			<?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</span>
		<?php
	}

	/**
	 * Get close button.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed render icons in widget.
	 *
	 * @return string
	 */
	public function get_close() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'cart-close',
			array(
				'class' => array( 'elementor-widget-cmsmasters-woo-cart__cart-close' ),
				'role' => 'button',
				'tabindex' => '0',
			),
		);

		if ( isset( $settings['close_position'] ) ) {
			$this->add_render_attribute( 'cart-close', 'class', 'cmsmasters-position-' . esc_attr( $settings['close_position'] ) );
		}

		if ( isset( $settings['close_alignment'] ) ) {
			$this->add_render_attribute( 'cart-close', 'class', 'cmsmasters-align-' . esc_attr( $settings['close_alignment'] ) );
		}

		$close_content = ( isset( $settings['close_content'] ) ? $settings['close_content'] : '' );

		if ( isset( $close_content ) && 'icon-text' === $close_content ) {
			$this->add_render_attribute( 'cart-close', 'class', array(
				'cmsmasters-icon-text',
				'cmsmasters-icon-' . esc_attr( $settings['icon_close_position'] ),
			) );
		}

		if ( isset( $settings['close_view'] ) ) {
			$this->add_render_attribute( 'cart-close', 'class', 'cmsmasters-view-' . esc_attr( $settings['close_view'] ) );
		}

		if ( isset( $settings['close_shape'] ) ) {
			$this->add_render_attribute( 'cart-close', 'class', 'cmsmasters-shape-' . esc_attr( $settings['close_shape'] ) );
		}

		$out = '<div ' . $this->get_render_attribute_string( 'cart-close' ) . '>';

		$close_label = ( isset( $settings['close_label'] ) ? $settings['close_label'] : '' );

		if ( 'text' !== $close_content ) {
			$close_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $close_content || ( 'icon-text' === $close_content && empty( $close_label ) ) ) {
				$close_icon_att = array_merge(
					$close_icon_att,
					array( 'aria-label' => 'Close Button' ),
				);
			}

			$out .= CmsmastersUtils::get_render_icon( $settings['close_icon'], $close_icon_att );
		}

		if ( 'icon' !== $close_content ) {
			$out .= '<span class="elementor-widget-cmsmasters-woo-cart__close-label">' .
				esc_html( $close_label ) .
			'</span>';
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * Render woocommerce breadcrumbs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.3.8 Fixed cart text
	 */

	protected function render() {

		if ( null === WC()->cart ) {
			return;
		}

		$cart_items = WC()->cart->get_cart();

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'cart-container', 'class', 'elementor-widget-cmsmasters-woo-cart__container' );

		if ( 'popup' === $settings['cart_type'] ) {
			$this->add_render_attribute( 'cart-container', 'class', 'cmsmasters-woo-cart-position-' . esc_attr( $settings['button_alignment'] ) );
		}

		$this->add_render_attribute( 'button-content', 'class', array(
			'elementor-widget-cmsmasters-woo-cart__button-content',
			'cmsmasters-counter-' . esc_attr( $settings['counter'] ),
		) );

		if ( 'text' !== $settings['button_style'] && isset( $settings['icon_position'] ) ) {
			$this->add_render_attribute( 'button-content', 'class', 'cmsmasters-icon-position-' . esc_attr( $settings['icon_position'] ) );
		}

		if ( 'relative' === $settings['counter'] ) {
			$this->add_render_attribute( 'button-content', 'class', 'cmsmasters-counter-type-' . esc_attr( $settings['counter_type'] ) );

			if ( isset( $settings['counter_position'] ) ) {
				$this->add_render_attribute( 'button-content', 'class', 'cmsmasters-counter-position-' . esc_attr( $settings['counter_position'] ) );
			}
		}

		if ( 'popup' === $settings['cart_type'] && 'none' === $settings['show_cart_on'] ) {
			if ( isset( $settings['cart_url'] ) && '' !== $settings['cart_url']['url'] ) {
				$this->add_render_attribute( 'button-content', 'href', esc_url( $settings['cart_url']['url'] ) );
			} else {
				$this->add_render_attribute( 'button-content', 'href', esc_url( wc_get_cart_url() ) );
			}
		} else {
			$this->add_render_attribute( 'button-content', 'href', esc_url( wc_get_cart_url() ) );
		}

		echo '<div class="cmsmasters-woo-cart-empty-style"></div>' .
		'<div ' . $this->get_render_attribute_string( 'cart-container' ) . '>' .
			'<div class="elementor-widget-cmsmasters-woo-cart__button-container">' .
				'<div class="elementor-widget-cmsmasters-woo-cart__button-inner">' .
					'<a ' . $this->get_render_attribute_string( 'button-content' ) . '>';

		if ( isset( $settings['button_text'] ) || 'true' === $settings['subtotal'] ) {
			echo '<span class="elementor-widget-cmsmasters-woo-cart__button-text-wrap">';

			if ( isset( $settings['button_text'] ) ) {
				echo $this->get_button_text(); // XSS ok.
			}

			if ( 'true' === $settings['subtotal'] ) {
				echo $this->get_subtotal(); // XSS ok.
			}

			echo '</span>';
		}

		if ( 'none' !== $settings['counter'] || 'text' !== $settings['button_style'] ) {
			echo '<span class="elementor-widget-cmsmasters-woo-cart__button-icon-wrap">';

			if ( 'none' !== $settings['counter'] ) {
				echo $this->get_counter(); // XSS ok.
			}

			if ( 'text' !== $settings['button_style'] ) {
				echo $this->get_icon_cart(); // XSS ok.
			}

			echo '</span>';
		}

					echo '</a>' .
				'</div>' .
			'</div>';

		if (
			( 'popup' === $settings['cart_type'] && 'none' !== $settings['show_cart_on'] ) ||
			'canvas' === $settings['cart_type']
		) {
			$this->add_render_attribute( 'cart-content', 'class', array(
				'elementor-widget-cmsmasters-woo-cart__cart-container',
				'cmsmasters-button-type-' . esc_attr( $settings['cart_buttons_type'] ),
			) );

			if ( isset( $settings['show_cart_on'] ) ) {
				$this->add_render_attribute( 'cart-content', 'class', 'cmsmasters-show-cart-' . esc_attr( $settings['show_cart_on'] ) );
			}

			if ( 'canvas' === $settings['cart_type'] ) {
				$this->add_render_attribute( 'cart-content', 'class', 'cmsmasters-canvas-position-' . esc_attr( $settings['canvas_position'] ) );
			}

			if ( isset( $settings['product_image_position'] ) ) {
				$this->add_render_attribute( 'cart-content', 'class', 'cmsmasters-woo-cart-image-' . esc_attr( $settings['product_image_position'] ) );
			}

			if ( isset( $settings['product_price_type'] ) ) {
				$this->add_render_attribute( 'cart-content', 'class', 'cmsmasters-woo-cart-price-' . esc_attr( $settings['product_price_type'] ) );
			}

			$this->add_render_attribute( 'cart-wrapper', 'class', 'elementor-widget-cmsmasters-woo-cart__cart-wrapper' );

			if ( isset( $settings['close_position'] ) ) {
				$this->add_render_attribute( 'cart-wrapper', 'class', 'cmsmasters-position-' . esc_attr( $settings['close_position'] ) );
			}

			echo '<div ' . $this->get_render_attribute_string( 'cart-content' ) . '>' .
				'<div ' . $this->get_render_attribute_string( 'cart-wrapper' ) . '>';

			if ( 'canvas' === $settings['cart_type'] ) {
				echo $this->get_close(); // XSS ok.
			}

				echo '<div class="elementor-widget-cmsmasters-woo-cart__cart-inner">
					<div class="elementor-widget-cmsmasters-woo-cart__cart-product-inner">';

			$this->get_cart_title();

			woocommerce_mini_cart();

			$this->get_cart_message();

				echo '</div>
					</div>
				</div>
			</div>';
		}

		echo '</div>';
	}

	/**
	 * Get cart title.
	 *
	 * @since 1.3.8
	 */
	public function get_cart_title() {
		$settings = $this->get_settings_for_display();

		if ( '' !== $settings['cart_title'] ) {
			echo '<div class="elementor-widget-cmsmasters-woo-cart__cart-title">' .
				esc_html( $settings['cart_title'] ) .
			'</div>';
		}
	}

	/**
	 * Get cart message.
	 *
	 * @since 1.3.8
	 */
	public function get_cart_message() {
		$settings = $this->get_settings_for_display();

		if ( '' !== $settings['cart_message'] ) {
			echo '<div class="elementor-widget-cmsmasters-woo-cart__cart-description">' .
				esc_html( $settings['cart_message'] ) .
			'</div>';
		}
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
			'cart_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Cart Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'button_text',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'cart_title',
				'type' => esc_html__( 'Cart Title Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'cart_message',
				'type' => esc_html__( 'Cart Message Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'close_label',
				'type' => esc_html__( 'Close Button Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}