<?php
namespace CmsmastersElementor\Modules\Effects;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Effects\Components;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon clip-path, transform & floating effects module.
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

	private $transform_element;

	private $background_effect = false;

	private $effect_types = array();

	private $background_selector = array();

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
		return 'effects';
	}

	protected function init_actions() {
		$this->effect_types = array(
			'transform' => __( 'Transform', 'cmsmasters-elementor' ),
			'tilt' => __( '3D Tilt', 'cmsmasters-elementor' ),
			'mouse_track' => __( 'Mouse Track', 'cmsmasters-elementor' ),
			'scroll' => __( 'Scrolling', 'cmsmasters-elementor' ),
			'floating' => __( 'Floating', 'cmsmasters-elementor' ),
		);

		$action_types = array(
			'section',
			'container',
			'column',
			'common',
		);

		foreach ( $action_types as $type ) {
			add_action( "elementor/element/{$type}/_section_responsive/after_section_end", array( $this, "register_{$type}_controls" ) );
		}

		add_action( 'elementor/element/section/section_background/before_section_end', array( $this, 'register_section_background_controls' ) );
		add_action( 'elementor/element/container/section_background/before_section_end', array( $this, 'register_section_background_controls' ) );
		add_action( 'elementor/element/column/section_style/before_section_end', array( $this, 'register_column_background_controls' ) );
	}

	/**
	 * Register section controls.
	 *
	 * Adds editor sections advanced section effects controls.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed effect inheritance in child elements.
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_section_controls( $element ) {
		$this->parent = $element;
		$this->transform_element = '> .elementor-container';

		$this->register_controls_section();
	}

	/**
	 * Register container controls.
	 *
	 * Adds editor containers advanced container effects controls.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed effect inheritance in child elements.
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_container_controls( $element ) {
		$this->parent = $element;
		$this->transform_element = '.e-con';

		$this->register_controls_section();
	}

	/**
	 * Register column controls.
	 *
	 * Adds editor columns advanced section effects controls.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed effect inheritance in child elements.
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_column_controls( $element ) {
		$is_dome_optimization_active = Plugin::elementor()->experiments->is_feature_active( 'e_dom_optimization' );
		$main_selector_element = $is_dome_optimization_active ? 'widget' : 'column';

		$this->parent = $element;
		$this->transform_element = "> .elementor-{$main_selector_element}-wrap";

		$this->register_controls_section();
	}

	/**
	 * Register common controls.
	 *
	 * Adds editor widgets advanced section effects controls.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed effect inheritance in child elements.
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_common_controls( $element ) {
		$this->parent = $element;

		$this->register_controls_section();
	}

	private function register_controls_section() {
		$this->background_effect = false;

		$section_name = 'cmsmasters_section_transform';

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
				'label' => __( 'Effects', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);

		$this->register_controls();

		$this->parent->end_controls_section();
	}

	/**
	 * Register section background controls.
	 *
	 * Adds editor sections style section background effects controls.
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_section_background_controls( $element ) {
		$this->parent = $element;
		$this->transform_element = '.elementor-container';

		$search_for = '{{WRAPPER}}:not(.elementor-motion-effects-element-type-background), ' .
		'{{WRAPPER}} > .elementor-motion-effects-container > .elementor-motion-effects-layer';

		$replace_to = '{{WRAPPER}}:not(.elementor-motion-effects-element-type-background):not(.cmsmasters-bg-effect), ' .
			'{{WRAPPER}} > .elementor-motion-effects-container > .elementor-motion-effects-layer, ' .
			'{{WRAPPER}} > .cmsmasters-bg-effects-container > .cmsmasters-bg-effects-element';

		$this->background_selector = array(
			'search' => $search_for,
			'replace' => $replace_to,
		);

		$this->register_background_controls();
	}

	/**
	 * Register column background controls.
	 *
	 * Adds editor columns style section background effects controls.
	 *
	 * @since 1.0.0
	 *
	 * @param Controls_Stack $element Controls stack class instance.
	 */
	public function register_column_background_controls( $element ) {
		$is_dome_optimization_active = Plugin::elementor()->experiments->is_feature_active( 'e_dom_optimization' );
		$main_selector_element = $is_dome_optimization_active ? 'widget' : 'column';

		$this->parent = $element;
		$this->transform_element = ".elementor-{$main_selector_element}-wrap";

		$search_for = "{{WRAPPER}}:not(.elementor-motion-effects-element-type-background) > .elementor-{$main_selector_element}-wrap, " .
			"{{WRAPPER}} > .elementor-{$main_selector_element}-wrap > .elementor-motion-effects-container > .elementor-motion-effects-layer";

		$replace_to = "{{WRAPPER}}:not(.elementor-motion-effects-element-type-background):not(.cmsmasters-bg-effect) > .elementor-{$main_selector_element}-wrap, " .
			"{{WRAPPER}} > .elementor-{$main_selector_element}-wrap > .elementor-motion-effects-container > .elementor-motion-effects-layer, " .
			"{{WRAPPER}} > .elementor-{$main_selector_element}-wrap > .cmsmasters-bg-effects-container > .cmsmasters-bg-effects-element";

		$this->background_selector = array(
			'search' => $search_for,
			'replace' => $replace_to,
		);

		$this->register_background_controls();
	}

	/**
	 * @since 1.0.0
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 */
	private function register_background_controls() {
		$this->background_effect = true;

		$this->update_background_group_controls();

		$this->register_controls();

		$this->parent->update_control(
			$this->get_control_name( 'effect_type' ),
			array(
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'background_background',
							'value' => 'classic',
						),
						array(
							'name' => 'background_background',
							'value' => 'gradient',
						),
					),
				),
			)
		);

		foreach ( array(
			'background',
			'background_hover',
		) as $control ) {
			$this->parent->add_control(
				"{$control}_notice",
				array(
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'content' => esc_html__( 'When selecting Dynamic background image, set the Fallback Image to properly apply the display image settings.', 'cmsmasters-elementor' ),
					'condition' => array(
						"{$control}_background" => array( 'classic' ),
						"{$control}_image[url]!" => '',
					),
				)
			);
		}
	}

	/**
	 * Update background group controls.
	 *
	 * Replace background group controls with effects module selectors.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed responsive controls selectors.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 * @since 1.3.7 Fixed controls for custom breakpoints.
	 */
	private function update_background_group_controls() {
		$controls_manager = Plugin::elementor()->controls_manager;

		$background_controls = $controls_manager->get_control_groups( 'background' );

		foreach ( $background_controls->get_fields() as $name => $field ) {
			if ( ! isset( $field['selectors'] ) ) {
				continue;
			}

			$control_ids = array( "background_{$name}" );

			if ( isset( $field['responsive'] ) && Plugin::elementor()->experiments->is_feature_active( 'additional_custom_breakpoints' ) ) {
				$devices = Utils::get_devices();

				foreach( $devices as $device ) {
					$control_ids[] = "background_{$name}_{$device}";
				}
			}

			foreach ( $control_ids as $control_id ) {
				$control_data = $controls_manager->get_control_from_stack( $this->parent->get_unique_name(), $control_id );

				if ( is_wp_error( $control_data ) ) {
					continue;
				}

				$custom_selector = str_replace(
					$this->background_selector['search'],
					$this->background_selector['replace'],
					key( $control_data['selectors'] )
				);

				$this->parent->update_control(
					$control_id,
					array(
						'selectors' => array( $custom_selector => current( $control_data['selectors'] ) ),
					)
				);
			}
		}
	}

	private function register_controls() {
		$effect_options = $this->effect_types;
		$effects = array_merge( array_keys( $this->effect_types ), array( 'clip_path' ) );
		$class_suffix = '';

		if ( $this->background_effect ) {
			array_shift( $effect_options );

			array_shift( $effects );
			array_pop( $effects );

			$class_suffix = '-bg';
		}

		$this->parent->add_control(
			$this->get_control_name( 'effect_type' ),
			array(
				'label' => __( 'Effect Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array_merge( array( '' => __( 'None', 'cmsmasters-elementor' ) ), $effect_options ),
				'prefix_class' => "cmsmasters{$class_suffix}-effect cmsmasters{$class_suffix}-effect-type-",
			)
		);

		foreach ( $effects as $effect ) {
			$component = __NAMESPACE__ . '\\Components\\' . ucwords( $effect, '_' );

			$this->add_component( $effect, new $component( $this->parent, $this->transform_element, $this->background_effect ) );
		}

		$this->image_hide_bg_control();

		if ( ! $this->background_effect ) {
			$this->parent->add_control(
				$this->get_control_name( 'effect_notice' ),
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( 'You can use either CMS effects or Transform/Motion effects on a single element, but not both at the same time.', 'cmsmasters-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		}
	}

	/**
	 * Added control for hide background image on responsive.
	 *
	 * @since 1.3.8
	 */
	public function image_hide_bg_control() {
		$this->parent->add_control(
			$this->get_control_name( 'hide_bg_heding' ),
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Hide Background Image', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'background_background' => array( 'classic' ),
					'background_image[url]!' => '',
					$this->get_control_name( 'effect_type' ) => '',
				),
			)
		);

		$this->parent->add_control(
			$this->get_control_name( 'hide_bg' ),
			array(
				'label' => __( 'Hide', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'Disabled', 'cmsmasters-elementor' ),
					),
					'tablet' => array(
						'title' => __( 'On Tablet', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => __( 'On Mobile', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'none',
				'show_label' => false,
				'prefix_class' => "cmsmasters-bg-hide-",
				'condition' => array(
					'background_background' => array( 'classic' ),
					'background_image[url]!' => '',
					$this->get_control_name( 'effect_type' ) => '',
				),
			)
		);
	}

	private function get_control_name( $name = '' ) {
		$control_name = 'cms';

		if ( $this->background_effect ) {
			$control_name .= '_bg';
		}

		if ( ! empty( $name ) ) {
			$control_name .= "_{$name}";
		}

		return $control_name;
	}

	/**
	 * Get transform component.
	 *
	 * Retrieve the transform effects module component.
	 *
	 * @return Components\Transform Transform effects component.
	 */
	public function get_transform_component() {
		return $this->get_component( 'transform' );
	}

	/**
	 * Get scroll component.
	 *
	 * Retrieve the scroll track effects module component.
	 *
	 * @return Components\Scroll Scroll Track effects component.
	 */
	public function get_scroll_component() {
		return $this->get_component( 'scroll' );
	}

	/**
	 * Get mouse track component.
	 *
	 * Retrieve the mouse track effects module component.
	 *
	 * @return Components\Mouse_Track Mouse Track effects component.
	 */
	public function get_mouse_track_component() {
		return $this->get_component( 'mouse-track' );
	}

	/**
	 * Get tilt component.
	 *
	 * Retrieve the tilt effects module component.
	 *
	 * @return Components\Tilt 3D Tilt effects component.
	 */
	public function get_tilt_component() {
		return $this->get_component( 'tilt' );
	}

	/**
	 * Get floating component.
	 *
	 * Retrieve the floating effects module component.
	 *
	 * @return Components\Floating Floating effects component.
	 */
	public function get_floating_component() {
		return $this->get_component( 'floating' );
	}

	/**
	 * Get clip-path component.
	 *
	 * Retrieve the clip-path effects module component.
	 *
	 * @return Components\Clip_Path Clip-Path effects component.
	 */
	public function get_clip_path_component() {
		return $this->get_component( 'clip_path' );
	}
}
