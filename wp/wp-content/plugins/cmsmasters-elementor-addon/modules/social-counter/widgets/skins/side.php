<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

/**
 * Addon social-counter side skin.
 *
 * @since 1.0.0
 */
class Side extends Base {

	/**
	 * Index for each social item.
	 *
	 * @var int
	 */
	private $social_index = 0;

	/**
	 * @since 1.0.0
	 */
	public function get_id() {
		return 'side';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Side', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_default_order() {
		return array( 'icon', 'title', 'numbers' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_controls_number_style();
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_number_style() {
		$this->start_controls_section(
			'section_style_side',
			array(
				'label' => __( 'Side', 'cmsmasters-elementor' ),
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

		$this->add_responsive_control(
			'side_alignment',
			array(
				'label' => __( 'Side Alignment', 'cmsmasters-elementor' ),
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
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .social-split' => 'text-align: {{VALUE}};',
				),
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
						'side_social_item_border_border!' => '',
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
			'side_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'side_padding',
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
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter--side .social-link-inner' => 'min-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .cmsmasters-social-counter--side' => '--social-min-height: {{SIZE}}{{UNIT}}',
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

	/**
	 * @since 1.0.0
	 */
	protected function render_social_inner() {
		$this->social_index = 0;

		parent::render_social_inner();
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_social_item( $item ) {
		$length = count( $this->get_order() );

		if ( 1 === $this->social_index ) {
			echo '<div class="social-split">';
		}

		parent::render_social_item( $item );

		if ( ( $this->social_index + 1 ) >= $length ) {
			echo '</div>';
		}

		++$this->social_index;
	}
}
