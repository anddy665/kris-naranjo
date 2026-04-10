<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon text background control.
 *
 * A base controls group for creating text background controls.
 * Displays fields to define the color, gradient background image.
 *
 * @since 1.17.0
 */
class Group_Control_Vars_Text_Background extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the group control fields.
	 *
	 * @since 1.17.0
	 *
	 * @var array Group control fields.
	 */
	protected static $fields;

	/**
	 * Background Types.
	 *
	 * Holds all the available background types.
	 *
	 * @since 1.17.0
	 *
	 * @var array
	 */
	private static $background_types;

	/**
	 * Get background control type.
	 *
	 * Retrieve the control type, in this case `background`.
	 *
	 * @since 1.17.0
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_TEXT_BACKGROUND_GROUP;
	}

	/**
	 * Get background control types.
	 *
	 * Retrieve available background types.
	 *
	 * @since 1.17.0
	 *
	 * @return array Available background types.
	 */
	public static function get_background_types() {
		if ( null === self::$background_types ) {
			self::$background_types = self::get_default_background_types();
		}

		return self::$background_types;
	}

	/**
	 * Get Default background types.
	 *
	 * Retrieve button background control initial types.
	 *
	 * @since 1.17.0
	 *
	 * @return array Default background types.
	 */
	private static function get_default_background_types() {
		return array(
			'default' => array(
				'title' => _x( 'Default', 'Text Background Control', 'cmsmasters-elementor' ),
				'icon' => 'eicon-paint-brush',
			),
			'gradient' => array(
				'title' => _x( 'Gradient', 'Text Background Control', 'cmsmasters-elementor' ),
				'icon' => 'eicon-barcode',
			),
			'background-image' => array(
				'title' => _x( 'Image', 'Text Background Control', 'cmsmasters-elementor' ),
				'icon' => 'eicon-image',
			),
		);
	}

	/**
	 * Init fields.
	 *
	 * Initialize control group fields.
	 *
	 * @since 1.17.0
	 *
	 * @return array Control fields.
	 */
	public function init_fields() {
		$fields = array();

		$fields['text_bg_variation'] = array(
			'label' => __( 'Text Background', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'toggle' => true,
			'render_type' => 'template',
			'prefix_class' => 'cmsmasters-color-variation-',
		);

		$fields = array_merge(
			$fields,
			$this->register_text_gradient_controls(),
			$this->register_text_image_controls()
		);

		return $fields;
	}

	/**
	 * Register text background gradient controls.
	 *
	 * @since 1.17.0
	 */
	protected function register_text_gradient_controls() {
		$fields = array();

		$fields['text_color_normal'] = array(
			'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => '--text-color: {{VALUE}};',
			),
			'condition' => array( 'text_bg_variation!' => '' ),
		);

		$fields['text_color_stop_normal'] = array(
			'label' => esc_html__( 'Location', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default' => array(
				'unit' => '%',
				'size' => 0,
			),
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--text-color-stop: {{SIZE}};',
			),
			'condition' => array( 'text_bg_variation' => 'gradient' ),
		);

		$fields['text_second_color_normal'] = array(
			'label' => esc_html__( 'Second Color', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::COLOR,
			'title' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
			'selectors' => array(
				'{{SELECTOR}}' => '--text-second-color: {{VALUE}};',
			),
			'condition' => array( 'text_bg_variation' => 'gradient' ),
		);

		$fields['text_second_color_stop_normal'] = array(
			'label' => esc_html__( 'Location', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default' => array(
				'unit' => '%',
				'size' => 100,
			),
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--text-second-color-stop: {{SIZE}};',
			),
			'condition' => array( 'text_bg_variation' => 'gradient' ),
		);

		$fields['text_gradient_type_normal'] = array(
			'label' => _x( 'Type', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'linear' => _x( 'Linear', 'Background Control', 'cmsmasters-elementor' ),
				'radial' => _x( 'Radial', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => 'linear',
			'prefix_class' => 'cmsmasters-color-gradient-',
			'render_type' => 'template',
			'condition' => array( 'text_bg_variation' => 'gradient' ),
		);

		$fields['text_gradient_angle_normal'] = array(
			'label' => _x( 'Angle', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'deg' ),
			'default' => array(
				'unit' => 'deg',
				'size' => 90,
			),
			'range' => array(
				'deg' => array( 'step' => 10 ),
			),
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--text-gradient-angle: {{SIZE}};',
			),
			'condition' => array(
				'text_bg_variation' => 'gradient',
				'text_gradient_type_normal' => 'linear',
			),
		);

		$fields['text_gradient_position_normal'] = array(
			'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
				'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
				'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
				'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
				'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
				'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
				'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
				'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
				'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => 'center center',
			'selectors' => array(
				'{{SELECTOR}}' => '--text-gradient-radial: at {{VALUE}};',
			),
			'condition' => array(
				'text_bg_variation' => 'gradient',
				'text_gradient_type_normal' => 'radial',
			),
		);

		$fields['text_gradient_animation'] = array(
			'label' => _x( 'Animate Gradient', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => false,
			'return_value' => 'enable',
			'render_type' => 'template',
			'prefix_class' => 'cmsmasters-color-variation-gradient-animation-',
			'condition' => array(
				'text_bg_variation' => 'gradient',
			),
		);

		$fields['text_gradient_animation_type'] = array(
			'label' => _x( 'Animation Type', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'cmsmasters_text_gradient_animation_shift' => __( 'Shift', 'cmsmasters-elementor' ),
				'cmsmasters_text_gradient_animation_pulse' => __( 'Pulse', 'cmsmasters-elementor' ),
				'cmsmasters_text_gradient_animation_wave' => __( 'Wave', 'cmsmasters-elementor' ),
			),
			'default' => 'cmsmasters_text_gradient_animation_shift',
			'selectors' => array(
				'{{SELECTOR}}' => '--text-gradient-animation-type: {{VALUE}};',
			),
			'condition' => array(
				'text_bg_variation' => 'gradient',
				'text_gradient_animation' => 'enable',
			),
		);

		$fields['title_gradient_animation_duration'] = array(
			'label' => _x( 'Animation Duration', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'default' => array( 'size' => 5 ),
			'range' => array(
				'px' => array(
					'max' => 10,
					'step' => 0.1,
				),
			),
			'selectors' => array(
				'{{SELECTOR}}' => '--text-gradient-animation-duration: {{SIZE}}s;',
			),
			'condition' => array(
				'text_bg_variation' => 'gradient',
				'text_gradient_animation' => 'enable',
			),
		);

		return $fields;
	}

	/**
	 * Register text background image controls.
	 *
	 * @since 1.17.0
	 */
	protected function register_text_image_controls() {
		$fields = array();

		$fields['text_background_image'] = array(
			'label' => _x( 'Image', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::MEDIA,
			'title' => _x( 'Background Image', 'Background Control', 'cmsmasters-elementor' ),
			'render_type' => 'template',
			'dynamic' => array( 'active' => true ),
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-image-url: url("{{URL}}");',
			),
			'condition' => array( 'text_bg_variation' => 'background-image' ),
		);

		$bg_img_condition = array(
			'text_bg_variation' => 'background-image',
			'text_background_image[url]!' => '',
		);

		$fields['text_background_image_hover'] = array(
			'label' => esc_html__( 'Image Visibility:', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'no' => array(
					'title' => esc_html__( 'Always', 'cmsmasters-elementor' ),
					'description' => esc_html__( 'Always show background image.', 'cmsmasters-elementor' ),
				),
				'yes' => array(
					'title' => esc_html__( 'On Hover', 'cmsmasters-elementor' ),
					'description' => esc_html__( 'Show background image only on text hover.', 'cmsmasters-elementor' ),
				),
			),
			'default' => 'no',
			'prefix_class' => 'cmsmasters-bg-image-hover-',
			'render_type' => 'template',
			'condition' => $bg_img_condition,
		);

		$bg_img_condition_hover = $bg_img_condition;

		$bg_img_condition_hover['text_background_image_hover'] = 'yes';

		$fields['text_background_image_hover_position'] = array(
			'label' => esc_html__( 'Hover Effect', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'top -40em left 0' => esc_html__( 'Top', 'cmsmasters-elementor' ),
				'top -40em right -40em' => esc_html__( 'Top Right', 'cmsmasters-elementor' ),
				'top 0 right -40em' => esc_html__( 'Right', 'cmsmasters-elementor' ),
				'bottom -40em right -40em' => esc_html__( 'Bottom Right', 'cmsmasters-elementor' ),
				'bottom -40em left 0' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
				'bottom -40em left -40em' => esc_html__( 'Bottom Left', 'cmsmasters-elementor' ),
				'top 0 left -40em' => esc_html__( 'Left', 'cmsmasters-elementor' ),
				'top -40em left -40em' => esc_html__( 'Top Left', 'cmsmasters-elementor' ),
			),
			'default' => 'top -40em left 0',
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-hover-position: {{VALUE}};',
			),
			'condition' => $bg_img_condition_hover,
		);

		$bg_img_condition_not_hover = $bg_img_condition;

		$bg_img_condition_not_hover['text_background_image_hover'] = 'no';

		$fields['text_background_image_position'] = array(
			'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
				'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
				'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
				'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
				'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
				'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
				'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
				'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
				'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
				'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
				'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => '',
			'prefix_class' => 'cmsmasters-bg-image-position-',
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-position: {{VALUE}};',
			),
			'condition' => $bg_img_condition_not_hover,
		);

		$bg_img_condition_not_hover_initial = $bg_img_condition_not_hover;

		$bg_img_condition_not_hover_initial['text_background_image_position'] = 'initial';

		$fields['text_background_image_position_x'] = array(
			'label' => _x( 'X Position', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array(
				'px',
				'em',
				'%',
				'vw',
				'vh',
				'custom',
			),
			'range' => array(
				'px' => array(
					'min' => -800,
					'max' => 800,
				),
				'em' => array(
					'min' => -100,
					'max' => 100,
				),
				'%' => array(
					'min' => -100,
					'max' => 100,
				),
				'vw' => array(
					'min' => -100,
					'max' => 100,
				),
			),
			'default' => array(
				'unit' => 'px',
				'size' => 0,
			),
			'required' => true,
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-x-position: {{SIZE}}{{UNIT}};',
			),
			'condition' => $bg_img_condition_not_hover_initial,
		);

		$fields['text_background_image_position_y'] = array(
			'label' => _x( 'Y Position', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array(
				'px',
				'em',
				'%',
				'vw',
				'vh',
				'custom',
			),
			'range' => array(
				'px' => array(
					'min' => -800,
					'max' => 800,
				),
				'em' => array(
					'min' => -100,
					'max' => 100,
				),
				'%' => array(
					'min' => -100,
					'max' => 100,
				),
				'vh' => array(
					'min' => -100,
					'max' => 100,
				),
			),
			'default' => array(
				'unit' => 'px',
				'size' => 0,
			),
			'required' => true,
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-y-position: {{SIZE}}{{UNIT}};',
			),
			'condition' => $bg_img_condition_not_hover_initial,
		);

		$fields['text_background_image_attachment'] = array(
			'label' => _x( 'Attachment', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
				'scroll' => _x( 'Scroll', 'Background Control', 'cmsmasters-elementor' ),
				'fixed' => _x( 'Fixed', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => '',
			'selectors' => array(
				'{{SELECTOR}}' => '--background-attachment: {{VALUE}};',
			),
			'condition' => $bg_img_condition_not_hover,
		);

		$bg_img_condition_not_hover_fixed = $bg_img_condition_not_hover;

		$bg_img_condition_not_hover_fixed['text_background_image_attachment'] = 'fixed';

		$fields['text_background_image_attachment_alert'] = array(
			'type' => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-control-field-description',
			'raw' => esc_html__( 'Note: Attachment Fixed works only on desktop.', 'cmsmasters-elementor' ),
			'separator' => 'none',
			'condition' => $bg_img_condition_not_hover_fixed,
		);

		$fields['text_background_image_repeat'] = array(
			'label' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
				'no-repeat' => _x( 'No-repeat', 'Background Control', 'cmsmasters-elementor' ),
				'repeat' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
				'repeat-x' => _x( 'Repeat-x', 'Background Control', 'cmsmasters-elementor' ),
				'repeat-y' => _x( 'Repeat-y', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => '',
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-repeat: {{VALUE}};',
			),
			'condition' => $bg_img_condition_not_hover,
		);

		$fields['text_background_image_size'] = array(
			'label' => _x( 'Size', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
				'auto' => _x( 'Auto', 'Background Control', 'cmsmasters-elementor' ),
				'cover' => _x( 'Cover', 'Background Control', 'cmsmasters-elementor' ),
				'contain' => _x( 'Contain', 'Background Control', 'cmsmasters-elementor' ),
				'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
			),
			'default' => '',
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-size: {{VALUE}};',
			),
			'condition' => $bg_img_condition_not_hover,
		);

		$fields['text_background_image_bg_width'] = array(
			'label' => _x( 'Width', 'Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array(
				'px',
				'em',
				'%',
				'vw',
				'vh',
				'custom',
			),
			'range' => array(
				'px' => array(
					'min' => 0,
					'max' => 1000,
				),
			),
			'default' => array(
				'size' => 100,
				'unit' => '%',
			),
			'required' => true,
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-size-initial: {{VALUE}};',

			),
			'condition' => $bg_img_condition_not_hover_initial,
		);

		$fields['text_background_transition'] = array(
			'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'default' => array( 'size' => 0.3 ),
			'range' => array(
				'px' => array(
					'max' => 3,
					'step' => 0.1,
				),
			),
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => '--background-hover-transition: {{SIZE}}s;',
			),
			'condition' => $bg_img_condition_hover,
		);

		return $fields;
	}

	/**
	 * Get child default args.
	 *
	 * Retrieve the default arguments for all the child controls for a
	 * specific group control.
	 *
	 * @since 1.17.0
	 *
	 * @return array Default arguments for all the child controls.
	 */
	protected function get_child_default_args() {
		return array(
			'types' => array(
				'default',
				'gradient',
				'background-image',
			),
			'selector' => '{{SELECTOR}}',
		);
	}

	/**
	 * Filter fields.
	 *
	 * Filter which controls to display, using `include`, `exclude`,
	 * `condition` and `of_type` arguments.
	 *
	 * @since 1.17.0
	 *
	 * @return array Control fields.
	 */
	protected function filter_fields() {
		$fields = parent::filter_fields();

		$args = $this->get_args();

		foreach ( $fields as &$field ) {
			if (
				isset( $field['of_type'] ) &&
				! in_array( $field['of_type'], $args['types'], true )
			) {
				unset( $field );
			}
		}

		return $fields;
	}

	/**
	 * Prepare fields.
	 *
	 * Process text background control fields before adding them to `add_control()`.
	 *
	 * @since 1.17.0
	 *
	 * @param array $fields Control group fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		$background_types = self::get_background_types();

		$choose_types = array();

		foreach ( $args['types'] as $type ) {
			if ( isset( $background_types[ $type ] ) ) {
				$choose_types[ $type ] = $background_types[ $type ];
			}
		}

		$fields['text_bg_variation']['options'] = $choose_types;

		return parent::prepare_fields( $fields );
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the text background control.
	 * Used to return the default options while initializing the text background control.
	 *
	 * @since 1.17.0
	 *
	 * @return array Default background control options.
	 */
	protected function get_default_options() {
		return array( 'popover' => false );
	}
}
