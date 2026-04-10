<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Addon WooCommerce `cart page` widget.
 *
 * Addon widget that display WooCommerce cart page.
 *
 * @since 1.8.0
 */
class Cart_Page extends Base_Widget {

	use Woo_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Cart', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-cart-page';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'woocommerce',
			'cart',
			'page',
		);
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::WOO_WIDGETS_CATEGORY );
	}

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array(
			'wc-cart',
			'selectWoo',
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
			'select2',
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
	 * @since 1.8.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->register_general_controls_content();

		$this->register_order_summary_controls_content();

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$this->register_coupons_controls_content();
		}

		$this->register_totals_controls_content();

		$this->register_additional_options_controls_content();

		$this->register_sections_controls_style();

		$this->register_forms_controls_style();

		$this->register_buttons_controls_style();

		$this->register_customize_controls_style();

		$this->register_customize_order_summary_controls_style();

		$this->register_customize_totals_controls_style();

		$this->register_customize_coupon_controls_style();
	}

	protected function register_general_controls_content() {
		$this->start_controls_section(
			'general_section_content',
			array( 'label' => esc_html__( 'General', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'general_cart_layout',
			array(
				'label' => esc_html__( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'two-column' => esc_html__( 'Two columns', 'cmsmasters-elementor' ),
					'one-column' => esc_html__( 'One column', 'cmsmasters-elementor' ),
				),
				'default' => 'two-column',
				'prefix_class' => 'cmsmasters-cart-layout-',
			)
		);

		$this->add_control(
			'general_default_cart_page',
			array(
				'raw' => __( 'You can set the default Cart page by going to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=go_theme_settings" target="_blank">' . __( 'Theme Settings', 'cmsmasters-elementor' ) . '</a>' . __( ' -> WooCommerce -> WooCommerce Pages', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();
	}

	protected function register_order_summary_controls_content() {
		$this->start_controls_section(
			'order_summary_section_content',
			array(
				'label' => esc_html__( 'Order Summary', 'cmsmasters-elementor' ),
				'condition' => array( 'additional_options_update_cart_automatically' => '' ),
			)
		);

		$this->add_control(
			'order_summary_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Update Cart Button', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'order_summary_button_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'Update Cart', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Update Cart', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'order_summary_button_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => esc_html__( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => esc_html__( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors_dictionary' => array(
					'start' => '--order-summary-button-alignment: start; --order-summary-button-width: auto;',
					'center' => '--order-summary-button-alignment: center; --order-summary-button-width: auto;',
					'end' => '--order-summary-button-alignment: end; --order-summary-button-width: auto;',
					'justify' => '--order-summary-button-alignment: justify; --order-summary-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register coupons controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.8.0
	 * @since 1.17.4 Added `Input Placeholder` and `Button Text` controls for coupon.
	 */
	protected function register_coupons_controls_content() {
		$this->start_controls_section(
			'coupon_section_content',
			array( 'label' => esc_html__( 'Coupon', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'coupon_display',
			array(
				'label' => esc_html__( 'Coupon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'coupon_input_placeholder',
			array(
				'label' => esc_html__( 'Input Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Coupon code', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'coupon_button_text',
			array(
				'label' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Apply coupon', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'coupon_button_alignment',
			array(
				'label' => esc_html__( 'Button Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => esc_html__( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => esc_html__( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors_dictionary' => array(
					'start' => '--coupon-button-alignment: start; --coupon-button-width: auto;',
					'center' => '--coupon-button-alignment: center;  --coupon-button-width: auto;',
					'end' => '--coupon-button-alignment: end;  --coupon-button-width: auto;',
					'justify' => '--coupon-button-alignment: center; --coupon-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
				'condition' => array( 'coupon_display' => 'yes' ),
			)
		);

		$this->add_control(
			'coupon_button_alignment_note',
			array(
				'raw' => esc_html__( 'Note: this setting will be applied for mobile only.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'coupon_display' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_totals_controls_content() {
		$this->start_controls_section(
			'totals_section_content',
			array( 'label' => esc_html__( 'Totals', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'totals_section_title',
			array(
				'label' => esc_html__( 'Section Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'Cart Totals', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Cart Totals', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'totals_title_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => esc_html__( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => esc_html__( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--totals-title-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'totals_update_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Update Shipping Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'totals_update_button_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'Update', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Update', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'totals_update_button_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => esc_html__( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => esc_html__( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors_dictionary' => array(
					'start' => '--totals-update-button-alignment: start; --totals-update-button-width: auto;',
					'center' => '--totals-update-button-alignment: center;  --totals-update-button-width: auto;',
					'end' => '--totals-update-button-alignment: end;  --totals-update-button-width: auto;',
					'justify' => '--totals-update-button-alignment: center; --totals-update-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'totals_checkout_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkout Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'totals_checkout_button_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'Proceed to Checkout', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Proceed to Checkout', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'totals_checkout_button_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => esc_html__( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => esc_html__( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors_dictionary' => array(
					'start' => '--totals-checkout-button-alignment: flex-start; --totals-checkout-button-width: fit-content;',
					'center' => '--totals-checkout-button-alignment: center; --totals-checkout-button-width: fit-content;',
					'end' => '--totals-checkout-button-alignment: flex-end; --totals-checkout-button-width: fit-content;',
					'justify' => '--totals-checkout-button-alignment: stretch; --totals-checkout-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_additional_options_controls_content() {
		$this->start_controls_section(
			'additional_options_section_content',
			array( 'label' => esc_html__( 'Additional Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'additional_options_update_cart_automatically',
			array(
				'label' => esc_html__( 'Update Cart Automatically', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors_dictionary' => array(
					'yes' => '--additional-options-update-cart-automatically-display: none;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'additional_options_update_cart_automatically_description',
			array(
				'raw' => esc_html__( 'Changes in the cart will be updated automatically.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'additional_options_template_switch',
			array(
				'label' => esc_html__( 'Customize empty cart', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'active',
				'default' => '',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-cart-empty-template-',
			)
		);

		/* translators: 1: Additional Template link <a> opening tag, 2: <a> closing tag. */
		$additional_options_template_description_message = sprintf(
			esc_html__( 'Replaces the default WooCommerce Empty Cart screen with a custom template. (Don’t have one? Head over to %1$sSaved Templates%2$s)', 'cmsmasters-elementor' ),
			'<a href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library#add_new' ) ) . '" target="_blank">',
			'</a>'
		);

		$this->add_control(
			'additional_options_template_description',
			array(
				'raw' => $additional_options_template_description_message,
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor elementor-descriptor-subtle',
				'condition' => array( 'additional_options_template_switch' => 'active' ),
			)
		);

		$this->add_control(
			'additional_options_template_select_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Choose template', 'cmsmasters-elementor' ),
				'condition' => array( 'additional_options_template_switch' => 'active' ),
			)
		);

		$document_types = Plugin::$instance->documents->get_document_types( array(
			'show_in_library' => true,
		) );

		$this->add_control(
			'additional_options_template_select',
			array(
				'type' => CmsmastersControls::QUERY,
				'label_block' => true,
				'show_label' => false,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => array_keys( $document_types ),
								'compare' => 'IN',
							),
						),
					),
				),
				'frontend_available' => true,
				'condition' => array( 'additional_options_template_switch' => 'active' ),
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_general_controls_style( $section, $selector, $customize_element ) {
		$section = ( '' === $section ? 'sections_' : $section . '_section_' );
		$selector = ( '' === $selector ? '' : ' ' . $selector );

		if ( '' === $customize_element ) {
			$condition = '';

			$condition_border_color = array( $section . 'border_type!' => 'none' );

			$condition_border_width = array(
				$section . 'border_type!' => array(
					'',
					'none',
				),
			);
		} else {
			$condition = array( 'section_cart_show_customize_elements' => $customize_element );

			$condition_border_color = array(
				'section_cart_show_customize_elements' => $customize_element,
				$section . 'border_type!' => 'none',
			);

			$condition_border_width = array(
				'section_cart_show_customize_elements' => $customize_element,
				$section . 'border_type!' => array(
					'',
					'none',
				),
			);
		}

		$this->add_control(
			$section . 'background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-background-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			$section . 'border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-border-color: {{VALUE}};',
				),
				'condition' => $condition_border_color,
			)
		);

		$this->add_responsive_control(
			$section . 'gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $section . 'item-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			$section . 'padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			$section . 'border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			$section . 'border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-border-type: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			$section . 'border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' . $selector => '--sections-border-top-width: {{TOP}}{{UNIT}}; --sections-border-right-width: {{RIGHT}}{{UNIT}}; --sections-border-bottom-width: {{BOTTOM}}{{UNIT}}; --sections-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition_border_width,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => $section . 'box_shadow',
				'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--sections-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}' . $selector,
				'condition' => $condition,
			)
		);
	}

	protected function register_sections_controls_style() {
		$this->start_controls_section(
			'section_sections_style',
			array(
				'label' => esc_html__( 'Sections', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->register_customize_general_controls_style( '', '', '' );

		$this->end_controls_section();
	}

	protected function register_forms_controls_style() {
		$this->start_controls_section(
			'forms_section_style',
			array(
				'label' => esc_html__( 'Forms', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'forms_rows_gap',
			array(
				'label' => esc_html__( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--forms-rows-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'forms_fields_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Field', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'forms_fields_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-field-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'forms_fields_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'focus' === $main_key ? ':focus' : '' );

			$this->start_controls_tab(
				"forms_fields_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"forms_fields_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--forms-fields-{$main_key}-color: {{VALUE}};",
						".e-woo-select2-wrapper .select2-results__option{$state}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"forms_fields_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--forms-fields-{$main_key}-bg-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"forms_fields_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--forms-fields-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'forms_fields_border_type!' => 'none' ),
				)
			);

			$this->add_responsive_control(
				"forms_fields_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--forms-fields-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "forms_fields_{$main_key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--forms-fields-{$main_key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'forms_fields_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--forms-fields-padding-top: {{TOP}}{{UNIT}}; --forms-fields-padding-right: {{RIGHT}}{{UNIT}}; --forms-fields-padding-bottom: {{BOTTOM}}{{UNIT}}; --forms-fields-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'forms_fields_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--forms-fields-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'forms_fields_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--forms-fields-border-top-width: {{TOP}}{{UNIT}}; --forms-fields-border-right-width: {{RIGHT}}{{UNIT}}; --forms-fields-border-bottom-width: {{BOTTOM}}{{UNIT}}; --forms-fields-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'forms_fields_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_buttons_controls_style() {
		$this->start_controls_section(
			'buttons_section_style',
			array(
				'label' => esc_html__( 'Buttons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'buttons_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--buttons-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'buttons_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'normal' === $main_key ? ':before' : ':after' );
			$buttons_bg_selector = "{{WRAPPER}} .shop_table .button{$state}, {{WRAPPER}} .cart_totals .checkout-button{$state}";

			$this->start_controls_tab(
				"buttons_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"buttons_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--buttons-{$main_key}-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$buttons_bg_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$buttons_bg_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{buttons_{$main_key}_bg_group_color_stop.SIZE}}{{buttons_{$main_key}_bg_group_color_stop.UNIT}}, {{buttons_{$main_key}_bg_group_color_b.VALUE}} {{buttons_{$main_key}_bg_group_color_b_stop.SIZE}}{{buttons_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
						"buttons_{$main_key}_bg_group_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_bg_group_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$buttons_bg_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{buttons_{$main_key}_bg_group_color_stop.SIZE}}{{buttons_{$main_key}_bg_group_color_stop.UNIT}}, {{buttons_{$main_key}_bg_group_color_b.VALUE}} {{buttons_{$main_key}_bg_group_color_b_stop.SIZE}}{{buttons_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"buttons_{$main_key}_bg_group_background" => array( 'gradient' ),
						"buttons_{$main_key}_bg_group_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"buttons_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--buttons-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'buttons_border_type!' => 'none' ),
				)
			);

			$this->add_responsive_control(
				"buttons_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--buttons-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "buttons_{$main_key}_text_shadow",
					'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--buttons-{$main_key}-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "buttons_{$main_key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--buttons-{$main_key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'buttons_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--buttons-padding-top: {{TOP}}{{UNIT}}; --buttons-padding-right: {{RIGHT}}{{UNIT}}; --buttons-padding-bottom: {{BOTTOM}}{{UNIT}}; --buttons-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'buttons_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--buttons-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'buttons_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--buttons-border-top-width: {{TOP}}{{UNIT}}; --buttons-border-right-width: {{RIGHT}}{{UNIT}}; --buttons-border-bottom-width: {{BOTTOM}}{{UNIT}}; --buttons-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'buttons_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_controls_style() {
		$this->start_controls_section(
			'section_customize_style',
			array(
				'label' => esc_html__( 'Customize', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$customize_options = array();

		$customize_options += array(
			'customize_order_summary' => esc_html__( 'Order Summary', 'cmsmasters-elementor' ),
		);

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$customize_options += array(
				'customize_coupon' => esc_html__( 'Coupon', 'cmsmasters-elementor' ),
			);
		}

		$customize_options += array(
			'customize_totals' => esc_html__( 'Totals', 'cmsmasters-elementor' ),
		);

		$this->add_control(
			'section_cart_show_customize_elements',
			array(
				'label' => esc_html__( 'Select sections of the cart to customize:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $customize_options,
				'render_type' => 'ui',
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register customize order summary controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.8.0
	 * @since 1.10.1 Added `Padding` control for order summary quantity.
	 * @since 1.11.11 Added `Odd Products Background Color` and `Odd Products Background Color` controls for order summary item.
	 */
	protected function register_customize_order_summary_controls_style() {
		$this->start_controls_section(
			'customize_order_summary_section_style',
			array(
				'label' => esc_html__( 'Customize: Order Summary', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_order_summary', '.e-shop-table', 'customize_order_summary' );

		$this->remove_control( 'customize_order_summary_section_gap' );

		$this->add_control(
			'customize_order_summary_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_title_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-title-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'customize_order_summary_title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-title-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_items_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-items-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_items_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-items-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_responsive_control(
			'customize_order_summary_items_rows_gap',
			array(
				'label' => esc_html__( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-items-rows-gap-top: calc( {{SIZE}}{{UNIT}}/2 ); --customize-order-summary-items-rows-gap-bottom: calc( {{SIZE}}{{UNIT}}/2 );',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_remove_icon_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Remove icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->start_controls_tabs(
			'customize_order_summary_remove_icon_tabs',
			array( 'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ) )
		);

		$this->start_controls_tab(
			'customize_order_summary_remove_icon_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_remove_icon_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-remove-icon-normal-color: {{VALUE}}',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'customize_order_summary_remove_icon_hover',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_remove_icon_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-remove-icon-hover-color: {{VALUE}}',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'customize_order_summary_product_link_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Link', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->start_controls_tabs(
			'customize_order_summary_link_tabs',
			array( 'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ) )
		);

		$this->start_controls_tab(
			'customize_order_summary_link_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_link_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-link-normal-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'customize_order_summary_link_hover',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_link_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-link-hover-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'customize_order_summary_quantity_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Quantity', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_responsive_control(
			'customize_order_summary_quantity_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-quantity-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_quantity_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-quantity-border-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_responsive_control(
			'customize_order_summary_quantity_border_weight',
			array(
				'label' => esc_html__( 'Border Weight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-quantity-border-weight: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_variations_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Variations', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_variations_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-variations-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_variations_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-variations-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_responsive_control(
			'customize_order_summary_button_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-button-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_mobile_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'On Mobile', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_mobile_odd_background_color',
			array(
				'label' => esc_html__( 'Odd Products Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-mobile-odd-background-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_mobile_even_background_color',
			array(
				'label' => esc_html__( 'Even Products Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-mobile-even-background-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_totals_controls_style() {
		$this->start_controls_section(
			'customize_totals_section_style',
			array(
				'label' => esc_html__( 'Customize: Totals', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_totals', '.e-cart-totals', 'customize_totals' );

		$this->add_control(
			'customize_totals_title_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_totals_title_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cart_totals' => '--customize-totals-title-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'customize_totals_title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-title-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_responsive_control(
			'customize_totals_title_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-title-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_totals_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Totals', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_totals_totals_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-totals-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_totals_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-totals-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_responsive_control(
			'customize_totals_totals_rows_gap',
			array(
				'label' => esc_html__( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-totals-rows-gap-top: calc( {{SIZE}}{{UNIT}}/2 ); --customize-totals-totals-rows-gap-bottom: calc( {{SIZE}}{{UNIT}}/2 );',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_responsive_control(
			'customize_totals_button_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-button-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_divider_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Divider Total', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_divider_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-divider-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_responsive_control(
			'customize_totals_divider_weight',
			array(
				'label' => esc_html__( 'Weight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-divider-weight: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_radio_buttons_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Buttons', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_totals_radio_buttons_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-radio-buttons-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_radio_buttons_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-radio-buttons-color: {{VALUE}}',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_descriptions_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_totals_descriptions_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-totals-descriptions-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_descriptions_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-cart-totals' => '--customize-totals-descriptions-color: {{VALUE}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_responsive_control(
			'customize_totals_descriptions_spacing',
			array(
				'label' => esc_html__( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-totals-descriptions-top-gap: {{TOP}}{{UNIT}}; --customize-totals-descriptions-bottom-gap: {{BOTTOM}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_link_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->start_controls_tabs(
			'customize_totals_link_colors',
			array( 'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ) )
		);

		$this->start_controls_tab(
			'customize_totals_link_normal_colors',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_link_normal_color',
			array(
				'label' => esc_html__( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-cart-totals' => '--customize-totals-links-normal-color: {{VALUE}} !important;',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'customize_totals_link_hover_colors',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->add_control(
			'customize_totals_link_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-cart-totals' => '--customize-totals-links-hover-color: {{VALUE}} !important;',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_totals' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_customize_coupon_controls_style() {
		$this->start_controls_section(
			'customize_coupon_section_style',
			array(
				'label' => esc_html__( 'Customize: Coupon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_control(
			'customize_coupon_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_coupon', '.coupon', 'customize_coupon' );

		$this->add_control(
			'customize_coupon_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_responsive_control(
			'customize_coupon_button_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--coupon-button-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'section_cart_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Is WooCommerce Feature Active.
	 *
	 * Checks whether a specific WooCommerce feature is active. These checks can sometimes look at multiple WooCommerce
	 * settings at once so this simplifies and centralizes the checking.
	 *
	 * @since 1.8.0
	 *
	 * @param string $feature
	 * @return bool
	 */
	protected function is_wc_feature_active( $feature ) {
		switch ( $feature ) {
			case 'checkout_login_reminder':
				return 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' );

			case 'shipping':
				if ( class_exists( 'WC_Shipping_Zones' ) ) {
					$all_zones = \WC_Shipping_Zones::get_zones();

					if ( count( $all_zones ) > 0 ) {
						return true;
					}
				}
				break;

			case 'coupons':
				return function_exists( 'wc_coupons_enabled' ) && wc_coupons_enabled();

			case 'signup_and_login_from_checkout':
				return 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' );

			case 'ship_to_billing_address_only':
				return wc_ship_to_billing_address_only();
		}

		return false;
	}

	/**
	 * Get Custom Border Type Options
	 *
	 * Return a set of border options to be used in different WooCommerce widgets.
	 *
	 * This will be used in cases where the Group Border Control could not be used.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public static function get_custom_border_type_options() {
		return array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
			'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
			'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
			'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
			'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
			'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Filter Gettext.
	 *
	 * Filter runs when text is output to the page using the translation functions (`_e()`, `__()`, etc.)
	 * used to apply text changes from the widget settings.
	 *
	 * This allows us to make text changes without having to ovveride WooCommerce templates, which would
	 * lead to dev tax to keep all the templates up to date with each future WC release.
	 *
	 * @since 1.8.0
	 *
	 * @param string $translation
	 * @param string $text
	 * @param string $domain
	 * @return string
	 */
	public function filter_gettext( $translation, $text, $domain ) {
		$settings = $this->get_settings_for_display();

		if ( 'woocommerce' !== $domain ) {
			return $translation;
		}

		$gettext_modifications = array(
			'Update cart' => isset( $settings['order_summary_button_text'] ) ? $settings['order_summary_button_text'] : '',
			'Cart totals' => isset( $settings['totals_section_title'] ) ? $settings['totals_section_title'] : '',
			'Proceed to checkout' => isset( $settings['totals_checkout_button_text'] ) ? $settings['totals_checkout_button_text'] : '',
			'Update' => isset( $settings['totals_update_button_text'] ) ? $settings['totals_update_button_text'] : '',
		);

		return array_key_exists( $text, $gettext_modifications ) ? $gettext_modifications[ $text ] : $translation;
	}

	/**
	 * Add Render Hooks
	 *
	 * Add actions & filters before displaying our widget.
	 *
	 * @since 1.8.0
	 */
	public function add_render_hooks() {
		/**
		 * Add actions & filters before displaying our Widget.
		 */
		add_filter( 'gettext', array( $this, 'filter_gettext' ), 20, 3 );

		add_action( 'woocommerce_before_cart', array( $this, 'woocommerce_before_cart' ) );
		add_action( 'woocommerce_after_cart_table', array( $this, 'woocommerce_after_cart_table' ) );
		add_action( 'woocommerce_before_cart_table', array( $this, 'woocommerce_before_cart_table' ) );
		add_action( 'woocommerce_before_cart_collaterals', array( $this, 'woocommerce_before_cart_collaterals' ) );
		add_action( 'woocommerce_after_cart', array( $this, 'woocommerce_after_cart' ) );
		// The following disabling of cart coupon needs to be done this way so that
		// we only disable the display of coupon interface in our cart widget and
		// `wc_coupons_enabled()` can still be reliably used elsewhere.
		add_action( 'woocommerce_cart_contents', array( $this, 'disable_cart_coupon' ) );
		add_action( 'woocommerce_after_cart_contents', array( $this, 'enable_cart_coupon' ) );
		add_filter( 'woocommerce_get_cart_url', array( $this, 'woocommerce_get_cart_url' ) );

		if ( $this->has_empty_cart_template() ) {
			remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

			add_action( 'woocommerce_cart_is_empty', array( $this, 'display_empty_cart_template' ), 10 );
		}

		// Remove cross-sells in cart.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
	}

	/**
	 * Remove Render Hooks
	 *
	 * Remove actions & filters after displaying our widget.
	 *
	 * @since 1.8.0
	 */
	public function remove_render_hooks() {
		remove_filter( 'gettext', array( $this, 'filter_gettext' ), 20 );

		remove_action( 'woocommerce_before_cart', array( $this, 'woocommerce_before_cart' ) );
		remove_action( 'woocommerce_after_cart_table', array( $this, 'woocommerce_after_cart_table' ) );
		remove_action( 'woocommerce_before_cart_table', array( $this, 'woocommerce_before_cart_table' ) );
		remove_action( 'woocommerce_before_cart_collaterals', array( $this, 'woocommerce_before_cart_collaterals' ) );
		remove_action( 'woocommerce_after_cart', array( $this, 'woocommerce_after_cart' ) );
		remove_filter( 'woocommerce_coupons_enabled', array( $this, 'hide_coupon_field_on_cart' ) );
		remove_filter( 'woocommerce_get_remove_url', array( $this, 'woocommerce_get_remove_url' ) );
		remove_action( 'woocommerce_cart_contents', array( $this, 'disable_cart_coupon' ) );
		remove_action( 'woocommerce_after_cart_contents', array( $this, 'enable_cart_coupon' ) );
		remove_action( 'woocommerce_get_cart_url', array( $this, 'woocommerce_get_cart_url' ) );
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

		if ( $this->has_empty_cart_template() ) {
			add_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
		}
	}

	public function render() {
		// Add actions & filters before displaying our Widget.
		$this->add_render_hooks();

		// Display our Widget.
		if ( $this->has_empty_cart_template() && WC()->cart->get_cart_contents_count() === 0 ) {
			$template_id = intval( $this->get_settings_for_display( 'additional_options_template_select' ) );

			echo do_shortcode( '[elementor-template id="' . $template_id . '"]' );
		} else {
			echo do_shortcode( '[woocommerce_cart]' );
		}

		// Remove actions & filters after displaying our Widget.
		$this->remove_render_hooks();
	}

	/**
	 * Woocommerce Before Cart
	 *
	 * Output containing elements. Callback function for the woocommerce_before_cart hook
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_before_cart() {
		?>
		<div class="e-cart__container">
			<!--open container-->
			<div class="e-cart__column e-cart__column-start">
				<!--open column-1-->
		<?php
	}

	/**
	 * Woocommerce After Cart Table
	 *
	 * Output containing elements. Callback function for the woocommerce_after_cart_table hook
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_after_cart_table() {
		?>
			</div>
		<!--close shop table div -->
		<div class="e-clear"></div>
		<?php

		if ( $this->should_render_coupon() ) {
			$this->render_woocommerce_cart_coupon_form();
		}
	}

	/**
	 * Should Render Coupon
	 *
	 * Decide if the coupon form should be rendered.
	 * The coupon form should be rendered if:
	 * 1) The WooCommerce setting is enabled
	 * 2) And the Coupon Display toggle hasn't been set to 'no'
	 *
	 * @since 1.8.0
	 *
	 * @return boolean
	 */
	private function should_render_coupon() {
		$settings = $this->get_settings_for_display();

		$coupon_display = ( isset( $settings['coupon_display'] ) ? $settings['coupon_display'] : '' );
		$coupon_display_control = true;

		if ( '' === $coupon_display ) {
			$coupon_display_control = false;
		}

		return wc_coupons_enabled() && $coupon_display_control;
	}

	/**
	 * Render Woocommerce Cart Coupon Form
	 *
	 * A custom function to render a coupon form on the Cart widget. The default WC coupon form
	 * was removed in this file's render() method.
	 *
	 * We are doing this in order to match the placement of the coupon form to the provided design.
	 *
	 * @since 1.8.0
	 */
	private function render_woocommerce_cart_coupon_form() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'button_coupon', array(
				'class' => array( 'button', 'e-apply-coupon' ),
				'name' => 'apply_coupon',
				'type' => 'submit',
			)
		);

		$input_placeholder = ( isset( $settings['coupon_input_placeholder'] ) && ! empty( $settings['coupon_input_placeholder'] ) )
			? esc_attr( $settings['coupon_input_placeholder'] )
			: esc_attr__( 'Coupon code', 'cmsmasters-elementor' );

		$button_text = ( isset( $settings['coupon_button_text'] ) && ! empty( $settings['coupon_button_text'] ) )
			? $settings['coupon_button_text']
			: __( 'Apply coupon', 'cmsmasters-elementor' );

		?>
		<div class="coupon e-cart-section shop_table">
			<div class="form-row coupon-col">
				<div class="coupon-col-start">
					<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php echo $input_placeholder; ?>" />
				</div>
				<div class="coupon-col-end">
					<button <?php $this->print_render_attribute_string( 'button_coupon' ); ?> value="<?php echo esc_attr( $button_text ); ?>">
						<?php echo esc_html( $button_text ); ?>
					</button>
				</div>
				<?php do_action( 'woocommerce_cart_coupon' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Woocommerce Before Cart Table
	 *
	 * Output containing elements. Callback function for the woocommerce_before_cart_table hook
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_before_cart_table() {
		$section_classes = array(
			'e-shop-table',
			'e-cart-section',
		);

		if ( ! $this->should_render_coupon() ) {
			$section_classes[] = 'e-cart-section--no-coupon';
		}

		$this->add_render_attribute(
			'before_cart_table', array(
				'class' => $section_classes,
			)
		);

		?>
		<div <?php $this->print_render_attribute_string( 'before_cart_table' ); ?>>
			<!--open shop table div -->
		<?php
	}

	/**
	 * Woocommerce Before Cart Collaterals
	 *
	 * Output containing elements. * Callback function for the woocommerce_before_cart_collaterals hook
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_before_cart_collaterals() {
		?>
		<!--close column-1-->
		</div>
		<div class="e-cart__column e-cart__column-end">
			<!--open column-2-->
				<div class="e-cart__column-inner e-sticky-right-column">
					<!--open column-inner-->
					<div class="e-cart-totals e-cart-section">
						<!--open cart-totals-->
		<?php
	}

	/**
	 * Woocommerce After Cart
	 *
	 * Output containing elements. Callback function for the woocommerce_after_cart hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_after_cart() {
		?>
						<!--close cart-totals-->
						</div>
						<!--close column-inner-->
					</div>
				<!--close column-2-->
			</div>
			<!--close container-->
		</div>
		<?php
	}

	/**
	 * The following disabling of cart coupon needs to be done this way so that
	 * we only disable the display of coupon interface in our cart widget and
	 * `wc_coupons_enabled()` can still be reliably used elsewhere.
	 */
	public function disable_cart_coupon() {
		add_filter( 'woocommerce_coupons_enabled', array( $this, 'cart_coupon_return_false' ), 90 );
	}

	public function enable_cart_coupon() {
		remove_filter( 'woocommerce_coupons_enabled', array( $this, 'cart_coupon_return_false' ), 90 );
	}

	public function cart_coupon_return_false() {
		return false;
	}

	/**
	 * WooCommerce Get Cart Url
	 *
	 * Used with the `woocommerce_get_cart_url`. This sets the url to the current page, so links like the `remove_url`
	 * wre set to the current page, and not the existing WooCommerce cart endpoint.
	 *
	 * @since 1.8.0
	 *
	 * @param $url
	 * @return string
	 */
	public function woocommerce_get_cart_url( $url ) {
		global $post;

		if ( ! $post ) {
			return $url;
		}

		if ( Plugin::$instance->preview->is_preview_mode() || Plugin::$instance->editor->is_edit_mode() ) {
			return Plugin::$instance->documents->get_current()->get_wp_preview_url();
		}

		return get_permalink( $post->ID );
	}

	/**
	 * Check if an Elementor template has been selected to display the empty cart notification
	 *
	 * @since 1.8.0
	 * @return boolean
	 */
	protected function has_empty_cart_template() {
		$additional_options_template_select = $this->get_settings_for_display( 'additional_options_template_select' );

		return ! empty( $additional_options_template_select ) && 0 < $additional_options_template_select;
	}

	public function hide_coupon_field_on_cart( $enabled ) {
		return is_cart() ? false : $enabled;
	}

	/**
	 * WooCommerce Get Remove URL.
	 *
	 * When in the Editor or (wp preview) and the uer clicks to remove an item from the cart, WooCommerce uses
	 * the`_wp_http_referer` url during the ajax call to generate the new cart html. So when we're in the Editor
	 * or (wp preview) we modify the `_wp_http_referer` to use the `get_wp_preview_url()` which will have
	 * the new cart content.
	 *
	 * @since 1.8.0
	 *
	 * @param $url
	 * @return string
	 */
	public function woocommerce_get_remove_url( $url ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.7.0' );

		$url_components = wp_parse_url( $url );

		if ( ! isset( $url_components['query'] ) ) {
			return $url;
		}

		$params = array();

		parse_str( html_entity_decode( $url_components['query'] ), $params );

		$params['_wp_http_referer'] = rawurlencode( Plugin::$instance->documents->get_current()->get_wp_preview_url() );

		return add_query_arg( $params, get_site_url() );
	}
}
