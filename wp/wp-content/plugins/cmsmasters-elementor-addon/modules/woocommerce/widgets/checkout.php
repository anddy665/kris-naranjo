<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Checkout extends Base_Widget {

	use Woo_Widget;

	private $reformatted_form_fields;

	protected $gettext_modifications;

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
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Checkout', 'cmsmasters-elementor' );
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
		return 'cmsicon-checkout';
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
			'checkout',
			'page',
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

	public function get_help_url() {
		return 'https://go.elementor.com/widget-woocommerce-checkout';
	}

	public function get_script_depends() {
		return array(
			'wc-checkout',
			'wc-password-strength-meter',
			'selectWoo',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.8.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->register_general_controls_content();

		if ( $this->is_wc_feature_active( 'checkout_login_reminder' ) ) {
			$this->add_returning_customer_controls_controls();
		}

		$this->register_billing_details_controls_content();

		if ( $this->is_wc_feature_active( 'shipping' ) && ! $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ) {
			$this->add_shipping_details_controls_content();
		}

		$this->register_additional_information_controls_content();

		if ( $this->is_wc_feature_active( 'signup_and_login_from_checkout' ) ) {
			$this->add_create_account_controls_content();
		}

		$this->register_your_order_controls_content();

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$this->add_coupon_controls_content();
		}

		$this->register_payment_controls_content();

		$this->register_sections_controls_style();

		$this->register_typography_controls_style();

		$this->register_forms_controls_style();

		$this->register_buttons_controls_style();

		$this->register_customize_controls_style();

		$this->register_customize_returning_customer_controls_style();

		$this->register_customize_billing_details_controls_style();

		if ( $this->is_wc_feature_active( 'shipping' ) ) {
			$this->add_customize_shipping_address_controls_style();
		}

		$this->register_customize_additional_info_controls_style();

		$this->register_customize_order_summary_controls_style();

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$this->add_customize_coupon_controls_style();
		}

		$this->register_customize_payment_controls_style();
	}

	protected function register_general_controls_content() {
		$this->start_controls_section(
			'general_section_content',
			array( 'label' => esc_html__( 'General', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'general_layout',
			array(
				'label' => esc_html__( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'two-column' => esc_html__( 'Two columns', 'cmsmasters-elementor' ),
					'one-column' => esc_html__( 'One column', 'cmsmasters-elementor' ),
				),
				'default' => 'two-column',
				'prefix_class' => 'cmsmasters-checkout-layout-',
			)
		);

		$this->add_control(
			'general_default_checkout_page',
			array(
				'raw' => __( 'You can set the default Checkout page by going to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=go_theme_settings" target="_blank">' . __( 'Theme Settings', 'cmsmasters-elementor' ) . '</a>' . __( ' -> WooCommerce -> WooCommerce Pages', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();
	}

	private function add_returning_customer_controls_controls() {
		$this->start_controls_section(
			'returning_customer_content',
			array( 'label' => esc_html__( 'Returning Customer', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'returning_customer_title_alignment',
			array(
				'label' => esc_html__( 'Title Alignment', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--returning-customer-title-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'returning_customer_button_alignment',
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
					'start' => '--returning-customer-button-alignment: start; --returning-customer-button-width: fit-content;',
					'center' => '--returning-customer-button-alignment: center;  --returning-customer-button-width: fit-content;',
					'end' => '--returning-customer-button-alignment: end;  --returning-customer-button-width: fit-content;',
					'justify' => '--returning-customer-button-alignment: center; --returning-customer-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'returning_customer_button_alignment_note',
			array(
				'raw' => esc_html__( 'Note: This control will only affect screen sizes Tablet and below', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	protected function register_billing_details_controls_content() {
		$this->start_controls_section(
			'billing_details_section_content',
			array(
				'label' => $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ? esc_html__( 'Billing and Shipping Details', 'cmsmasters-elementor' ) : esc_html__( 'Billing Details', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'billing_details_title_alignment',
			array(
				'label' => esc_html__( 'Title Alignment', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--billing-details-title-alignment: {{VALUE}};',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs(
			'tabs',
			array( 'condition' => array( 'repeater_state' => '' ) )
		);

		$repeater->start_controls_tab(
			'content_tab',
			array( 'label' => esc_html__( 'Content', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'label',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'placeholder',
			array(
				'label' => esc_html__( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'advanced_tab',
			array( 'label' => esc_html__( 'Advanced', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'default',
			array(
				'label' => esc_html__( 'Default Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_control(
			'repeater_state',
			array(
				'label' => esc_html__( 'Repeater State - hidden', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
			)
		);

		$repeater->add_control(
			'locale_notice',
			array(
				'raw' => __( 'Note: This content cannot be changed due to local regulations.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'repeater_state' => 'locale' ),
			)
		);

		$repeater->add_control(
			'from_billing_notice',
			array(
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'repeater_state' => 'from_billing' ),
			)
		);

		$this->add_control(
			'billing_details_form_fields',
			array(
				'label' => esc_html__( 'Form Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => array(
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				),
				'default' => $this->get_billing_field_defaults(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	private function add_shipping_details_controls_content() {
		$this->start_controls_section(
			'shipping_details_section_content',
			array( 'label' => esc_html__( 'Shipping Details', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'shipping_details_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Ship to a different address?', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'repeater_state',
			array(
				'label' => esc_html__( 'Repeater State - hidden', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
			)
		);

		$repeater->add_control(
			'label_placeholder_notification',
			array(
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'repeater_state' => 'from_billing' ),
			)
		);

		$repeater->start_controls_tabs(
			'tabs',
			array( 'condition' => array( 'repeater_state' => '' ) )
		);

		$repeater->start_controls_tab(
			'content_tab',
			array( 'label' => esc_html__( 'Content', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'label',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'placeholder',
			array(
				'label' => esc_html__( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'advanced_tab',
			array( 'label' => esc_html__( 'Advanced', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'default',
			array(
				'label' => esc_html__( 'Default Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$repeater->add_control(
			'locale_notice',
			array(
				'raw' => __( 'Note: This content cannot be changed due to local regulations.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'repeater_state' => 'locale' ),
			)
		);

		$repeater->add_control(
			'from_billing_notice',
			array(
				'raw' => __( 'Note: This label and placeholder are taken from the Billing section. You can change it there.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'repeater_state' => 'from_billing' ),
			)
		);

		$this->add_control(
			'shipping_details_form_fields',
			array(
				'label' => esc_html__( 'Form Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => array(
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				),
				'default' => $this->get_shipping_field_defaults(),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();
	}

	protected function register_additional_information_controls_content() {
		$this->start_controls_section(
			'additional_information_section_content',
			array( 'label' => esc_html__( 'Additional Information', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'additional_information_display',
			array(
				'label' => esc_html__( 'Additional Information', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'selectors' => array(
					'{{WRAPPER}}' => '--additional-information-display: block;',
				),
			)
		);

		if ( $this->is_wc_feature_active( 'ship_to_billing_address_only' ) ) {
			$this->add_control(
				'additional_information_title',
				array(
					'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'Additional Information', 'cmsmasters-elementor' ),
					'default' => esc_html__( 'Additional Information', 'cmsmasters-elementor' ),
					'dynamic' => array(
						'active' => true,
					),
					'condition' => array( 'additional_information_display!' => '' ),
				)
			);

			$this->add_responsive_control(
				'additional_information_title_alignment',
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
						'{{WRAPPER}}' => '--additional-information-title-alignment: {{VALUE}};',
					),
					'condition' => array( 'additional_information_display!' => '' ),
				)
			);
		}

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'additional_information_form_fields_tabs' );

		$repeater->start_controls_tab(
			'additional_information_form_fields_content_tab',
			array( 'label' => esc_html__( 'Content', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'label',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'placeholder',
			array(
				'label' => esc_html__( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'additional_information_form_fields_advanced_tab',
			array( 'label' => esc_html__( 'Advanced', 'cmsmasters-elementor' ) )
		);

		$repeater->add_control(
			'default',
			array(
				'label' => esc_html__( 'Default Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'additional_information_form_fields',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'item_actions' => array(
					'add' => false,
					'duplicate' => false,
					'remove' => false,
					'sort' => false,
				),
				'default' => array(
					array(
						'field_key' => 'order_comments',
						'field_label' => esc_html__( 'Order Notes', 'cmsmasters-elementor' ),
						'label' => esc_html__( 'Order Notes', 'cmsmasters-elementor' ),
						'placeholder' => esc_html__( 'Notes about your order, e.g. special notes for delivery.', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '{{{ label }}}',
				'condition' => array( 'additional_information_display!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	private function add_create_account_controls_content() {
		$this->start_controls_section(
			'create_account_section_content',
			array( 'label' => esc_html__( 'Create an Account', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'create_account_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Create an account?', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_your_order_controls_content() {
		$this->start_controls_section(
			'your_order_section_content',
			array( 'label' => esc_html__( 'Your Order', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'your_order_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Your Order', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_responsive_control(
			'your_order_title_alignment',
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
					'{{WRAPPER}}' => '--your-order-title-alignment: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_coupon_controls_content() {
		$this->start_controls_section(
			'coupon_section_content',
			array( 'label' => esc_html__( 'Coupon', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'coupon_section_display',
			array(
				'label' => esc_html__( 'Coupon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'coupon_title_alignment',
			array(
				'label' => esc_html__( 'Title Alignment', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--coupon-title-alignment: {{VALUE}};',
				),
				'condition' => array( 'coupon_section_display' => 'yes' ),
			)
		);

		$this->add_responsive_control(
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
					'start' => '--coupon-button-alignment: start; --coupon-button-width: fit-content;',
					'center' => '--coupon-button-alignment: center; --coupon-button-width: fit-content;',
					'end' => '--coupon-button-alignment: end; --coupon-button-width: fit-content;',
					'justify' => '--coupon-button-alignment: justify; --coupon-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
				'condition' => array( 'coupon_section_display' => 'yes' ),
			)
		);

		$this->add_control(
			'coupon_button_alignment_note',
			array(
				'raw' => esc_html__( 'Note: This control will only affect screen sizes Tablet and below', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => array( 'coupon_section_display' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_payment_controls_content() {
		$this->start_controls_section(
			'payment_section_content',
			array( 'label' => esc_html__( 'Payment', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'payment_terms_conditions_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Terms &amp; Conditions', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_terms_conditions_message',
			array(
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'I have read and agree to the website', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'payment_terms_conditions_link_tex',
			array(
				'label' => esc_html__( 'Link Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'terms and conditions', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'payment_purchase_buttom_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Purchase Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'payment_purchase_button_alignment',
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
					'start' => '--payment-purchase-button-alignment: flex-start; --payment-purchase-button-width: fit-content;',
					'center' => '--payment-purchase-button-alignment: center; --payment-purchase-button-width: fit-content;',
					'end' => '--payment-purchase-button-alignment: flex-end; --payment-purchase-button-width: fit-content;',
					'justify' => '--payment-purchase-button-alignment: stretch; --payment-purchase-button-width: 100%;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
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
			$condition = array( 'section_checkout_show_customize_elements' => $customize_element );

			$condition_border_color = array(
				'section_checkout_show_customize_elements' => $customize_element,
				$section . 'border_type!' => 'none',
			);

			$condition_border_width = array(
				'section_checkout_show_customize_elements' => $customize_element,
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

		if ( 'customize_shipping_address' === $customize_element ) {
			$gap_selector = ' #ship-to-different-address';
		} elseif ( 'customize_order_summary' === $customize_element ) {
			$gap_selector = ' .e-checkout__container';
		} else {
			$gap_selector = $selector;
		}

		$var = ( 'customize_order_summary' === $customize_element ? '--customize-order-summary-section-item-gap' : '--sections-item-gap' );

		if (
			( $this->is_wc_feature_active( 'checkout_login_reminder' ) && 'customize_returning_customer' !== $customize_element ) ||
			( ! $this->is_wc_feature_active( 'checkout_login_reminder' ) && 'customize_billing_details' !== $customize_element )
		) {
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
						'{{WRAPPER}}' . $gap_selector => $var . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => $condition,
				)
			);
		}

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
			'sections_section_style',
			array(
				'label' => esc_html__( 'Sections', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->register_customize_general_controls_style( '', '', '' );

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
			'typography_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Titles', 'cmsmasters-elementor' ),
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

		$this->add_responsive_control(
			'typography_titles_spacing',
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
					'{{WRAPPER}}' => '--typography-titles-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'typography_secondary_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_secondary_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_secondary_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-secondary-titles-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'typography_secondary_titles_spacing',
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
					'{{WRAPPER}}' => '--typography-secondary-titles-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'typography_descriptions_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Descriptions', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_descriptions_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_descriptions_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-descriptions-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'typography_descriptions_spacing',
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
					'{{WRAPPER}}' => '--typography-descriptions-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'typography_messages_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Messages', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_messages_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_messages_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-messages-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'typography_checkboxes_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkboxes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_checkboxes_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_checkboxes_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-checkboxes-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'typography_radio_buttons_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Buttons', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_radio_buttons_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'typography_radio_buttons_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--typography-radio-buttons-color: {{VALUE}};',
				),
			)
		);

		// Links
		$this->add_control(
			'typography_links_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_links_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'typography_links_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"typography_links_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"typography_links_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--typography-links-{$main_key}-color: {{VALUE}};",
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

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
			'forms_columns_gap',
			array(
				'label' => esc_html__( 'Columns Gap', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--forms-columns-gap: {{SIZE}}{{UNIT}};',
				),
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
			'forms_labels_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Labels', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'forms_labels_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'forms_labels_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--forms-labels-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'forms_labels_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--forms-labels-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'forms_fields_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Fields', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'forms_fields_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-fields-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}, .e-woo-select2-wrapper .select2-results__option',
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
			$buttons_bg_selector = "{{WRAPPER}} .woocommerce-button{$state}, {{WRAPPER}} .button{$state}";

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

	private function register_customize_controls_style() {
		$this->start_controls_section(
			'customize_section_style',
			array(
				'label' => esc_html__( 'Customize', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$customize_options = array();

		if ( $this->is_wc_feature_active( 'checkout_login_reminder' ) ) {
			$customize_options += array(
				'customize_returning_customer' => esc_html__( 'Returning Customer', 'cmsmasters-elementor' ),
			);
		}

		$customize_options += array(
			'customize_billing_details' => esc_html__( 'Billing Details', 'cmsmasters-elementor' ),
		);

		if ( $this->is_wc_feature_active( 'shipping' ) ) {
			$customize_options += array(
				'customize_shipping_address' => esc_html__( 'Shipping Address', 'cmsmasters-elementor' ),
			);
		}

		$customize_options += array(
			'customize_additional_info' => esc_html__( 'Additional Information', 'cmsmasters-elementor' ),
			'customize_order_summary' => esc_html__( 'Order Summary', 'cmsmasters-elementor' ),
		);

		if ( $this->is_wc_feature_active( 'coupons' ) ) {
			$customize_options += array(
				'customize_coupon' => esc_html__( 'Coupon', 'cmsmasters-elementor' ),
			);
		}

		$customize_options += array(
			'customize_payment' => esc_html__( 'Payment', 'cmsmasters-elementor' ),
		);

		$this->add_control(
			'section_checkout_show_customize_elements',
			array(
				'label' => esc_html__( 'Select sections of the checkout to customize:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $customize_options,
				'render_type' => 'ui',
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	private function register_customize_returning_customer_controls_style() {
		$this->start_controls_section(
			'customize_returning_customer_section_style',
			array(
				'label' => esc_html__( 'Customize: Returning Customer', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_returning_customer', '.e-woocommerce-login-section', 'customize_returning_customer' );

		$this->add_control(
			'customize_returning_customer_secondary_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_returning_customer_secondary_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-form-login-toggle',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_secondary_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-form-login-toggle' => '--typography-secondary-titles-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_description_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_returning_customer_descriptions_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .e-woocommerce-login-nudge',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_descriptions_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-woocommerce-login-nudge' => '--typography-descriptions-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_checkboxes_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_returning_customer_checkboxes_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .e-woocommerce-login-section .woocommerce-form__label-for-checkbox span',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_checkboxes_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-woocommerce-login-section' => '--typography-checkboxes-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_control(
			'customize_returning_customer_links_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_returning_customer_links_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .e-woocommerce-login-section',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
			)
		);

		$this->start_controls_tabs(
			'customize_returning_customer_links_tabs',
			array( 'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ) )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"customize_returning_customer_links_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
				)
			);

			$this->add_control(
				"customize_returning_customer_links_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .e-woocommerce-login-section' => "--typography-links-{$main_key}-color: {{VALUE}};",
					),
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_returning_customer' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_customize_billing_details_controls_style() {
		$this->start_controls_section(
			'customize_billing_details_section_style',
			array(
				'label' => esc_html__( 'Customize: Billing Details', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_control(
			'customize_billing_details_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_billing_details', '.col2-set .col-1', 'customize_billing_details' );

		$this->add_control(
			'customize_billing_details_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_billing_details_titles_typography',
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
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields h3',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_control(
			'customize_billing_details_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .col2-set .col-1' => '--typography-titles-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'customize_billing_details_titles_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields h3',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_control(
			'customize_billing_details_checkboxes_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_billing_details_checkboxes_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .col2-set .col-1 .woocommerce-form__label-for-checkbox span',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->add_control(
			'customize_billing_details_checkboxes_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .col2-set .col-1' => '--typography-checkboxes-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_billing_details' ),
			)
		);

		$this->end_controls_section();
	}

	private function add_customize_shipping_address_controls_style() {
		$this->start_controls_section(
			'customize_shipping_address_section_style',
			array(
				'label' => esc_html__( 'Customize: Shipping Address', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_shipping_address' ),
			)
		);

		$this->add_control(
			'customize_shipping_address_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_shipping_address' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_shipping_address', '.woocommerce-shipping-fields .shipping_address', 'customize_shipping_address' );

		$this->add_control(
			'customize_shipping_address_checkboxes_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkboxes', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_shipping_address' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_shipping_address_checkboxes_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-shipping-fields .woocommerce-form__label-for-checkbox span',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_shipping_address' ),
			)
		);

		$this->add_control(
			'customize_shipping_address_checkboxes_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-shipping-fields' => '--typography-checkboxes-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_shipping_address' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_customize_additional_info_controls_style() {
		$this->start_controls_section(
			'customize_additional_info_section_style',
			array(
				'label' => esc_html__( 'Customize: Additional Information', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_additional_info' ),
			)
		);

		$this->add_control(
			'customize_additional_info_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_additional_info' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_additional_info', '.woocommerce-additional-fields', 'customize_additional_info' );

		$this->add_control(
			'customize_additional_info_labels_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Labels', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_additional_info' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_additional_info_labels_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--forms-labels-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-additional-fields',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_additional_info' ),
			)
		);

		$this->add_control(
			'customize_additional_info_labels_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-additional-fields' => '--forms-labels-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_additional_info' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_customize_order_summary_controls_style() {
		$this->start_controls_section(
			'customize_order_summary_section_style',
			array(
				'label' => esc_html__( 'Customize: Order Summary', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_order_summary', '.e-checkout__order_review', 'customize_order_summary' );

		$this->add_control(
			'customize_order_summary_title_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_title_typography',
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
				'selector' => '{{WRAPPER}} h3#order_review_heading',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-checkout__order_review' => '--typography-titles-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
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
							'{{SELECTOR}}' => '--typography-titles-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} h3#order_review_heading',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Titles', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_table_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-titles-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_titles_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-titles-bg-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Items', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_table_items_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-items-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_items_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-items-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_items_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-items-bg-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_responsive_control(
			'customize_order_summary_table_items_rows_gap',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-items-rows-gap: calc({{SIZE}}{{UNIT}} / 2);',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_totals_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Table Totals', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_table_totals_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--customize-order-summary-table-totals-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_totals_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-totals-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_table_totals_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--customize-order-summary-table-totals-bg-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_variations_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Variations', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
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
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
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
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_descriptions_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_descriptions_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-no-shipping-available-html.e-description, {{WRAPPER}} .woocommerce-no-shipping-available-html.e-checkout-message',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_descriptions_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-checkout__order_review' => '--typography-descriptions-color: {{VALUE}}; --typography-messages-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_radio_buttons_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_order_summary_radio_buttons_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce .e-checkout__order_review ul#shipping_method li label',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->add_control(
			'customize_order_summary_radio_buttons_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-checkout__order_review' => '--typography-radio-buttons-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_order_summary' ),
			)
		);

		$this->end_controls_section();
	}

	private function add_customize_coupon_controls_style() {
		$this->start_controls_section(
			'customize_coupon_section_style',
			array(
				'label' => esc_html__( 'Customize: Coupon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_control(
			'customize_coupon_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_coupon', '.e-coupon-box', 'customize_coupon' );

		$this->add_control(
			'customize_coupon_secondary_titles_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Secondary Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_coupon_secondary_titles_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-secondary-titles-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .e-woocommerce-coupon-nudge.e-checkout-secondary-title',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_control(
			'customize_coupon_secondary_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .e-woocommerce-coupon-nudge' => '--typography-secondary-titles-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_control(
			'customize_coupon_links_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_coupon_links_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .e-coupon-box',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
			)
		);

		$this->start_controls_tabs(
			'customize_coupon_links_tabs',
			array( 'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ) )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"customize_coupon_links_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
				)
			);

			$this->add_control(
				"customize_coupon_links_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .e-coupon-box' => "--typography-links-{$main_key}-color: {{VALUE}};",
					),
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_coupon' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_customize_payment_controls_style() {
		$this->start_controls_section(
			'customize_payment_section_style',
			array(
				'label' => esc_html__( 'Customize: Payment', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_section_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->register_customize_general_controls_style( 'customize_payment', '.woocommerce-checkout #payment', 'customize_payment' );

		$this->add_control(
			'customize_payment_info_box_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Info Box', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_info_box_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout #payment .payment_methods .payment_box' => '--forms-fields-normal-bg-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_description_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_payment_descriptions_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-descriptions-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-checkout-payment .e-description',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_descriptions_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--typography-descriptions-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_messages_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_payment_messages_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-messages-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-checkout #payment .payment_box, {{WRAPPER}} .woocommerce-privacy-policy-text p',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_messages_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--typography-messages-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_checkboxes_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Checkbox', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_payment_checkboxes_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-checkboxes-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-terms-and-conditions-wrapper .woocommerce-form__label-for-checkbox span',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_checkboxes_checkboxes_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-terms-and-conditions-wrapper' => '--typography-checkboxes-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_radio_buttons_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Radio Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_payment_radio_buttons_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-radio-buttons-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-checkout-payment .wc_payment_method label',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_radio_buttons_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-payment' => '--typography-radio-buttons-color: {{VALUE}};',
				),
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_control(
			'customize_payment_links_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Links', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'customize_payment_links_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--typography-links-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-checkout-payment',
				'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
			)
		);

		$this->start_controls_tabs(
			'customize_payment_links_tabs',
			array( 'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ) )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"customize_payment_links_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
				)
			);

			$this->add_control(
				"customize_payment_links_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .woocommerce-checkout-payment' => "--typography-links-{$main_key}-color: {{VALUE}};",
					),
					'condition' => array( 'section_checkout_show_customize_elements' => 'customize_payment' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Is WooCommerce Feature Active.
	 *
	 * Checks whether a specific WooCommerce feature is active. These checks can sometimes look at multiple WooCommerce
	 * settings at once so this simplifies and centralizes the checking.
	 *
	 * @since 3.5.0
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
	 * Get Billing Field Defaults
	 *
	 * Get defaults used for the billing details repeater control.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	private function get_billing_field_defaults() {
		$fields = array(
			'billing_first_name' => array(
				'label' => esc_html__( 'First Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'billing_last_name' => array(
				'label' => esc_html__( 'Last Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'billing_company' => array(
				'label' => esc_html__( 'Company Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'billing_country' => array(
				'label' => esc_html__( 'Country / Region', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'billing_address_1' => array(
				'label' => esc_html__( 'Street Address', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'billing_postcode' => array(
				'label' => esc_html__( 'Post Code', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'billing_city' => array(
				'label' => esc_html__( 'Town / City', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'billing_state' => array(
				'label' => esc_html__( 'State', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'billing_phone' => array(
				'label' => esc_html__( 'Phone', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'billing_email' => array(
				'label' => esc_html__( 'Email Address', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
		);

		return $this->reformat_address_field_defaults( $fields );
	}

	/**
	 * Get Shipping Field Defaults
	 *
	 * Get defaults used for the shipping details repeater control.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	private function get_shipping_field_defaults() {
		$fields = array(
			'shipping_first_name' => array(
				'label' => esc_html__( 'First Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'shipping_last_name' => array(
				'label' => esc_html__( 'Last Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'shipping_company' => array(
				'label' => esc_html__( 'Company Name', 'cmsmasters-elementor' ),
				'repeater_state' => '',
			),
			'shipping_country' => array(
				'label' => esc_html__( 'Country / Region', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'shipping_address_1' => array(
				'label' => esc_html__( 'Street Address', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'shipping_postcode' => array(
				'label' => esc_html__( 'Post Code', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'shipping_city' => array(
				'label' => esc_html__( 'Town / City', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
			'shipping_state' => array(
				'label' => esc_html__( 'State', 'cmsmasters-elementor' ),
				'repeater_state' => 'locale',
			),
		);

		return $this->reformat_address_field_defaults( $fields );
	}

	/**
	 * Reformat Address Field Defaults
	 *
	 * Used with the `get_..._field_defaults()` methods.
	 * Takes the address array and converts it into the format expected by the repeater controls.
	 *
	 * @since 1.8.0
	 *
	 * @param $address
	 * @return array
	 */
	private function reformat_address_field_defaults( $address ) {
		$defaults = array();

		foreach ( $address as $key => $value ) {
			$defaults[] = array(
				'field_key' => $key,
				'field_label' => $value['label'],
				'label' => $value['label'],
				'placeholder' => $value['label'],
				'repeater_state' => $value['repeater_state'],
			);
		}

		return $defaults;
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

		// Simulate a logged out user so that all WooCommerce sections will render in the Editor
		if ( $is_editor ) {
			$store_current_user = wp_get_current_user()->ID;

			wp_set_current_user( 0 );
		}

		// Add actions & filters before displaying our Widget.
		$this->add_render_hooks();

		// Display our Widget.
		echo do_shortcode( '[woocommerce_checkout]' );

		// Remove actions & filters after displaying our Widget.
		$this->remove_render_hooks();

		// Return to existing logged-in user after widget is rendered.
		if ( $is_editor ) {
			wp_set_current_user( $store_current_user );
		}
	}

	/**
	 * Add Render Hooks
	 *
	 * Add actions & filters before displaying our widget.
	 *
	 * @since 1.8.0
	 */
	public function add_render_hooks() {
		add_filter( 'woocommerce_form_field_args', array( $this, 'modify_form_field' ), 70, 3 );
		add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', array( $this, 'woocommerce_terms_and_conditions_checkbox_text' ), 10, 1 );

		add_filter( 'gettext', array( $this, 'filter_gettext' ), 20, 3 );

		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'woocommerce_checkout_before_customer_details' ), 5 );
		add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'woocommerce_checkout_after_customer_details' ), 95 );
		add_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'woocommerce_checkout_before_order_review_heading_1' ), 5 );
		add_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'woocommerce_checkout_before_order_review_heading_2' ), 95 );
		add_action( 'woocommerce_checkout_order_review', array( $this, 'woocommerce_checkout_order_review' ), 15 );
		add_action( 'woocommerce_checkout_after_order_review', array( $this, 'woocommerce_checkout_after_order_review' ), 95 );

		// remove the default login & coupon form because we'll be adding our own forms
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	}

	/**
	 * Modify Form Field.
	 *
	 * WooCommerce filter is used to apply widget settings to the Checkout forms address fields
	 * from the Billing and Shipping Details widget sections, e.g. label, placeholder, default.
	 *
	 * @since 1.8.0
	 *
	 * @param array $args
	 * @param string $key
	 * @param string $value
	 * @return array
	 */
	public function modify_form_field( $args, $key, $value ) {
		$reformatted_form_fields = $this->get_reformatted_form_fields();

		// Check if we need to modify the args of this form field.
		if ( isset( $reformatted_form_fields[ $key ] ) ) {
			$apply_fields = array(
				'label',
				'placeholder',
				'default',
			);

			foreach ( $apply_fields as $field ) {
				if ( ! empty( $reformatted_form_fields[ $key ][ $field ] ) ) {
					$args[ $field ] = $reformatted_form_fields[ $key ][ $field ];
				}
			}
		}

		return $args;
	}

	/**
	 * Get Reformatted Form Fields.
	 *
	 * Combines the 3 relevant repeater settings arrays into a one level deep associative array
	 * with the keys that match those that WooCommerce uses for its form fields.
	 *
	 * The result is cached so the conversion only ever happens once.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	private function get_reformatted_form_fields() {
		if ( ! isset( $this->reformatted_form_fields ) ) {
			$instance = $this->get_settings_for_display();

			// Reformat form repeater field into one usable array.
			$repeater_fields = array(
				'billing_details_form_fields',
				'shipping_details_form_fields',
				'additional_information_form_fields',
			);

			$this->reformatted_form_fields = array();

			// Apply other modifications to inputs.
			foreach ( $repeater_fields as $repeater_field ) {
				if ( isset( $instance[ $repeater_field ] ) ) {
					foreach ( $instance[ $repeater_field ] as $item ) {
						if ( ! isset( $item['field_key'] ) ) {
							continue;
						}

						$this->reformatted_form_fields[ $item['field_key'] ] = $item;
					}
				}
			}
		}

		return $this->reformatted_form_fields;
	}

	/**
	 * WooCommerce Terms and Conditions Checkbox Text.
	 *
	 * WooCommerce filter is used to apply widget settings to Checkout Terms & Conditions text and link text.
	 *
	 * @since 1.8.0
	 * @since 1.19.2 Fixed Terms and Conditions displaying.
	 *
	 * @param string $text
	 * @return string
	 */
	public function woocommerce_terms_and_conditions_checkbox_text( $text ) {
		$instance = $this->get_settings_for_display();

		if ( ! isset( $instance['payment_terms_conditions_message'] ) || ! isset( $instance['payment_terms_conditions_link_tex'] ) ) {
			return $text;
		}

		$message = $instance['payment_terms_conditions_message'];
		$link = $instance['payment_terms_conditions_link_tex'];
		$terms_page_id = wc_terms_and_conditions_page_id();

		if ( $terms_page_id ) {
			$message .= ' <a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . $link . '</a>';
		}

		update_option( 'woocommerce_checkout_terms_and_conditions_checkbox_text', $message );

		return $message;
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
	 * WooCommerce Checkout Before Customer Details
	 *
	 * Callback function for the woocommerce_checkout_before_customer_details hook that outputs elements
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_before_customer_details() {
		?>
		<div class="e-checkout__container">
			<!--open container-->
			<div class="e-checkout__column e-checkout__column-start">
				<!--open column-1-->
		<?php
		if ( $this->should_render_login() ) {
			$this->render_woocommerce_checkout_login_form();
		}
	}

	/**
	 * Should Render Login
	 *
	 * Decide if the login form should be rendered.
	 * The login form should be rendered if:
	 * 1) The WooCommerce setting is enabled
	 * 2) AND: a logged out user is viewing the page, OR the Editor is open
	 *
	 * @since 1.8.0
	 *
	 * @return boolean
	 */
	private function should_render_login() {
		return 'no' !== get_option( 'woocommerce_enable_checkout_login_reminder' ) && ( ! is_user_logged_in() || Plugin::$instance->editor->is_edit_mode() );
	}

	/**
	 * Render Woocommerce Checkout Login Form
	 *
	 * A custom function to render a login form on the Checkout widget. The default WC Login form
	 * was removed in this file's render() method with:
	 * remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	 *
	 * And then we are adding this form into the widget at the
	 * 'woocommerce_checkout_before_customer_details' hook.
	 *
	 * We are doing this in order to match the placement of the Login form to the provided design.
	 * WC places these forms ABOVE the checkout form section where as we needed to place them inside the
	 * checkout form section. So we removed the default login form and added our own form.
	 *
	 * @since 1.8.0
	 */
	private function render_woocommerce_checkout_login_form() {
		$settings = $this->get_settings_for_display();

		$button_classes = array(
			'woocommerce-button',
			'button',
			'woocommerce-form-login__submit',
			'e-woocommerce-form-login-submit',
		);

		$this->add_render_attribute(
			'button_login', array(
				'class' => $button_classes,
				'name' => 'login',
				'type' => 'submit',
			)
		);

		?>
		<div class="e-woocommerce-login-section">
			<div class="elementor-woocommerce-login-messages"></div>
			<div class="woocommerce-form-login-toggle e-checkout-secondary-title">
				<?php echo esc_html__( 'Returning customer?', 'cmsmasters-elementor' ) . ' <a href="#" class="e-show-login" tabindex="0">' . esc_html__( 'Click here to login', 'cmsmasters-elementor' ) . '</a>'; ?>
			</div>
			<div class="e-woocommerce-login-anchor" style="display:none;">
				<p class="e-woocommerce-login-nudge e-description"><?php echo esc_html__( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'cmsmasters-elementor' ); ?></p>

				<div class="e-login-wrap">
					<div class="e-login-wrap-start">
						<p class="form-row form-row-first">
							<label for="username"><?php esc_html_e( 'Email', 'cmsmasters-elementor' ); ?> <span class="required">*</span></label>
							<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
						</p>
						<p class="form-row form-row-last">
							<label for="password"><?php esc_html_e( 'Password', 'cmsmasters-elementor' ); ?> <span class="required">*</span></label>
							<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
						</p>
						<div class="clear"></div>
					</div>

					<div class="e-login-wrap-end">
						<p class="form-row">
							<label for="login" class="e-login-label">&nbsp;</label>
							<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
							<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ); ?>" />
							<button <?php $this->print_render_attribute_string( 'button_login' ); ?> value="<?php esc_attr_e( 'Login', 'cmsmasters-elementor' ); ?>"><?php esc_html_e( 'Login', 'cmsmasters-elementor' ); ?></button>
						</p>
						<div class="clear"></div>
					</div>
				</div>

				<div class="e-login-actions-wrap">
					<div class="e-login-actions-wrap-start">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span class="elementor-woocomemrce-login-rememberme"><?php esc_html_e( 'Remember me', 'cmsmasters-elementor' ); ?></span>
						</label>
					</div>

					<div class="e-login-actions-wrap-end">
						<p class="lost_password">
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'cmsmasters-elementor' ); ?></a>
						</p>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Woocommerce Checkout After Customer Details
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_after_customer_details hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_after_customer_details() {
		?>
					<!--close column-1-->
				</div>
		<?php
	}

	/**
	 * Woocommerce Checkout Before Order Review Heading 1
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_before_order_review_heading_1() {
		?>
				<div class="e-checkout__column e-checkout__column-end">
					<!--open column-2-->
						<div class="e-checkout__column-inner e-sticky-right-column">
							<!--open column-inner-->
		<?php
	}

	/**
	 * Woocommerce Checkout Before Order Review Heading 2
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_before_order_review_heading hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_before_order_review_heading_2() {
		?>
							<div class="e-checkout__order_review">
								<!--open order_review-->
		<?php
	}

	/**
	 * Woocommerce Checkout Order Review
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_order_review hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_order_review() {
		?>
									<!--close wc_order_review-->
								</div>
								<!--close order_review-->
							</div>
		<?php

		if ( $this->should_render_coupon() ) {
			$this->render_woocommerce_checkout_coupon_form();
		}

		?>
							<div class="e-checkout__order_review-2">
								<!--reopen wc_order_review-2-->
		<?php
	}

	/**
	 * Should Render Coupon
	 *
	 * Decide if the coupon form should be rendered.
	 * The coupon form should be rendered if:
	 * 1) The WooCommerce setting is enabled
	 * 2) And the Coupon Display toggle hasn't been set to 'no'
	 * 3) AND: a payment is needed, OR the Editor is open
	 *
	 * @since 1.8.0
	 *
	 * @return boolean
	 */
	private function should_render_coupon() {
		$settings = $this->get_settings_for_display();

		$coupon_display_control = true;

		if ( '' === $settings['coupon_section_display'] ) {
			$coupon_display_control = false;
		}

		return ( WC()->cart->needs_payment() || Plugin::$instance->editor->is_edit_mode() ) && wc_coupons_enabled() && $coupon_display_control;
	}

	/**
	 * Render Woocommerce Checkout Coupon Form
	 *
	 * A custom function to render a coupon form on the Checkout widget. The default WC coupon form
	 * was removed in this file's render() method with:
	 * remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	 *
	 * And then we are adding this form into the widget at the
	 * 'woocommerce_checkout_order_review' hook.
	 *
	 * We are doing this in order to match the placement of the coupon form to the provided design.
	 * WC places these forms ABOVE the checkout form section where as we needed to place them inside the
	 * checkout form section. So we removed the default coupon form and added our own form.
	 *
	 * @since 1.8.0
	 */
	private function render_woocommerce_checkout_coupon_form() {
		$settings = $this->get_settings_for_display();

		$button_classes = array(
			'woocommerce-button',
			'button',
			'e-apply-coupon',
		);

		$this->add_render_attribute(
			'button_coupon', array(
				'class' => $button_classes,
				'name' => 'apply_coupon',
				'type' => 'submit',
			)
		);

		?>
		<div class="e-coupon-box">
			<p class="e-woocommerce-coupon-nudge e-checkout-secondary-title"><?php esc_html_e( 'Have a coupon?', 'cmsmasters-elementor' ); ?> <a href="#" class="e-show-coupon-form"><?php esc_html_e( 'Click here to enter your coupon code', 'cmsmasters-elementor' ); ?></a></p>
			<div class="e-coupon-anchor" style="display:none">
				<label class="e-coupon-anchor-description"><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'cmsmasters-elementor' ); ?></label>
				<div class="form-row">
					<div class="coupon-container-grid">
						<div class="col coupon-col-1 ">
							<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'cmsmasters-elementor' ); ?>" id="coupon_code" value="" />
						</div>
						<div class="col coupon-col-2">
							<button <?php $this->print_render_attribute_string( 'button_coupon' ); ?>><?php esc_html_e( 'Apply', 'cmsmasters-elementor' ); ?></button>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Woocommerce Checkout After Order Review
	 *
	 * Output containing elements. Callback function for the woocommerce_checkout_after_order_review hook.
	 *
	 * This eliminates the need for template overrides.
	 *
	 * @since 1.8.0
	 */
	public function woocommerce_checkout_after_order_review() {
		?>
										<!--close wc_order_review-2-->
						<!--close column-inner-->
					</div>
					<!--close column-2-->
				</div>
				<!--close container-->
			</div>
		<?php
	}

	/**
	 * Remove Render Hooks
	 *
	 * Remove actions & filters after displaying our widget.
	 *
	 * @since 1.8.0
	 */
	public function remove_render_hooks() {
		remove_filter( 'woocommerce_form_field_args', array( $this, 'modify_form_field' ), 70 );
		remove_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', array( $this, 'woocommerce_terms_and_conditions_checkbox_text' ), 10 );

		remove_filter( 'gettext', array( $this, 'filter_gettext' ), 20 );

		remove_action( 'woocommerce_checkout_before_customer_details', array( $this, 'woocommerce_checkout_before_customer_details' ), 5 );
		remove_action( 'woocommerce_checkout_after_customer_details', array( $this, 'woocommerce_checkout_after_customer_details' ), 95 );
		remove_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'woocommerce_checkout_before_order_review_heading_1' ), 5 );
		remove_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'woocommerce_checkout_before_order_review_heading_2' ), 95 );
		remove_action( 'woocommerce_checkout_order_review', array( $this, 'woocommerce_checkout_order_review' ), 15 );
		remove_action( 'woocommerce_checkout_after_order_review', array( $this, 'woocommerce_checkout_after_order_review' ), 95 );
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
			'Ship to a different address?' => isset( $instance['shipping_details_title'] ) ? $instance['shipping_details_title'] : '',
			'Additional information' => isset( $instance['additional_information_title'] ) ? $instance['additional_information_title'] : '',
			'Your order' => isset( $instance['your_order_title'] ) ? $instance['your_order_title'] : '',
			'Create an account?' => isset( $instance['create_account_text'] ) ? $instance['create_account_text'] : '',
		);
	}
}
