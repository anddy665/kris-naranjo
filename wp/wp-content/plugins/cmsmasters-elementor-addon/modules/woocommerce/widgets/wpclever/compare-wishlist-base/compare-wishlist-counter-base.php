<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Wpclever\CompareWishlistBase;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Base\Base_Document;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Compare_Wishlist_Counter_Base extends Base_Widget {

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
	 * Get group widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.11.0
	 *
	 * @return string The widget name.
	 */
	public function get_group_name() {
		return 'cmsmasters-wpclever-smart-counter-base';
	}

	public function get_unique_keywords() {
		return array(
			'wishlist',
			'compare',
			'counter',
		);
	}

	public function cmsmasters_base_class() {
		return 'elementor-widget-cmsmasters-wpclever-base-counter';
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.11.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'wpclever_section_main_settings',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'label_block' => true,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'text' => __( 'Text', 'cmsmasters-elementor' ),
					'count' => __( 'Counter', 'cmsmasters-elementor' ),
				),
				'multiple' => true,
				'default' => array( 'icon', 'count' ),
			)
		);

		$this->add_control(
			'icon_block',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array(
						'title' => __( 'Row', 'cmsmasters-elementor' ),
						'description' => 'Row',
					),
					'column' => array(
						'title' => __( 'Column', 'cmsmasters-elementor' ),
						'description' => 'Column',
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'row',
				'prefix_class' => 'cmsmasters-wpclever__icon-block-',
				'render_type' => 'template',
				'condition' => array(
					'order' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
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
				),
				'default' => 'center',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-wpclever-base-counter__general' => '--cmsmasters-item-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'text', 'cmsmasters-elementor' ),
				'condition' => array( 'order' => 'text' ),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => $this->default_icon(),
				'condition' => array(
					'order' => 'icon',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpclever_icon_style',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'order' => 'icon' ),
			)
		);

		$this->add_control(
			'wpclever_items_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'default',
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'wpclever_items_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'wpclever_items_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'wpclever_tabs_button_icon_style' );

		$colors_icon = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_icon as $key => $label ) {
			$this->start_controls_tab(
				"wpclever_tab_button_icon_{$key}",
				array( 'label' => $label )
			);

			$this->add_control(
				"wpclever_button_icon_color_{$key}",
				array(
					'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wpclever_button_icon_bg_color_{$key}",
				array(
					'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bg-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wpclever_items_view!' => 'default',
					),
				)
			);

			$this->add_control(
				"wpclever_button_icon_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wpclever_items_view' => 'framed',
					),
				)
			);

			$this->add_control(
				"wpclever_button_icon_border_radius__{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
					'condition' => array(
						'wpclever_items_view!' => 'default',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'wpclever_button_icon_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'wpclever_items_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_square_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-square-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view!' => 'default',
					'wpclever_items_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_circle_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-circle-pdd: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view!' => 'default',
					'wpclever_items_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-border-w: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view' => 'framed',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpclever_text_style',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'order' => 'text' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_text_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-wpclever-base-counter__text',
			)
		);

		$this->add_control(
			"wpclever_text_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => "--cmsmasters-wpcl-text-color: {{VALUE}};",
				),
			)
		);

		$this->add_control(
			"wpclever_text_color_hover",
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => "--cmsmasters-wpcl-text-color-hover: {{VALUE}};",
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_text_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-text-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpclever_count_style',
			array(
				'label' => __( 'Counter', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'order' => 'count' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_count_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-wpclever-base-counter__count',
			)
		);

		$this->start_controls_tabs( 'wpclever_tabs_button_counter_state' );

		$colors_counter = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_counter as $key => $label ) {
			$this->start_controls_tab(
				"wpclever_tab_button_counter_{$key}",
				array( 'label' => $label )
			);

			$this->add_control(
				"wpclever_count_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-count-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wpclever_count_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-bg-count-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wpclever_count_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-bd-count-color-{$key}: {{VALUE}};",
					),
				)
			);

			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "wpclever_counter_box_shadow_{$key}",
					'selector' => "{{WRAPPER}} .elementor-widget-cmsmasters-wpclever-base-counter__link{$state} .elementor-widget-cmsmasters-wpclever-base-counter__count",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wpclever_counter_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'separator' => 'before',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-wpclever-base-counter__count',
			)
		);

		$this->add_responsive_control(
			'wpclever_counter_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wpcl-count-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_counter_padd',
			array(
				'label' => __( 'Counter Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' => '--cmsmasters-wpcl-count-pdd: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'wpclever_counter_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'absolute' => array( 'title' => __( 'Absolute', 'cmsmasters-elementor' ) ),
					'relative' => array( 'title' => __( 'Relative', 'cmsmasters-elementor' ) ),
				),
				'default' => 'absolute',
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'wpclever_button_counter_spacing_vertical',
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
					'{{WRAPPER}}' => '--cmsmasters-wpcl-count-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'wpclever_counter_position' => 'absolute' ),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_counter_spacing_horizontal',
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
					'{{WRAPPER}}' => '--cmsmasters-wpcl-count-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'wpclever_counter_position' => 'absolute' ),
			)
		);

		$this->add_responsive_control(
			'wpclever_counter_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wpcl-count-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'wpclever_counter_position' => 'relative' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.11.0
	 */
	public function render_html() {
		$settings = $this->get_settings_for_display();
		$base_class = $this->cmsmasters_base_class();
		$obj = $this->get_obj();
		$class = $this->individual_class();

		$this->add_render_attribute( 'base-counter-wrapper', 'class', array(
			$class['wrapper'],
			"{$base_class}__general",
			$base_class,
			"{$base_class}__alignment-{$settings['button_alignment']}",
			$class['trigger'],
		) );

		$link = $obj::get_url();

		$this->add_render_attribute( 'base-counter-link', 'class', array(
			$class['link'],
			"{$base_class}__link",
		) );

		$this->add_render_attribute( 'base-counter-link', 'href', array(
			$link,
		) );

		$this->add_render_attribute( 'base-counter-link', 'target', array(
			'_blank',
		) );

		echo '<div ' . $this->get_render_attribute_string( 'base-counter-wrapper' ) . '>
			<a ' . $this->get_render_attribute_string( 'base-counter-link' ) . '>';
				$this->render_inner();
			echo '</a>
		</div>';
	}

	/**
	 * Render each social item.
	 *
	 * @since 1.11.0
	 */
	public function render_inner() {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['order'] as $item ) {
			$this->render_item( $item );
		}
	}

	/**
	 * Render social item.
	 *
	 * @since 1.11.0
	 */
	public function render_item( $item ) {

		$obj = $this->get_obj();
		$settings = $this->get_settings_for_display();
		$base_class = $this->cmsmasters_base_class();

		$this->add_render_attribute( 'header-count', 'class', array(
			'count',
			"{$base_class}__count",
			"{$base_class}__count-{$settings['wpclever_counter_position']}",
		) );

		$this->add_render_attribute( 'header-text', 'class', array(
			"{$base_class}__text",
		) );

		$this->add_render_attribute( 'header-count', 'data-count', array(
			$obj::get_count(),
		) );

		$default_text = $this->default_text();
		$text = ( '' !== $settings['button_text'] ) ? esc_html( $settings['button_text'] ) : $default_text;

		$count = '<span ' . $this->get_render_attribute_string( 'header-count' ) . '>' . $obj::get_count() . '</span>';
		$text = '<span ' . $this->get_render_attribute_string( 'header-text' ) . '>' . $text . '</span>';
		$icon = $this->render_icon();

		switch ( $item ) {
			case 'icon':
				echo $icon;

				break;
			case 'text':
				echo $text;

				break;
			case 'count':
				echo $count;

				break;
		}
	}

	public function render_icon() {
		$base_class = $this->cmsmasters_base_class();
		$settings = $this->get_settings_for_display();

		$icon_view = ( isset( $settings['wpclever_items_view'] ) ? $settings['wpclever_items_view'] : '' );
		$icon_shape = ( isset( $settings['wpclever_items_shape'] ) ? $settings['wpclever_items_shape'] : '' );

		$this->add_render_attribute( 'icon-normal', 'class', array(
			"{$base_class}__icon-wrapper",
			"{$base_class}__icon-{$icon_view}",
			"{$base_class}__icon-{$icon_shape}",
		) );

		ob_start();

		if ( isset( $settings["button_icon"] ) && ! empty( $settings["button_icon"]['value'] ) ) {
			echo "<span {$this->get_render_attribute_string( 'icon-normal' )}>";

				$button_icon_att = array( 'aria-hidden' => 'true' );

				if ( ! in_array( 'text', $settings['order'] ) ) {
					$button_icon_att = array_merge(
						$button_icon_att,
						array( 'aria-label' => 'Counter' ),
					);
				}

				Icons_Manager::render_icon( $settings["button_icon"], $button_icon_att );

			echo '</span>';
		}

		return ob_get_clean();
	}

	abstract public function individual_class();
	abstract public function get_obj();
	abstract public function default_text();
	abstract public function default_icon();

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.11.0
	 */
	protected function render() {
		if ( ! class_exists( 'WPCleverWoosw' ) || ! class_exists( 'WPCleverWoosc' ) ) {
			return;
		}

		$this->render_html();
	}
}
