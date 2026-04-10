<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets\Base;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Traits\Extendable_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Short Text widget.
 *
 * Base short text widget class.
 *
 * @since 1.0.0
 */
abstract class Short_Text extends Base_Widget {

	use Extendable_Widget;

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-short-text';
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
		return array( 'text' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the users change and
	 * customize widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->register_content_tab_controls();

		$this->register_style_tab_controls();
	}

	/**
	 * Register widget content tab controls.
	 *
	 * Adds content control tab fields to allow the users change
	 * and customize widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fix for line-clamp css property.
	 */
	protected function register_content_tab_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->register_widget_content_controls();

		$this->add_responsive_control(
			'align',
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
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'prefix_class' => 'cmsmasters-align-',
				'selectors' => array(
					'{{WRAPPER}} .entry-content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'drop_cap',
			array(
				'label' => __( 'Drop Cap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-drop-cap_',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'drop_cap_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-drop-cap-view-',
				'condition' => array( 'drop_cap' => 'yes' ),
			)
		);

		$this->add_control(
			'line_clamp',
			array(
				'label' => __( 'Truncate Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-line-clamp-',
			)
		);

		$this->add_control(
			'line_clamp_count',
			array(
				'label' => __( 'Number of Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3,
				'min' => 1,
				'max' => 10,
				'selectors' => array(
					'{{WRAPPER}} .entry-content' => '-webkit-line-clamp: {{SIZE}}; height: auto;',
				),
				'condition' => array( 'line_clamp' => 'yes' ),
			)
		);

		$columns_range = range( 1, 10 );
		$columns = array_combine( $columns_range, $columns_range );
		$columns[''] = __( 'Default', 'cmsmasters-elementor' );

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'separator' => 'before',
				'options' => $columns,
				'selectors' => array(
					'{{WRAPPER}} .entry-content' => 'columns: {{VALUE}};',
				),
				'condition' => array( 'line_clamp!' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'vw' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
					'%' => array(
						'max' => 10,
						'step' => 0.1,
					),
					'vw' => array(
						'max' => 10,
						'step' => 0.1,
					),
					'em' => array(
						'max' => 10,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .entry-content' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'line_clamp!' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget content controls.
	 *
	 * Adds widget content control fields.
	 *
	 * @since 1.0.0
	 */
	abstract protected function register_widget_content_controls();

	/**
	 * Register widget style tab controls.
	 *
	 * Adds style tab control fields to allow the users change
	 * and customize widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_style_tab_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .entry-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .entry-content',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .entry-content',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .entry-content',
				'condition' => array( '_background_color!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_drop_cap',
			array(
				'label' => __( 'Drop Cap', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'drop_cap' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'drop_cap_typography',
				'exclude' => array( 'letter_spacing' ),
				'selector' => '{{WRAPPER}} .cmsmasters-drop-cap',
			)
		);

		$this->add_control(
			'drop_cap_primary_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-drop-cap-view-stacked .cmsmasters-drop-cap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-drop-cap-view-default .cmsmasters-drop-cap, {{WRAPPER}}.cmsmasters-drop-cap-view-framed .cmsmasters-drop-cap' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'drop_cap_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-drop-cap-view-stacked .cmsmasters-drop-cap' => 'color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-drop-cap-view-framed .cmsmasters-drop-cap' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_control(
			'drop_cap_size',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 30 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-drop-cap' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_control(
			'drop_cap_space',
			array(
				'label' => __( 'Space', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-drop-cap' => sprintf(
						'margin-%s: {{SIZE}}{{UNIT}};',
						is_rtl() ? 'left' : 'right'
					),
				),
			)
		);

		$this->add_control(
			'drop_cap_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 20 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-drop-cap' => 'border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view' => 'framed' ),
			)
		);

		$this->add_control(
			'drop_cap_border_radius', array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'after',
				'size_units' => array( '%', 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-drop-cap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'drop_cap_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-drop-cap__letter',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'drop_cap_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-drop-cap',
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	abstract protected function get_tag_names();

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and
	 * used to generate editor live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {}
}
