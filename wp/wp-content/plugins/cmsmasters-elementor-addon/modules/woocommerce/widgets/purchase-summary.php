<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Purchase_Summary extends Base_Widget {

	use Woo_Widget;

	private $order_id = null;
	private $order_key = null;

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
		return esc_html__( 'Purchase Summary', 'cmsmasters-elementor' );
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
		return 'cmsicon-purchase-summary';
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
			'summary',
			'thank you',
			'confirmation',
			'purchase',
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
	 * @since 1.8.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->register_confirmation_message_controls_content();

		$this->register_payment_details_controls_content();

		$this->register_bank_details_controls_content();

		$this->register_downloads_controls_content();

		$this->register_order_summary_controls_content();

		$this->register_billing_details_controls_content();

		$this->register_shipping_details_controls_content();

		$this->register_preview_order_controls_content();

		$this->register_sections_controls_style();

		$this->register_typography_controls_style();

		$this->register_payment_details_controls_style();

		$this->register_bank_details_controls_style();

		$this->register_order_details_controls_style();

		$this->register_buttons_controls_style();
	}

	protected function register_confirmation_message_controls_content() {
		$this->start_controls_section(
			'confirmation_message',
			array( 'label' => esc_html__( 'Confirmation Message', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'confirmation_message_active',
			array(
				'label' => esc_html__( 'Confirmation Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'selectors' => array(
					'{{WRAPPER}}' => '--confirmation-message-display: block;',
				),
			)
		);

		$this->add_control(
			'confirmation_message_text',
			array(
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Thank You. Your order has been received.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array( 'confirmation_message_active!' => '' ),
			)
		);

		$this->add_responsive_control(
			'confirmation_message_alignment',
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
					'{{WRAPPER}}' => '--confirmation-message-alignment: {{VALUE}};',
				),
				'condition' => array( 'confirmation_message_active!' => '' ),
			)
		);

		$this->add_control(
			'general_default_purchase_summary_page',
			array(
				'raw' => __( 'You can set the default Purchase Summary page by going to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=go_theme_settings" target="_blank">' . __( 'Theme Settings', 'cmsmasters-elementor' ) . '</a>' . __( ' -> WooCommerce -> WooCommerce Pages', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();
	}

	protected function register_payment_details_controls_content() {
		$this->start_controls_section(
			'payment_details',
			array( 'label' => esc_html__( 'Payment Details', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'payment_details_number',
			array(
				'label' => esc_html__( 'Number', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Order Number:', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_details_date',
			array(
				'label' => esc_html__( 'Date:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Order Date:', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_details_email',
			array(
				'label' => esc_html__( 'Email', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Order Email:', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_details_total',
			array(
				'label' => esc_html__( 'Total', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Order Total:', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_details_payment',
			array(
				'label' => esc_html__( 'Payment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Payment Method:', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_bank_details_controls_content() {
		$this->start_controls_section(
			'bank_details',
			array( 'label' => esc_html__( 'Bank Details', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'bank_details_text',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Our Bank Details', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'bank_details_alignment',
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
					'{{WRAPPER}}' => '--bank-details-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_downloads_controls_content() {
		$this->start_controls_section(
			'downloads',
			array( 'label' => esc_html__( 'Downloads', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'downloads_text',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Downloads', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'downloads_alignment',
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
					'{{WRAPPER}}' => '--downloads-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_order_summary_controls_content() {
		$this->start_controls_section(
			'order_summary',
			array( 'label' => esc_html__( 'Purchase Summary', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'order_summary_text',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Order Details', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'order_summary_alignment',
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
					'{{WRAPPER}}' => '--order-summary-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_billing_details_controls_content() {
		$this->start_controls_section(
			'billing_details',
			array( 'label' => esc_html__( 'Billing Details', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'billing_details_text',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Billing Details', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'billing_details_alignment',
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
					'{{WRAPPER}}' => '--billing-details-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_shipping_details_controls_content() {
		$this->start_controls_section(
			'shipping_details',
			array( 'label' => esc_html__( 'Shipping Address', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'shipping_details_text',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Shipping Details', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'shipping_details_alignment',
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
					'{{WRAPPER}}' => '--shipping-details-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_preview_order_controls_content() {
		$this->start_controls_section(
			'preview_order',
			array( 'label' => esc_html__( 'Preview Settings', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'preview_order_type',
			array(
				'label' => esc_html__( 'Preview order with', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => 'Latest Order',
					'custom-order' => 'Order ID',
				),
			)
		);

		$this->add_control(
			'preview_order_custom',
			array(
				'label' => esc_html__( 'Order ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'render_type' => 'template',
				'description' => esc_html__( 'Note: To find an order ID, go to the WP dashboard: WooCommerce > Orders', 'cmsmasters-elementor' ),
				'condition' => array( 'preview_order_type' => 'custom-order' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_sections_controls_style() {
		$this->start_controls_section(
			'sections_style',
			array(
				'label' => esc_html__( 'Sections', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'sections_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sections_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-border-color: {{VALUE}};',
				),
				'condition' => array( 'sections_border_type!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'sections_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sections_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sections_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'sections_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--sections-border-top-width: {{TOP}}{{UNIT}}; --sections-border-right-width: {{RIGHT}}{{UNIT}}; --sections-border-bottom-width: {{BOTTOM}}{{UNIT}}; --sections-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'sections_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'sections_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--sections-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'sections_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'sections_titles_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--titles-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_typography_controls_style() {
		$this->start_controls_section(
			'typography_section_style',
			array(
				'label' => esc_html__( 'Typography', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'typography_confirmation_message_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Confirmation Message', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_confirmation_message_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_confirmation_message_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-confirmation-message-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'typography_confirmation_message_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-confirmation-message-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-titles-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'typography_titles_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_general_text_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'General Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_general_text_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-general-text-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_general_text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-general-text-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_payment_details_controls_style() {
		$this->start_controls_section(
			'payment_details_section_style',
			array(
				'label' => esc_html__( 'Payment Details', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'payment_details_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'payment_details_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'payment_details_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'payment_details_titles_typography',
				'exclude' => array( 'text_decoration' ),
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'payment_details_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-titles-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'payment_details_titles_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'payment_details_titles_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-titles-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'payment_details_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'payment_details_items_typography',
				'exclude' => array( 'text_decoration' ),
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--payment-details-items-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'payment_details_items_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-items-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'payment_details_dividers_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Dividers', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'payment_details_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'payment_details_border_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'payment_details_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'payment_details_border_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-border-color: {{VALUE}};',
				),
				'condition' => array( 'payment_details_border_type!' => 'none' ),
			)
		);

		$this->add_control(
			'payment_details_general_text_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'General Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'payment_details_general_text_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--payment-details-general-text-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_bank_details_controls_style() {
		$this->start_controls_section(
			'bank_details_section_style',
			array(
				'label' => esc_html__( 'Bank Details', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'bank_details_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bank_details_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'default' => array( 'px' => 0 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bank_details_account_title_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Account Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'bank_details_account_title_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'bank_details_account_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-account-title-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'bank_details_account_title_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-account-title-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'bank_details_account_title_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-account-title-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bank_details_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'bank_details_titles_typography',
				'exclude' => array( 'text_decoration' ),
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'bank_details_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-titles-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'bank_details_titles_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'bank_details_titles_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-titles-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bank_details_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'bank_details_items_typography',
				'exclude' => array( 'text_decoration' ),
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--bank-details-items-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'bank_details_items_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-items-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bank_details_dividers_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Dividers', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'bank_details_dividers_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-dividers-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bank_details_dividers_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-dividers-border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'bank_details_dividers_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'bank_details_dividers_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--bank-details-dividers-border-color: {{VALUE}};',
				),
				'condition' => array( 'bank_details_dividers_border_type!' => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_order_details_controls_style() {
		$this->start_controls_section(
			'order_details_section_style',
			array(
				'label' => esc_html__( 'Order Details', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'order_details_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 75,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'order_details_rows_gap',
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
					'{{WRAPPER}}' => '--order-details-rows-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'order_details_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'order_details_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-titles-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'order_details_titles_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_totals_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Totals', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'order_details_totals_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_totals_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-totals-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'order_details_totals_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-totals-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'order_details_items_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-items-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_items_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-items-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'order_details_variations_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Variations', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'order_details_variations_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--order-details-variations-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'order_details_variations_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-variations-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'order_details_product_links_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Product Link', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'order_details_product_links_colors' );

		$this->start_controls_tab(
			'order_details_product_links_normal_colors',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'order_details_product_links_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-product-links-normal-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'order_details_product_links_hover_colors',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'order_details_product_links_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-product-links-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'order_details_dividers_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Dividers', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'order_details_dividers_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-dividers-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'order_details_dividers_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-dividers-border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'order_details_dividers_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'order_details_dividers_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--order-details-dividers-border-color: {{VALUE}};',
				),
				'condition' => array( 'order_details_dividers_border_type!' => 'none' ),
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

		$this->start_controls_tabs( 'buttons_styles' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'normal' === $main_key ? ':before' : ':after' );
			$buttons_bg_selector = "{{WRAPPER}} .shop_table .button{$state}, {{WRAPPER}} .order-again .button{$state}";

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

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"buttons_{$main_key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'selectors' => array(
							'{{WRAPPER}}' => "--buttons-{$main_key}-transition-duration: {{SIZE}}ms",
						),
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
					)
				);

				$this->add_control(
					"buttons_{$main_key}_animation",
					array(
						'label' => esc_html__( 'Hover Animation', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::HOVER_ANIMATION,
						'frontend_available' => true,
						'render_type' => 'template',
					)
				);
			}

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

	protected function render() {
		$is_editor = Plugin::$instance->editor->is_edit_mode();
		$is_preview = Plugin::$instance->preview->is_preview_mode();

		if ( $is_editor || $is_preview ) {
			$this->set_preview_order();

			add_filter( 'woocommerce_thankyou_order_id', array( $this, 'get_modified_order_id' ) );
			add_filter( 'woocommerce_thankyou_order_key', array( $this, 'get_modified_order_key' ) );

			/**
			 * The action `template_redirect` is not run during the re-loading of the Widget and as a result the
			 * `wc_template_redirect` function is not run which is responsible for loading the following, so we
			 * must load them ourselves.
			 */
			WC()->payment_gateways();
			WC()->shipping();
		}

		/*
		 * Add actions & filters before displaying our Widget.
		 */
		add_filter( 'gettext', array( $this, 'filter_gettext' ), 20, 3 );
		add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'modify_order_received_text' ) );

		/**
		 * Display our Widget.
		 */
		global $wp;

		if ( isset( $wp->query_vars['order-received'] ) && wc_get_order( intval( $wp->query_vars['order-received'] ) ) ) {
			echo do_shortcode( '[woocommerce_checkout]' );
		} elseif ( $is_editor || $is_preview ) {
			$this->no_order_notice();
		}

		/*
		 * Remove actions & filters after displaying our Widget.
		 */
		remove_filter( 'gettext', array( $this, 'filter_gettext' ), 20 );
		remove_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'modify_order_received_text' ) );

		if ( $is_editor || $is_preview ) {
			remove_filter( 'woocommerce_thankyou_order_id', array( $this, 'get_modified_order_id' ) );
			remove_filter( 'woocommerce_thankyou_order_key', array( $this, 'get_modified_order_key' ) );
		}
	}

	public function set_preview_order() {
		$instance = $this->get_settings_for_display();
		$order = false;

		if ( 'custom-order' === $instance['preview_order_type'] ) {
			$order = wc_get_order( $instance['preview_order_custom'] );
		}

		if ( ! $order ) {
			$latest_order = wc_get_orders( array(
				'limit' => 1,
				'orderby'  => 'date',
				'order'    => 'DESC',
				'return'   => 'ids',
			) );

			if ( isset( $latest_order[0] ) ) {
				$order = wc_get_order( $latest_order[0] );
			}
		}

		if ( $order ) {
			global $wp;

			$wp->set_query_var( 'order-received', $order->get_id() );

			$this->order_id = $order->get_id();
			$this->order_key = $order->get_order_key();
		}
	}

	public function get_modified_order_id() {
		return $this->order_id;
	}

	public function get_modified_order_key() {
		return $this->order_key;
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
		if ( 'woocommerce' !== $domain ) {
			return $translation;
		}

		if ( ! isset( $this->gettext_modifications ) ) {
			$this->init_gettext_modifications();
		}

		return array_key_exists( $text, $this->gettext_modifications ) ? $this->gettext_modifications[ $text ] : $translation;
	}

	/**
	 * Init Gettext Modifications
	 *
	 * Sets the `$gettext_modifications` property used with the `filter_gettext()` in the extended Base_Widget.
	 *
	 * @since 1.8.0
	 */
	protected function init_gettext_modifications() {
		$instance = $this->get_settings_for_display();

		$this->gettext_modifications = array(
			'Order number:' => isset( $instance['payment_details_number'] ) ? $instance['payment_details_number'] : '',
			'Date:' => isset( $instance['payment_details_date'] ) ? $instance['payment_details_date'] : '',
			'Email:' => isset( $instance['payment_details_email'] ) ? $instance['payment_details_email'] : '',
			'Total:' => isset( $instance['payment_details_total'] ) ? $instance['payment_details_total'] : '',
			'Payment method:' => isset( $instance['payment_details_payment'] ) ? $instance['payment_details_payment'] : '',
			'Our bank details' => isset( $instance['bank_details_text'] ) ? $instance['bank_details_text'] : '',
			'Order details' => isset( $instance['order_summary_text'] ) ? $instance['order_summary_text'] : '',
			'Billing address' => isset( $instance['billing_details_text'] ) ? $instance['billing_details_text'] : '',
			'Shipping address' => isset( $instance['shipping_details_text'] ) ? $instance['shipping_details_text'] : '',
			'Downloads' => isset( $instance['downloads_text'] ) ? $instance['downloads_text'] : '',
		);
	}

	/**
	 * Modify Order Received Text.
	 *
	 * @since 1.8.0
	 *
	 * @param $text
	 * @return string
	 */
	public function modify_order_received_text( $text ) {
		$instance = $this->get_settings_for_display();

		if ( isset( $instance['confirmation_message_text'] ) ) {
			$text = $instance['confirmation_message_text'];
		}

		return $text;
	}

	public function no_order_notice() {
		?>
		<div class="woocommerce-error" role="alert">
			<?php echo esc_html__( 'You need at least one WooCommerce order to preview the order here.', 'cmsmasters-elementor' ); ?>
		</div>
		<?php
	}
}
