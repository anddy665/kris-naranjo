<?php
namespace CmsmastersElementor\Modules\Blog\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog;
use CmsmastersElementor\Modules\MetaData\Classes\Meta_Data;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Ticker extends Base_Blog {

	public $meta_data = false;

	protected $_has_template_content = false; //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	public function get_title() {
		return __( 'Posts Ticker', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-posts-ticker';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.5.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'carousel',
			'slider',
			'ticker',
		);
	}

	/**
	 * Get scripts dependencies.
	 *
	 * Retrieve the list of scripts dependencies the widget requires.
	 *
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'swiper' );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.5.0
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array_merge( array(
			'e-swiper',
		), parent::get_style_depends() );
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-ticker';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return false;
	}

	/**
	 * Register widget skins.
	 *
	 * This method is activated while initializing the widget base class.
	 * It is used to assign skins to widgets with `add_skin()` method.
	 *
	 * @since 1.5.0
	 */
	protected function register_skins() {
		$this->add_skin( new Ticker_Skins\Slider( $this ) );
		$this->add_skin( new Ticker_Skins\Marquee( $this ) );
	}

	public function register_controls() {
		/* Tab Content */
		$this->section_post();
		$this->section_header();
		parent::register_controls();

		/* Tab Style */
		$this->section_style_header();
		$this->section_style_post();
		$this->section_style_title();
		$this->section_style_meta_data();
	}

	public function __construct( $data = array(), $args = null ) {
		$this->meta_data = new Meta_Data( $this );

		parent::__construct( $data, $args );
	}

	protected function section_post() {
		$this->start_controls_section(
			'section_post',
			array(
				'label' => __( 'Post', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->update_control(
			'_skin',
			array(
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'slider' => array( 'title' => __( 'Slider', 'cmsmasters-elementor' ) ),
					'marquee' => array( 'title' => __( 'Marquee', 'cmsmasters-elementor' ) ),
				),
				'default' => 'marquee',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-ticker-skin-',
				'render_type' => 'template',
			)
		);

		$this->meta_data->register_controls_content();

		$options_control = $this->get_controls( $this->meta_data->get_prefix( 'options' ) );

		$this->update_control( $this->meta_data->get_prefix( 'options' ), array(
			'default' => array( 'date' ),
			'options' => array(
				'author' => ( isset( $options_control['options'] ) ? $options_control['options']['author'] : '' ),
				'time' => ( isset( $options_control['options'] ) ? $options_control['options']['time'] : '' ),
				'date' => ( isset( $options_control['options'] ) ? $options_control['options']['date'] : '' ),
			),
		) );

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Posts', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '3',
				'min' => 1,
				'separator' => 'before',
				'condition' => array(
					self::QUERY_CONTROL_PREFIX . '_post_type!' => 'current_query',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function section_header() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_header',
			array(
				'label' => __( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'header_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Ticker', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-info-circle',
					'library' => 'fa-solid',
				),
				'label_block' => false,
				'skin' => 'inline',
			)
		);

		$this->add_control(
			'header_icon_align',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'left',
				'selectors_dictionary' => array(
					'left' => '0',
					'right' => '1',
				),
				'label_block' => false,
				'prefix_class' => 'cmsmasters-header-icon-align-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header .cmsmasters-wrap-icon' => 'order: {{VALUE}};',
				),
				'condition' => array(
					'header_title!' => '',
					'header_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_post() {
		$this->start_controls_section(
			'section_style_post',
			array(
				'label' => __( 'Post', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'post_bg',
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-ticker-posts',
			)
		);

		$this->add_responsive_control(
			'post_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ticker-posts' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'post_border',
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-ticker-posts',
			)
		);

		$this->add_control(
			'post_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ticker-posts' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'post_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-ticker-posts',
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_title() {
		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-ticker-post-title, {{WRAPPER}} .cmsmasters-ticker-post-title a',
			)
		);

		$this->add_control(
			'title_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ticker-post-title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ticker-post-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_vertical_align',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'baseline' => __( 'Default', 'cmsmasters-elementor' ),
					'flex-start' => __( 'Top', 'cmsmasters-elementor' ),
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'flex-end' => __( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'baseline',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-inner' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_meta_data() {
		$this->start_controls_section(
			'section_style_meta_data',
			array(
				'label' => __( 'Meta Data', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->meta_data->get_prefix( 'show!' ) => '',
				),
			)
		);

		$this->add_responsive_control(
			'meta_data_ver_space',
			array(
				'label' => __( 'Vertical Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-meta-data-item' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 )',
					'{{WRAPPER}} .cmsmasters-widget-meta-data-inner' => 'margin-top: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-bottom: calc( -{{SIZE}}{{UNIT}} / 2 )',
				),
				'condition' => array( '_skin' => 'slider' ),
			)
		);

		$this->meta_data->register_controls_style();

		$this->end_controls_section();
	}

	protected function section_style_header() {
		$widget_selector = $this->get_widget_selector();

		$header_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'header_title',
					'operator' => '!=',
					'value' => '',
				),
				array(
					'name' => 'header_icon[value]',
					'operator' => '!=',
					'value' => '',
				),
			),
		);

		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => __( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $header_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__header',
				'condition' => array( 'header_title!' => '' ),
			)
		);

		$this->add_control(
			'header_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( 'header_title!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'header_bg',
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__header',
				'conditions' => $header_conditions,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-header-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $header_conditions,
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $header_conditions,
			)
		);

		$this->add_responsive_control(
			'header_min_height',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 35,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header' => 'min-height: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $header_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'header_border',
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__header',
				'conditions' => $header_conditions,
			)
		);

		$this->add_control(
			'header_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $header_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'header_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__header',
				'conditions' => $header_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'header_text_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__header',
				'condition' => array( 'header_title!' => '' ),
			)
		);

		$this->add_control(
			'header_icon_style_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'header_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'header_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header i' => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__header svg' => 'fill: {{VALUE}};',
				),
				'condition' => array( 'header_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'header_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 1,
					),
					'em' => array(
						'max' => 5,
						'min' => 0.1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__header i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__header svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'header_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'header_icon_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-header-icon-align-left ' . $widget_selector . '__header .cmsmasters-wrap-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-header-icon-align-right ' . $widget_selector . '__header .cmsmasters-wrap-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'header_title!' => '',
					'header_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	public function render_header() {
		$settings = $this->get_settings();

		$header_icon = ( isset( $settings['header_icon'] ) && ! empty( $settings['header_icon']['value'] ) ? $settings['header_icon'] : '' );
		$header_title = ( isset( $settings['header_title'] ) && ! empty( $settings['header_title'] ) ? $settings['header_title'] : '' );

		if ( $header_icon || $header_title ) {
			echo '<div class="' . $this->get_widget_class() . '__header' .
				( $header_icon && $header_title ? ' cmsmasters_header_show_both' : '' ) .
			'">';

			if ( $header_icon ) {
				Utils::render_icon( $header_icon, array( 'aria-hidden' => 'true' ) );
			}

			if ( $header_title ) {
				echo '<span class="' . $this->get_widget_class() . '__title">' .
					esc_html( $header_title ) .
				'</span>';
			}

			echo '</div>';
		}
	}

	/**
	 * Render post markers.
	 *
	 * @since 1.5.0
	 */
	public function marker_post() {
		return '';
	}

	protected function render_post_inner() {}

	protected function render_blog() {}

}
