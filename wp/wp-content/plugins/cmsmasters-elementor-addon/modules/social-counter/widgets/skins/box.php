<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Skins;

use CmsmastersElementor\Controls\Groups\Group_Control_Flex_Align;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon social-counter box skin.
 *
 * @since 1.0.0
 */
class Box extends Base {

	/**
	 * @since 1.0.0
	 */
	public function get_id() {
		return 'box';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Box', 'cmsmasters-elementor' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_controls_injection();
		$this->register_controls_box_style();
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_social_item_attrs() {
		$social_item_attrs = parent::get_social_item_attrs();
		$social_item = $this->parent->get_social_item();
		$brand_color_to = $this->parent->get_settings_for_display( "box_brand_color_to_{$social_item::get_name()}" );

		if ( $brand_color_to ) {
			$social_item_attrs['class'][] = "social-item--brand-color-{$brand_color_to}";
		}

		return $social_item_attrs;
	}


	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	protected function register_controls_injection() {
		$states = array(
			'normal',
			'hover',
		);

		foreach ( array_keys( $this->parent->get_social_classes() ) as $social_name ) {
			$this->parent->start_injection(
				array(
					'of' => "title_{$social_name}",
				)
			);

			$this->add_control(
				"color_type_{$social_name}",
				array(
					'label' => __( 'Color Type', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'separator' => 'before',
					'default' => 'brand',
					'options' => array(
						'brand' => array( 'title' => __( 'Brand', 'cmsmasters-elementor' ) ),
						'custom' => array( 'title' => __( 'custom', 'cmsmasters-elementor' ) ),
					),
				)
			);

			$this->add_control(
				"brand_color_to_{$social_name}",
				array(
					'label' => __( 'Brand Color To', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'default' => 'background',
					'options' => array(
						'background' => array( 'title' => __( 'Background', 'cmsmasters-elementor' ) ),
						'icon' => array( 'title' => __( 'Icon', 'cmsmasters-elementor' ) ),
					),
					'condition' => array(
						$this->get_control_id( "color_type_{$social_name}" ) => 'brand',
					),
				)
			);

			$this->parent->end_injection();

			foreach ( $states as $state ) {
				$selector_item = "{{WRAPPER}} .social-item[data-name=\"{$social_name}\"] .social-link";

				if ( 'hover' === $state ) {
					$selector_item .= ':hover';
				}

				$this->parent->start_injection(
					array( 'of' => "color_numbers_{$social_name}_{$state}" )
				);

				$this->add_control(
					"background_{$social_name}_{$state}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'render_type' => 'ui',
						'selectors' => array(
							$selector_item => 'background-color: {{VALUE}};',
						),
						'conditions' => array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => "box_color_type_{$social_name}",
									'operator' => '===',
									'value' => 'custom',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => "box_color_type_{$social_name}",
											'operator' => '===',
											'value' => 'brand',
										),
										array(
											'name' => "box_brand_color_to_{$social_name}",
											'operator' => '===',
											'value' => 'background',
										),
									),
								),
							),
						),
					)
				);

				$this->add_control(
					"color_icon_{$social_name}_{$state}",
					array(
						'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'render_type' => 'ui',
						'selectors' => array(
							"{$selector_item} .social-icon" => 'color: {{VALUE}}; fill: {{VALUE}};',
						),
						'conditions' => array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => $this->get_control_id( "color_type_{$social_name}" ),
									'operator' => '===',
									'value' => 'custom',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => $this->get_control_id( "color_type_{$social_name}" ),
											'operator' => '===',
											'value' => 'brand',
										),
										array(
											'name' => $this->get_control_id( "brand_color_to_{$social_name}" ),
											'operator' => '====',
											'value' => 'icon',
										),
									),
								),
							),
						),
					)
				);

				$this->parent->end_injection();
			}
		}
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_box_style() {
		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => __( 'Box', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'icon',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'numbers',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'title',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Flex_Align::get_type(),
			array(
				'name' => 'flex_align_inner',
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'position' => array(
						'prefix_class' => 'cmsmasters-box-position-',
					),
					'ai_vertical' => array(
						'default' => 'center',
					),
					'ai_horizontal' => array(
						'default' => 'center',
					),
					'jc_horizontal' => array(
						'default' => 'flex-start',
					),
					'jc_vertical' => array(
						'default' => 'center',
					),
				),
				'selector' => '{{WRAPPER}} .social-link-inner',
			)
		);

		$this->start_controls_tabs( 'social_style_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $state => $label ) {
			$selector = '{{WRAPPER}} .social-link';

			if ( 'hover' === $state ) {
				$selector .= ':hover';
			}

			$this->start_controls_tab(
				"social_style_tab_$state",
				array( 'label' => $label )
			);

			$this->add_control(
				"background_color_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"social_item_bd_color_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}}',
					),
					'condition' => array(
						'box_social_item_border_border!' => '',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "social_item_box_shadow_{$state}",
					'selector' => $selector,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'box_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'isLinked' => false,
				'selectors' => array(
					'{{WRAPPER}} .social-link-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'min_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'default' => array(
					'size' => 50,
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter--box .social-link-inner' => 'min-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'columns' => '' ),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'default' => array( 'size' => 50 ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter--box' => '--social-min-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'social_item_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .social-link' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'social_item_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .social-link',
			)
		);

		$this->end_controls_section();
	}
}
