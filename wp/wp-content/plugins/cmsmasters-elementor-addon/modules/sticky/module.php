<?php
namespace CmsmastersElementor\Modules\Sticky;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor google maps module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Controls parent class.
	 *
	 * @since 1.0.0
	 *
	 * @var Controls_Stack
	 */
	private $parent;

	/**
	 * Controls parent type.
	 *
	 * Checks if controls parent type is section
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_section;

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'sticky';
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		$action_types = array(
			'section',
			'container',
			'common',
		);

		foreach ( $action_types as $type ) {
			add_action( "elementor/element/{$type}/_section_responsive/after_section_end", array( $this, 'register_controls_sticky' ) );
		}
	}

	public function register_controls_sticky( $element ) {
		$this->parent = $element;

		$this->is_section = 'section' === $this->parent->get_type() || 'container' === $this->parent->get_type();

		$section_name = 'cmsmasters_section_sticky';

		$controls_manager = Plugin::elementor()->controls_manager;
		$section_check = $controls_manager->get_control_from_stack(
			$this->parent->get_unique_name(),
			$section_name
		);

		if ( ! is_wp_error( $section_check ) ) {
			return false;
		}

		$this->parent->start_controls_section(
			$section_name,
			array(
				'label' => __( 'Sticky', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);

		$sticky_type_args = array(
			'label' => __( 'View', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'default' => array(
					'title' => __( 'Default', 'cmsmasters-elementor' ),
					'description' => __( 'Disabled', 'cmsmasters-elementor' ),
				),
				'sticky' => array(
					'title' => __( 'Sticky', 'cmsmasters-elementor' ),
					'description' => __( 'Sticks block in the parent section or page body tag.', 'cmsmasters-elementor' ),
				),
				'fixed' => array(
					'title' => __( 'Header', 'cmsmasters-elementor' ),
					'description' => __( 'Sticks block in the top of the page.', 'cmsmasters-elementor' ),
				),
			),
			'default' => 'default',
			'prefix_class' => self::get_control_prefix_class( '', 'block' ),
			'toggle' => false,
		);

		if ( ! $this->is_section ) {
			unset( $sticky_type_args['options']['fixed'] );
		}

		$this->parent->add_control( self::get_control_full_name( 'type' ), $sticky_type_args );

		$this->register_fixed_controls();

		$this->register_sticky_controls();

		$this->parent->end_controls_section();
	}

	private static function get_control_full_name( $name = '' ) {
		$control_name = 'cms_sticky';

		if ( ! empty( $name ) ) {
			$control_name .= "_{$name}";
		}

		return $control_name;
	}

	private static function get_control_prefix_class( $control = '', $suffix = 'sticky' ) {
		$prefix = "cmsmasters-{$suffix}-";

		if ( ! empty( $control ) ) {
			$prefix .= "{$control}-";
		}

		return $prefix;
	}

	private function register_fixed_controls() {
		$this->parent->add_control(
			self::get_control_full_name( 'fixed_style' ),
			array(
				'label' => __( 'Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'slide' => __( 'Slide', 'cmsmasters-elementor' ),
					'swing' => __( 'Swing', 'cmsmasters-elementor' ),
					'flip' => __( 'Flip', 'cmsmasters-elementor' ),
					'bounce' => __( 'Bounce', 'cmsmasters-elementor' ),
				),
				'default' => 'slide',
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array( self::get_control_full_name( 'type' ) => 'fixed' ),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'fixed_offset' ),
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => __( 'Vertical offset (in pixels) before header section first time will be hidden. Default offset is equal to header height. By default equal to header height.', 'cmsmasters-elementor' ),
				'min' => 0,
				'step' => 5,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array( self::get_control_full_name( 'type' ) => 'fixed' ),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'fixed_top_gap' ),
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Adds page main container (body) top gap with size of fixed block height.', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'prefix_class' => self::get_control_prefix_class( 'body-top-gap', 'fixed' ),
				'condition' => array( self::get_control_full_name( 'type' ) => 'fixed' ),
			)
		);
	}

	/**
	 * Register Sticky controls.
	 *
	 * @since 1.0.0
	 * @since 1.20.0 Added new `Mobile Only` option to the `Sticky on Sizes` control.
	 */
	private function register_sticky_controls() {
		$scroll_in_args = array(
			'label' => __( 'Container', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'default' => array(
					'title' => __( 'Default', 'cmsmasters-elementor' ),
					'description' => __( 'Scroll in parent section.', 'cmsmasters-elementor' ),
				),
				'body' => array(
					'title' => __( 'Body', 'cmsmasters-elementor' ),
					'description' => __( 'Scroll in page body.', 'cmsmasters-elementor' ),
				),
				'custom' => array(
					'title' => __( 'Custom', 'cmsmasters-elementor' ),
					'description' => __( 'Scroll in block with custom ID or class.', 'cmsmasters-elementor' ),
				),
			),
			'default' => 'default',
			'prefix_class' => self::get_control_prefix_class( 'parent' ),
			'condition' => array( self::get_control_full_name( 'type' ) => 'sticky' ),
		);

		if ( $this->is_section ) {
			unset( $scroll_in_args['options']['body'] );
		}

		$this->parent->add_control( self::get_control_full_name( 'scroll_in' ), $scroll_in_args );

		$this->parent->add_control(
			self::get_control_full_name( 'custom_selector' ),
			array(
				'label' => __( 'Custom Selector', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '#my-id or .my-class', 'cmsmasters-elementor' ),
				'description' => __( 'Add your custom parent block id or class.', 'cmsmasters-elementor' ),
				'title' => __( 'ID or class must be WITH the Pound key or the dot respectively. (e.g: #my-id or .my-class)', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array(
					self::get_control_full_name( 'type' ) => 'sticky',
					self::get_control_full_name( 'scroll_in' ) => 'custom',
				),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'disable_on' ),
			array(
				'label' => __( 'Sticky on Sizes', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'All', 'cmsmasters-elementor' ),
						'description' => __( 'Sticky works on any window width.', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => '<i aria-hidden="true" class="eicon-arrow-up"></i> ' . __( 'Mobile', 'cmsmasters-elementor' ),
						'description' => __( 'Sticky works on window width higher than mobile.', 'cmsmasters-elementor' ),
					),
					'tablet' => array(
						'title' => '<i aria-hidden="true" class="eicon-arrow-up"></i> ' . __( 'Tablet', 'cmsmasters-elementor' ),
						'description' => __( 'Sticky works on window width higher than tablet.', 'cmsmasters-elementor' ),
					),
					'mobile-only' => array(
						'title' => __( 'Mobile Only', 'cmsmasters-elementor' ),
						'description' => __( 'Sticky works on mobile devices only.', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'mobile',
				'label_block' => true,
				'prefix_class' => self::get_control_prefix_class( 'disable' ),
				'condition' => array( self::get_control_full_name( 'type' ) => 'sticky' ),
			)
		);

		// $this->parent->add_control(
		// 	'sticky_direction',
		// 	array(
		// 		'label' => __( 'Sticky direction', 'cmsmasters-elementor' ),
		// 		'type' => CmsmastersControls::CHOOSE_TEXT,
		// 		'options' => array(
		// 			'top' => array(
		// 				'title' => __( 'Top', 'cmsmasters-elementor' ),
		// 				'description' => __( 'Search outside parent block.', 'cmsmasters-elementor' ),
		// 			),
		// 			'bottom' => array(
		// 				'title' => __( 'Bottom', 'cmsmasters-elementor' ),
		// 				'description' => __( 'Search inside parent block.', 'cmsmasters-elementor' ),
		// 			),
		// 		),
		// 		'render_type' => 'template',
		// 		'default' => 'top',
		// 		'frontend_available' => true,
		// 		'condition' => array( self::get_control_full_name( 'type' ) => 'sticky' ),
		// 	)
		// );

		$this->parent->add_responsive_control(
			self::get_control_full_name( 'offset_top' ),
			array(
				'label' => __( 'Offset Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 5,
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array( self::get_control_full_name( 'type' ) => 'sticky' ),
			)
		);

		$offset_bottom_condition = array( self::get_control_full_name( 'type' ) => 'sticky' );

		if ( $this->is_section ) {
			$offset_bottom_condition[ self::get_control_full_name( 'scroll_in' ) ] = 'custom';
			$offset_bottom_condition[ self::get_control_full_name( 'custom_selector!' ) ] = '';
		}

		$this->parent->add_responsive_control(
			self::get_control_full_name( 'offset_bottom' ),
			array(
				'label' => __( 'Offset Bottom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 5,
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => $offset_bottom_condition,
			)
		);

		if ( ! $this->is_section ) {
			$this->parent->add_control(
				self::get_control_full_name( 'follow_scroll' ),
				array(
					'label' => __( 'Follow Scroll', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'description' => __( 'When select `no`, sticky content will not move with the page if it is higher than screen height.', 'cmsmasters-elementor' ),
					'default' => 'yes',
					'prefix_class' => self::get_control_prefix_class( 'follow-scroll' ),
					'condition' => array( self::get_control_full_name( 'type' ) => 'sticky' ),
				)
			);
		}

		$this->register_sticky_style_controls();
	}

	/**
	 * Register sticky style controls.
	 *
	 * Adds sticky module custom styling controls.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added Minimum Height & Maximum Width controls.
	 * @since 1.12.6 Fixed `Minimum Height` and `Content Width` controls in sticky effect.
	 */
	private function register_sticky_style_controls() {
		$this->parent->add_control(
			self::get_control_full_name( 'style_heading' ),
			array(
				'label' => __( 'Sticky Block Custom Styling', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( self::get_control_full_name( 'type!' ) => 'default' ),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'style' ),
			array(
				'label' => __( 'Activate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition' => array( self::get_control_full_name( 'type!' ) => 'default' ),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'style_message' ),
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: The following settings will override the corresponding element default settings when block is sticky.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		$this->parent->add_control(
			self::get_control_full_name( 'style_background_color' ),
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}:not(.elementor-motion-effects-element-type-background):not(.cmsmasters-bg-effect).cmsmasters-sticky-active, ' .
					'{{WRAPPER}}:not(.elementor-motion-effects-element-type-background):not(.cmsmasters-bg-effect).headroom--not-top' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		$this->parent->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => self::get_control_full_name( 'style_border' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'default' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'default',
					),
					'width' => array(
						'label' => __( 'Border Width', 'cmsmasters-elementor' ),
						'condition' => array(),
						'conditions' => array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'border',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'border',
									'operator' => '!==',
									'value' => 'default',
								),
							),
						),
					),
					'color' => array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'condition' => array(),
						'conditions' => array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'border',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'border',
									'operator' => '!==',
									'value' => 'default',
								),
							),
						),
					),
				),
				'selector' => '{{WRAPPER}}.cmsmasters-sticky-active, {{WRAPPER}}.headroom--not-top',
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		if ( $this->is_section ) {
			$width = ( 'section' === $this->parent->get_type() ? 'layout' : 'content_width' );
			$height_control = ( 'section' === $this->parent->get_type() ? 'height' : 'min_height[size]!' );
			$height_value = ( 'section' === $this->parent->get_type() ? array( 'min-height' ) : '' );

			$this->parent->add_control(
				self::get_control_full_name( 'style_max_width' ),
				array(
					'label' => __( 'Content Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 500,
							'max' => 1600,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-sticky-active.elementor-section-boxed > .elementor-container, ' .
						'{{WRAPPER}}.headroom--not-top.elementor-section-boxed > .elementor-container, ' .
						'{{WRAPPER}}.cmsmasters-sticky-active.e-con-boxed > .e-con-inner, ' .
						'{{WRAPPER}}.headroom--not-top.e-con-boxed > .e-con-inner' => 'max-width: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$width => array( 'boxed' ),
						self::get_control_full_name( 'type!' ) => 'default',
						self::get_control_full_name( 'style' ) => 'yes',
					),
				)
			);

			$this->parent->add_responsive_control(
				self::get_control_full_name( 'style_min_height' ),
				array(
					'label' => __( 'Minimum Height', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
						'vh',
						'vw',
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 1440,
						),
						'vh' => array(
							'min' => 0,
							'max' => 100,
						),
						'vw' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-sticky-active.elementor-section-height-min-height > .elementor-container, ' .
						'{{WRAPPER}}.headroom--not-top.elementor-section-height-min-height > .elementor-container, ' .
						'{{WRAPPER}}.cmsmasters-sticky-active.e-con, ' .
						'{{WRAPPER}}.headroom--not-top.e-con' => 'min-height: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$height_control => $height_value,
						self::get_control_full_name( 'type!' ) => 'default',
						self::get_control_full_name( 'style' ) => 'yes',
					),
				)
			);
		}

		$this->parent->add_responsive_control(
			self::get_control_full_name( 'style_padding' ),
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-sticky-active, {{WRAPPER}}.headroom--not-top' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		$this->parent->add_responsive_control(
			self::get_control_full_name( 'style_border_radius' ),
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-sticky-active, {{WRAPPER}}.headroom--not-top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		$this->parent->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => self::get_control_full_name( 'style_box_shadow' ),
				'label' => __( 'Box Shadow', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}}.cmsmasters-sticky-active, {{WRAPPER}}.headroom--not-top',
				'condition' => array(
					self::get_control_full_name( 'type!' ) => 'default',
					self::get_control_full_name( 'style' ) => 'yes',
				),
			)
		);

		if ( ! $this->is_section ) {
			$this->parent->add_control(
				self::get_control_full_name( 'visibility' ),
				array(
					'label' => __( 'Sticky Visibility', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'default' => array(
							'title' => __( 'Default', 'cmsmasters-elementor' ),
							'description' => __( 'No action when parent is sticky.', 'cmsmasters-elementor' ),
						),
						'show' => array(
							'title' => __( 'Show', 'cmsmasters-elementor' ),
							'description' => __( 'Show block only when parent is sticky.', 'cmsmasters-elementor' ),
						),
						'hide' => array(
							'title' => __( 'Hide', 'cmsmasters-elementor' ),
							'description' => __( 'Hide block when parent is sticky.', 'cmsmasters-elementor' ),
						),
					),
					'default' => 'default',
					'prefix_class' => self::get_control_prefix_class(),
					'toggle' => false,
					'condition' => array( self::get_control_full_name( 'type' ) => 'default' ),
				)
			);
		}
	}

}
