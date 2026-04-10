<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Wbw_Product_Filter extends Base_Widget {
	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array(
			Base_Document::WOO_WIDGETS_CATEGORY,
		);
	}

	public function get_name() {
		return 'woofilters';
	}

	public function get_script_depends() {
		return array(
			'commonWpf',
			'coreWpf',
			'jquery-ui-slider',
			'tooltipster',
			'frontend.filters',
			'frontend.multiselect',
			'frontend.filters.pro',
			'jquery-ui-autocomplete',
			'ion.slider',
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
			'frontend.filters',
			'tooltipster',
			'frontend.filters.accessibility',
			'frontend.multiselect',
			'frontend.filters.pro',
			'jquery-ui',
			'jquery-ui.structure',
			'jquery-ui.theme',
			'jquery-slider',
			'custom.filters',
			'custom.filters.pro',
			'jquery-ui-autocomplete',
			'ion.slider',
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
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'WBW Product Filters', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-product-filter';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'filter',
			'wbw',
		);
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.11.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		if ( $this->is_empty_filters() ) {
			$this->start_controls_section(
				'section_error',
				array(
					'label' => __( 'Warning', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'warning_section',
				array(
					'raw' => '<strong>Product Filter</strong>' . __( ' You do not have any forms created. ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( '?page=wpf-filters&tab=woofilters' ) ) . '" target="_blank">' . __( 'Go to the form creation page', 'cmsmasters-elementor' ) . '</a>',
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);

			$this->end_controls_section();

			return;
		}

		$frame_wpf = new \FrameWpf();
		$is_pro = $frame_wpf->_()->isPro();

		$this->start_controls_section(
			'section_wbw_filter_general',
			array(
				'label' => __( 'Filterls', 'cmsmasters-elementor' ),
			)
		);

		list( $filters_opts ) = $this->filters();

		$this->add_control(
			'filter_id',
			array(
				'label' => __( 'Select Filter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => $filters_opts,
				'default' => 0,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wbw_filter_general_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wbw_type_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'theme' => __( 'Theme', 'cmsmasters-elementor' ),
					'plugin' => __( 'Plugin', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'description' => __( 'Select Theme Styles to apply the styles from the theme and open the additional settings.
				Select Plugin Styles to apply the styles set in the Product Filter by WBW plugin settings.', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( '?page=wpf-filters&tab=woofilters' ) ) . '" target="_blank">' . __( 'Settings page', 'cmsmasters-elementor' ) . '</a>',
				'default' => 'theme',
				'toggle' => false,
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_label',
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_general_v_gap',
			array(
				'label' => __( 'Box Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-box-general-v-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_general_item_h_gap',
			array(
				'label' => __( 'Horizontal Item Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-general-item-h-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_general_item_v_gap',
			array(
				'label' => __( 'Vertical Item Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'description' => __( 'This setting will be applied if the Vertical Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-general-item-v-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->start_controls_tabs( 'wbw_general_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'selected' => __( 'Selected', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$this->start_controls_tab(
				"wbw_general_form_tab_{$key}",
				array(
					'label' => $label,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_control(
				"wbw_general_label_color_{$key}",
				array(
					'label' => __( 'Label Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--wbw-general-label-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_control(
				"wbw_general_radio_bg_color_{$key}",
				array(
					'label' => __( 'Radio & Checkbox Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--wbw-general-radio-bg-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_control(
				"wbw_general_radio_bd_color_{$key}",
				array(
					'label' => __( 'Radio & Checkbox Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--wbw-general-radio-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'wbw_radio_label_gap',
			array(
				'label' => __( 'Label Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-radio-label-gap: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array(
					'px',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_radio_size',
			array(
				'label' => __( 'Radio & Checkbox Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-radio-size: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array(
					'px',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_control(
			'wbw_radio_bd_size',
			array(
				'label' => __( 'Radio & Checkbox Border Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-radio-bd-size: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array(
					'px',
				),
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->end_controls_section();

		if ( $is_pro ) {
			$this->start_controls_section(
				'wbw_clear_block_style',
				array(
					'label' => __( 'Clear Block', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_clear_block',
				)
			);

			$this->add_responsive_control(
				'wbw_clear_block_h_gap',
				array(
					'label' => __( 'Item Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-clear-block-h-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'wbw_clear_block_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$state = '';

				if ( 'hover' === $key ) {
					$state = ':hover';
				}

				$state = ( 'hover' === $key ) ? ':hover' : '';
				$selector = "{{WRAPPER}}";

				$this->start_controls_tab(
					"wbw_clear_block_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wbw_clear_block_text_color_{$key}",
					array(
						'label' => __( 'Item Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-clear-block-text-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_clear_block_clear_color_{$key}",
					array(
						'label' => __( 'Clear Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-clear-block-clear-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_clear_block_bg_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-clear-block-bg-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_clear_block_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-clear-block-bd-color-{$key}: {{VALUE}};",
						),
						'condition' => array(
							'wbw_clear_block_border_border!' => array(
								'none',
							),
						),
					)
				);

				$this->add_responsive_control(
					"wbw_clear_block_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => "--wbw-clear-block-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
						),
					)
				);

				$this->add_group_control(
					CmsmastersControls::VARS_BOX_SHADOW_GROUP,
					array(
						'name' => "wbw_clear_block_bxs_{$key}",
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_group_control(
				CmsmastersControls::VARS_BORDER_GROUP,
				array(
					'name' => 'wbw_clear_block_bd',
					'exclude' => array( 'color' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wbw_clear_block_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'separator' => 'before',
					'selectors' => array(
						$selector => "--wbw-clear-block-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_wbw_filter_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_title',
			)
		);

		$this->add_control(
			'wbw_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-title-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-title-color-hover: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wbw_filter_description_style',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_description',
			)
		);

		$this->add_control(
			'wbw_description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-description-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wbw_description_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-description-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		if ( $is_pro ) {
			$this->start_controls_section(
				'section_wbw_filter_search_style',
				array(
					'label' => __( 'Search', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_search',
				)
			);

			$this->add_responsive_control(
				'wbw_search_gap',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-search-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'wbw_search_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'focus' => __( 'Focus', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$state = ( 'focus' === $key ) ? ':focus' : '';
				$selector = "{{WRAPPER}}";

				$this->start_controls_tab(
					"wbw_search_form_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wbw_search_text_color_{$key}",
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-search-text-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_search_background_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-search-bg-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_search_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-search-bd-color-{$key}: {{VALUE}};",
						),
						'condition' => array(
							'wbw_search_border_field_border!' => array(
								'none',
							),
						),
					)
				);

				$this->add_responsive_control(
					"wbw_search_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => "--wbw-search-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
						),
					)
				);

				$this->add_group_control(
					CmsmastersControls::VARS_BOX_SHADOW_GROUP,
					array(
						'name' => "wbw_search_bxs_{$key}",
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_group_control(
				CmsmastersControls::VARS_BORDER_GROUP,
				array(
					'name' => 'wbw_search_bd',
					'exclude' => array( 'color' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wbw_search_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--wbw-search-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
					'separator' => 'before',
				)
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_wbw_filter_dropdawn_style',
			array(
				'label' => __( 'Dropdown Skin', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_dropdawn',
			)
		);

		$this->start_controls_tabs( 'wbw_dropdawn_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'focus' === $key ) ? ':focus' : '';
			$selector = "{{WRAPPER}}";

			$this->start_controls_tab(
				"wbw_dropdawn_form_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"wbw_dropdawn_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-dropdawn-text-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wbw_dropdawn_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-dropdawn-bg-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wbw_dropdawn_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-dropdawn-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_dropdawn_border_field_border!' => array(
							'none',
						),
					),
				)
			);

			$this->add_responsive_control(
				"wbw_dropdawn_border_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$selector => "--wbw-dropdawn-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "wbw_dropdawn_bxs_{$key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'wbw_dropdawn_bd',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'wbw_dropdawn_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => "--wbw-dropdawn-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		if ( $is_pro ) {
			$this->start_controls_section(
				'section_wbw_filter_text_style',
				array(
					'label' => __( 'Text Skin', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_text',
				)
			);

			$this->add_control(
				'wbw_text_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-text-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'wbw_text_color_hover',
				array(
					'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-text-color-hover: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'wbw_text_color_selected',
				array(
					'label' => __( 'Selected Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-text-color-selected: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_text_h_gap',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-text-h-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_text_v_gap',
				array(
					'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Vertical Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-text-v-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		if ( $is_pro ) {
			$this->start_controls_section(
				'section_wbw_filter_swich_style',
				array(
					'label' => __( 'Switch Skin', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_swich',
				)
			);

			$this->start_controls_tabs( 'wbw_swich_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
				'selected' => __( 'Selected', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$this->start_controls_tab(
					"wbw_swich_form_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wbw_swich_label_color_{$key}",
					array(
						'label' => __( 'Label Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => "--wbw-swich-label-color-{$key}: {{VALUE}};",
						),
					)
				);

				if ( 'normal' === $key || 'selected' === $key ) {
					$this->add_control(
						"wbw_swich_color_{$key}",
						array(
							'label' => __( 'Swich Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}}' => "--wbw-swich-color-{$key}: {{VALUE}};",
							),
						)
					);

					$this->add_control(
						"wbw_swich_bg_color_{$key}",
						array(
							'label' => __( 'Swich Background Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}}' => "--wbw-swich-bg-color-{$key}: {{VALUE}};",
							),
						)
					);
				}

				$this->end_controls_tab();

			}

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'wbw_swich_h_gap',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-swich-h-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_swich_v_gap',
				array(
					'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Vertical Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-swich-v-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		if ( $is_pro ) {
			$this->start_controls_section(
				'section_wbw_filter_colors_style',
				array(
					'label' => __( 'Colors Skin', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_color',
				)
			);

			$this->add_responsive_control(
				'wbw_colors_size',
				array(
					'label' => __( 'Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-colors-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_colors_icon_size',
				array(
					'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-colors-icon-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'wbw_colors_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$this->start_controls_tab(
					"wbw_colors_form_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wbw_colors_label_color_{$key}",
					array(
						'label' => __( 'Label Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => "--wbw-colors-label-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_icon_color_{$key}",
					array(
						'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => "--wbw-icon-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_colors_bd_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => "--wbw-colors-bd-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->end_controls_tab();

			}

			$this->end_controls_tabs();

			$this->add_group_control(
				CmsmastersControls::VARS_BORDER_GROUP,
				array(
					'name' => 'wbw_color_bd',
					'exclude' => array( 'color' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wbw_colors_bdr',
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-colors-bdr: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_colors_h_gap',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-colors-h-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_colors_v_gap',
				array(
					'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Vertical Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-colors-v-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		if ( $is_pro ) {
			$this->start_controls_section(
				'wbw_button_skin_style',
				array(
					'label' => __( 'Button Skin', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'wbw_button_skin',
				)
			);

			$this->start_controls_tabs( 'wbw_button_skin_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
				'selected' => __( 'Selected', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$state = '';

				if ( 'hover' === $key ) {
					$state = ':hover';
				}

				if ( 'selected' === $key ) {
					$state = '.wpfTermChecked';
				}

				$state = ( 'hover' === $key ) ? ':hover' : '';
				$selector = "{{WRAPPER}}";

				$this->start_controls_tab(
					"wbw_button_skin_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wbw_button_skin_text_color_{$key}",
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-button-skin-text-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_button_skin_bg_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-button-skin-bg-color-{$key}: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wbw_button_skin_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--wbw-button-skin-bd-color-{$key}: {{VALUE}};",
						),
						'condition' => array(
							'wbw_button_skin_border_border!' => array(
								'none',
							),
						),
					)
				);

				$this->add_responsive_control(
					"wbw_button_skin_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => "--wbw-button-skin-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
						),
					)
				);

				$this->add_group_control(
					CmsmastersControls::VARS_BOX_SHADOW_GROUP,
					array(
						'name' => "wbw_button_skin_bxs_{$key}",
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_group_control(
				CmsmastersControls::VARS_BORDER_GROUP,
				array(
					'name' => 'wbw_button_skin_bd',
					'exclude' => array( 'color' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wbw_button_skin_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'separator' => 'before',
					'selectors' => array(
						$selector => "--wbw-button-skin-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_responsive_control(
				'wbw_button_skin_h_gap',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Horizontal Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-button-skin-h-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_button_skin_v_gap',
				array(
					'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'description' => __( 'This setting will be applied if the Vertical Layout is selected for the filter settings.', 'cmsmasters-elementor' ),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-button-skin-v-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		if ( $is_pro ) {
			$this->start_controls_section(
				'section_wbw_filter_rating_style',
				array(
					'label' => __( 'Rating Skin', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'wbw_type_style!' => 'plugin',
					),
				)
			);

			$this->add_responsive_control(
				'wbw_rating_size',
				array(
					'label' => __( 'Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-rating-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'wbw_rating_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-rating-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'wbw_noactive_rating_color',
				array(
					'label' => __( 'Unselected Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-noactive-rating-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'wbw_rating_bd_color',
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-rating-bd-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'wbw_rating_border_size',
				array(
					'label' => __( 'Border Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'size_units' => array(
						'px',
					),
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-rating-border-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'wbw_rating_gap',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--wbw-rating-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_wbw_filter_slider_style',
			array(
				'label' => __( 'Slider Skin', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_control(
			'wbw_slider_title_line',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Line', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'wbw_slide_line_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-line-text-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slide_line_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-line-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slider_title_bar',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Bar', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'wbw_slide_bar_bg_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-bar-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slider_title_handle',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Handle', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'wbw_slide_handle_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-handle-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slide_handle_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-handle-bd-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slider_title_fromto',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'From & To', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'wbw_slide_fromto_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-fromto-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slide_fromto_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-fromto-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slider_title_minmax',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Min & Max', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'wbw_slide_minmax_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-minmax-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wbw_slide_minmax_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-slide-minmax-bg-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wbw_filter_sliderinput_style',
			array(
				'label' => __( 'Slider Skins Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_sliderinput',
			)
		);

		$this->add_responsive_control(
			'wbw_sliderinput_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wbw-sliderinput-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'wbw_sliderinput_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'focus' === $key ) ? ':focus' : '';
			$selector = "{{WRAPPER}}";

			$this->start_controls_tab(
				"wbw_sliderinput_form_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"wbw_sliderinput_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-sliderinput-text-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wbw_sliderinput_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-sliderinput-bg-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wbw_sliderinput_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-sliderinput-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_sliderinput_border_field_border!' => array(
							'none',
						),
					),
				)
			);

			$this->add_responsive_control(
				"wbw_sliderinput_border_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$selector => "--wbw-sliderinput-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "wbw_sliderinput_bxs_{$key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'wbw_sliderinput_bd',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'wbw_sliderinput_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => "--wbw-sliderinput-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wbw_section_button_style',
			array(
				'label' => __( 'Buttons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wbw_type_style!' => 'plugin',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'wbw_button',
			)
		);

		$this->start_controls_tabs( 'tabs_wbw_button_style' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}}";

			$this->start_controls_tab(
				"wbw_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"wbw_button_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => array(
						$selector  => "--wbw-button-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_background",
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
				"wbw_button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"#cmsmasters_body {{WRAPPER}} .cmsmasters-wbw-product-filter.cmsmasters-theme-style button{$state}{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"wbw_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_color_stop",
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
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_color_b_stop",
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
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_gradient_type",
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
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_gradient_angle",
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
						"#cmsmasters_body {{WRAPPER}} .cmsmasters-wbw-product-filter.cmsmasters-theme-style button{$state}{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{wbw_button_bg_{$key}_color_stop.SIZE}}{{wbw_button_bg_{$key}_color_stop.UNIT}}, {{wbw_button_bg_{$key}_color_b.VALUE}} {{wbw_button_bg_{$key}_color_b_stop.SIZE}}{{wbw_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
						"wbw_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_bg_{$key}_gradient_position",
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
						"#cmsmasters_body {{WRAPPER}} .cmsmasters-wbw-product-filter.cmsmasters-theme-style button{$state}{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{wbw_button_bg_{$key}_color_stop.SIZE}}{{wbw_button_bg_{$key}_color_stop.UNIT}}, {{wbw_button_bg_{$key}_color_b.VALUE}} {{wbw_button_bg_{$key}_color_b_stop.SIZE}}{{wbw_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"wbw_button_bg_{$key}_background" => array( 'gradient' ),
						"wbw_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wbw_button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--wbw-button-border-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wbw_button_border_border!' => array(
							'',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'wbw_button_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => '--wbw-button-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"wbw_button_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => '--wbw-border-radius-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "wbw_button_shadow_text_{$key}",
					'selector' => "#cmsmasters_body {{WRAPPER}} .cmsmasters-wbw-product-filter.cmsmasters-theme-style button{$state}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "wbw_button_bxs_{$key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'wbw_button_bd',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'wbw_button_text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator' => 'before',
				'selectors' => array(
					$selector => '--wbw-button-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function filters() {
		$frame_wpf = new \FrameWpf();
		$filters = $frame_wpf->_()->getModule( 'woofilters' )->getModel()->getFromTbl();
		$filters_opts = array();
		$filters_opts[0] = 'Select';
		$filters_settings = array();

		foreach ( $filters as $filter ) {
			$filters_opts[ $filter['id'] ] = $filter['title'];
			$filters_settings[ $filter['id'] ] = unserialize( $filter['setting_data'] );
		}

		return array( $filters_opts, $filters_settings );
	}

	public function is_empty_filters() {
		list( $filtres ) = $this->filters();
		array_shift( $filtres );

		return empty( $filtres );
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.11.0
	 */
	protected function render() {
		if ( ! class_exists( 'FrameWpf' ) ) {
			return;
		}

		if ( $this->is_empty_filters() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$shortcode = $this->get_settings_for_display( 'filter_id' );

		$theme_class = '';

		if ( 'theme' === $settings['wbw_type_style'] ) {
			$theme_class = 'cmsmasters-theme-style';
		}

		$this->add_render_attribute( 'wbw_pf', array(
			'class' => array(
				'cmsmasters-wbw-product-filter',
				$theme_class,
			),
		) );

		echo "<div {$this->get_render_attribute_string( 'wbw_pf' )}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $shortcode ? do_shortcode( '[wpf-filters id="' . $shortcode . '"]' ) : '';
		echo "</div>";
	}

	protected function content_template() {}
}
