<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Additional_Information extends Base_Widget {

	use Woo_Singular_Widget;

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
		return __( 'Additional Info', 'cmsmasters-elementor' );
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
		return 'eicon-product-info';
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
			'attributes',
			'additional',
			'information',
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section( 'section_additional_info_style', array(
			'label' => __( 'Heading', 'cmsmasters-elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );

		$product_base = '.woocommerce div.product{{WRAPPER}}';
		$template_product_base = '.woocommerce div.product {{WRAPPER}}';

		$this->add_control(
			'show_heading',
			array(
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'render_type' => 'ui',
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-show-heading-',
			)
		);

		$this->add_responsive_control(
			'heading_align',
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
				'selectors' => array(
					$template_product_base . ' h2,' .
					$product_base . ' h2' => 'text-align: {{VALUE}}',
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
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $template_product_base . ' h2,' .
				$product_base . ' h2',
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$template_product_base . ' h2,' .
					$product_base . ' h2' => 'color: {{VALUE}}',
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
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$template_product_base . ' h2,' .
					$product_base . ' h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'show_heading!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_additional_info_attributes', array(
			'label' => __( 'Table', 'cmsmasters-elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control(
			'title_table_border',
			array(
				'label' => __( 'Table Border', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'table_border',
				'selector' => $template_product_base . ' .shop_attributes td,' .
				$product_base . ' .shop_attributes td,' .
				$template_product_base . ' .shop_attributes th,' .
				$product_base . ' .shop_attributes th,' .
				$template_product_base . '.cmsmasters-table-style-line-hor tr + tr > *,' .
				$product_base . '.cmsmasters-table-style-line-hor tr + tr > *',
				'fields_options' => array(
					'width' => array(
						'default' => array(
							'top' => 1,
							'bottom' => 1,
							'left' => 1,
							'right' => 1,
						),
					),
				),
			)
		);

		$this->add_control(
			'table_style_choose',
			array(
				'label' => __( 'Display Borders', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'all' => array(
						'title' => __( 'All', 'cmsmasters-elementor' ),
					),
					'inn-hor' => array(
						'title' => __( 'Inner Hor', 'cmsmasters-elementor' ),
						'description' => __( 'Borders in parent, horizontal borders in table cells', 'cmsmasters-elementor' ),
					),
					'only-hor' => array(
						'title' => __( 'Only Hor', 'cmsmasters-elementor' ),
						'description' => __( 'Only horizontal borders in table cells', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inn-hor',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-table-style-',
				'condition' => array( 'table_border_border!' => '' ),
			)
		);

		$this->add_control(
			'table_styles',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lines_background_odd',
			array(
				'label' => __( 'Odd Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$template_product_base . ' .shop_attributes tr:nth-child(odd) th,' .
					$product_base . ' .shop_attributes tr:nth-child(odd) th,' .
					$template_product_base . ' .shop_attributes tr:nth-child(odd) td,' .
					$product_base . ' .shop_attributes tr:nth-child(odd) td' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'lines_background_even',
			array(
				'label' => __( 'Even Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$template_product_base . ' .shop_attributes tr:nth-child(even) th,' .
					$product_base . ' .shop_attributes tr:nth-child(even) th,' .
					$template_product_base . ' .shop_attributes tr:nth-child(even) td,' .
					$product_base . ' .shop_attributes tr:nth-child(even) td' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'table_box_shadow',
				'selector' => $template_product_base . ' .shop_attributes,' .
				$product_base . ' .shop_attributes',
			)
		);

		$this->add_responsive_control(
			'content_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					$template_product_base . ' .shop_attributes th,' .
					$product_base . ' .shop_attributes th,' .
					$template_product_base . ' .shop_attributes td,' .
					$product_base . ' .shop_attributes td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_cell_style',
			array(
				'label' => __( 'Table Cell', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'table_cell_tabs' );

		$this->start_controls_tab(
			'title_tab',
			array( 'label' => __( 'Name', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'content_title_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 300,
						'step' => 5,
					),
				),
				'selectors' => array(
					$template_product_base . ' .shop_attributes th,' .
					$product_base . ' .shop_attributes th' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_title_hor_alignment',
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
				'default' => 'left',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					$template_product_base . ' .shop_attributes th,' .
					$product_base . ' .shop_attributes th' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $template_product_base . ' .shop_attributes th,' .
				$product_base . ' .shop_attributes th',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$template_product_base . ' .shop_attributes th,' .
					$product_base . ' .shop_attributes th' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'attributes_tab',
			array( 'label' => __( 'Value', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'attributes_hor_alignment',
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
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					$template_product_base . ' .shop_attributes td,' .
					$product_base . ' .shop_attributes td' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'attributes_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $template_product_base . ' .shop_attributes td,' .
				$product_base . ' .shop_attributes td',
			)
		);

		$this->add_control(
			'attributes_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$template_product_base . ' .shop_attributes td,' .
					$product_base . ' .shop_attributes td' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		wc_get_template( 'single-product/tabs/additional-information.php' );
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
