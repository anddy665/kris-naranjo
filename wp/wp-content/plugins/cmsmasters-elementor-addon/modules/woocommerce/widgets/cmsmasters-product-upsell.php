<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Settings\Kit_Globals;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class CMSMasters_Product_Upsell extends Base_Widget {

	public function get_name() {
		return 'cmsmasters-woocommerce-product-upsell';
	}

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-woocommerce';
	}

	public function get_title() {
		return __( 'Upsells', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'eicon-product-upsell';
	}

	public function get_keywords() {
		return array( 'woocommerce', 'shop', 'store', 'upsell', 'product' );
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
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_upsell_content',
			array(
				'label' => __( 'Upsells', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-products-columns%s-',
				'default' => 4,
				'min' => 1,
				'max' => 12,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date' => __( 'Date', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'price' => __( 'Price', 'cmsmasters-elementor' ),
					'popularity' => __( 'Popularity', 'cmsmasters-elementor' ),
					'rating' => __( 'Rating', 'cmsmasters-elementor' ),
					'rand' => __( 'Random', 'cmsmasters-elementor' ),
					'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => array(
					'asc' => __( 'ASC', 'cmsmasters-elementor' ),
					'desc' => __( 'DESC', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->end_controls_section();

		parent::register_controls();

		$this->start_injection( array(
			'at' => 'before',
			'of' => 'section_design_box',
		) );

		$this->start_controls_section(
			'section_heading_style',
			array(
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'show_heading',
			array(
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'show-heading-',
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
				'selectors' => array(
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'heading_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}}.elementor-wc-products .products > h2',
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'heading_text_align',
			array(
				'label' => __( 'Text Align', 'cmsmasters-elementor' ),
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
				'selectors' => array(
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
				),
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$limit = '-1';
		$columns = 4;
		$orderby = 'rand';
		$order = 'desc';

		if ( ! empty( $settings['columns'] ) ) {
			$columns = $settings['columns'];
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$orderby = $settings['orderby'];
		}

		if ( ! empty( $settings['order'] ) ) {
			$order = $settings['order'];
		}

		woocommerce_upsell_display( $limit, $columns, $orderby, $order );
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
