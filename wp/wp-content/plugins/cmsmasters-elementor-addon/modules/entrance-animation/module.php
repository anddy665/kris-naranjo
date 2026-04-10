<?php
namespace CmsmastersElementor\Modules\EntranceAnimation;

use CmsmastersElementor\Base\Base_Module;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Entrance Animation module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	public function get_name() {
		return 'entrance-animation';
	}

	protected function init_filters() {
		add_filter( 'elementor/controls/animations/additional_animations', array( $this, 'extend_entrance_animations' ) );

		// Added new animation
		add_action( 'elementor/element/common/section_effects/before_section_end', array( $this, 'add_params_animation_type' ), 10, 2 );

		// Before Elementor 2.1.0
		add_action( 'elementor/frontend/element/before_render', array( $this, 'add_animation_data_to_widgets' ), 10, 1 );
		// After Elementor 2.1.0
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'add_animation_data_to_widgets' ), 10, 1 );
	}

	public function extend_entrance_animations( $additional_animations ) {
		$animations = array_merge(
			$additional_animations,
			array(
				esc_html__( 'CMSMasters Fading', 'cmsmasters-elementor' ) => array(
					'cmsmasters-fade-in' => esc_html__( 'Fade In (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-fade-in-down' => esc_html__( 'Fade In Down (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-fade-in-left' => esc_html__( 'Fade In Left (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-fade-in-right' => esc_html__( 'Fade In Right (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-fade-in-up' => esc_html__( 'Fade In Up (CMS)', 'cmsmasters-elementor' ),
				),
				esc_html__( 'CMSMasters Popping', 'cmsmasters-elementor' ) => array(
					'cmsmasters-pop-in' => esc_html__( 'Pop In (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-pop-in-down' => esc_html__( 'Pop In Down (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-pop-in-left' => esc_html__( 'Pop In Left (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-pop-in-right' => esc_html__( 'Pop In Right (CMS)', 'cmsmasters-elementor' ),
					'cmsmasters-pop-in-up' => esc_html__( 'Pop In Up (CMS)', 'cmsmasters-elementor' ),
				),
			)
		);

		return $animations;
	}

	/**
	 * Added new entrance animation type.
	 *
	 * @since 1.18.0
	 */
	public function add_params_animation_type( $element, $args ) {
		if ( ! is_object( $element ) ) {
			return;
		}

		$types = array(
			'block' => esc_html__( 'Whole block', 'cmsmasters-elementor' ),
			'sequental' => esc_html__( 'Item by item', 'cmsmasters-elementor' ),
			'random' => esc_html__( 'Random items', 'cmsmasters-elementor' ),
		);

		$types_text = $types + array(
			'line' => esc_html__( 'Line by line', 'cmsmasters-elementor' ),
			'word' => esc_html__( 'Word by word', 'cmsmasters-elementor' ),
			'char' => esc_html__( 'Char by char', 'cmsmasters-elementor' ),
		);

		$element->add_control( '_animation_type', array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Animation type', 'cmsmasters-elementor' ),
			'label_block' => false,
			'description' => esc_html__( 'This animation type is not visible in the editor. Please open the page preview or view the updated published page to see it.', 'cmsmasters-elementor' ),
			'options' => $types,
			'default' => 'block',
			'render_type' => 'template',
			'prefix_class' => 'animation_type_',
			'condition' => array(
				'_animation!' => array( '', 'none' ),
				'entrance_animation' => 'yes',
				'entrance_animation_text' => '',
			),
		) );

		$element->add_control( '_animation_type_text', array(
			'type' => Controls_Manager::SELECT,
			'label' => esc_html__( 'Animation type', 'cmsmasters-elementor' ),
			'label_block' => false,
			'description' => esc_html__( 'This animation type is not visible in the editor. Please open the page preview or view the updated published page to see it.', 'cmsmasters-elementor' ),
			'options' => $types_text,
			'default' => 'block',
			'render_type' => 'template',
			'prefix_class' => 'animation_type_',
			'condition' => array(
				'_animation!' => array( '', 'none' ),
				'entrance_animation' => 'yes',
				'entrance_animation_text' => 'yes',
			),
		) );

		$element->add_control( '_animation_stagger', array(
			'type' => Controls_Manager::NUMBER,
			'label' => esc_html__( 'Stagger (ms)', 'cmsmasters-elementor' ),
			'description' => esc_html__( 'A delay before the next item appears. If not specified - the value from the `Animation Delay` field is used.', 'cmsmasters-elementor' ),
			'default' => '200',
			'min' => 0,
			'max' => 5000,
			'step' => 10,
			'render_type' => 'template',
			'condition' => array(
				'_animation!' => array( '', 'none' ),
				'entrance_animation' => 'yes',
			),
		) );
	}

	/**
	 * Added entrance animation data to widgets.
	 *
	 * @since 1.18.0
	 */
	public function add_animation_data_to_widgets( $element ) {
		$entrance_animation = $element->get_settings( 'entrance_animation' );

		if ( empty( $entrance_animation ) ) {
			return;
		}

		$animation = $element->get_settings( '_animation' );

		if ( empty( $animation ) ) {
			$animation = $element->get_settings( 'animation' );
		}

		if ( ! empty( $animation ) && 'none' !== $animation ) {
			$entrance_animation_text = $element->get_settings( 'entrance_animation_text' );

			$type = '';

			if ( 'yes' === $entrance_animation_text ) {
				$type = $element->get_settings( '_animation_type_text' );
			} else {
				$type = $element->get_settings( '_animation_type' );
			}

			if ( empty( $type ) ) {
				return;
			}

			$element->add_render_attribute( '_wrapper', 'data-animation-type', $type );
			$element->add_render_attribute( '_wrapper', 'class', 'animation_type_' . $type );

			$stagger = $element->get_settings( '_animation_stagger' );

			$element->add_render_attribute( '_wrapper', 'data-animation-stagger', ! empty( $stagger ) ? $stagger : '' );
		}
	}
}
