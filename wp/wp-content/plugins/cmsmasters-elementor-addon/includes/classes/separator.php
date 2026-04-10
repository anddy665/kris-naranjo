<?php
namespace CmsmastersElementor\Classes;

use CmsmastersElementor\Controls_Manager as CmsmastersElementorControlsManager;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use CmsmastersElementor\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Separator {
	public $args = array();

	private $element;
	private $parent;
	private $render_cache = '';

	public function __construct( Base_Widget $parent, $args ) {
		$args_default = array(
			'label' => __( 'Separator', 'cmsmasters-elementor' ),
			'name' => 'separator',
			'selector' => '{{WRAPPER}}',
			'skin' => false,
		);

		$this->args = array_merge( $args_default, $args );
		$this->element = $this->args['skin'];
		$this->parent = $parent;

		if ( ! $this->element ) {
			$this->element = $parent;
		}
	}

	public function get_prefix( $control_base_id ) {
		$names = array( $this->args['name'], $control_base_id );

		return join( '_', $names );
	}

	protected function get_skin_control_id( $control_base_id ) {
		$skin_id = str_replace( '-', '_', $this->args['skin']->get_id() );

		return $skin_id . '_' . $control_base_id;
	}

	public function get_condition_prefix( $control_base_id ) {
		$id = $this->get_prefix( $control_base_id );

		if ( $this->args['skin'] ) {
			return $this->get_skin_control_id( $id );
		}

		return $id;
	}

	/**
	 * Register separator controls.
	 *
	 * Adds different fields to allow the user to change and customize the separator settings.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Fixed display of advanced separator settings when adding a skin.
	 */

	public function add_controls( $condition = array(), $conditions = array() ) {
		$conditions_popover = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => $this->get_condition_prefix( 'type' ),
									'value' => 'symbol',
								),
								array(
									'name' => $this->get_condition_prefix( 'content' ),
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => $this->get_condition_prefix( 'type' ),
									'value' => 'icon',
								),
								array(
									'name' => $this->get_condition_prefix( 'icon[value]' ),
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
				array(
					'name' => $this->get_condition_prefix( 'type' ),
					'operator' => 'in',
					'value' => array( 'icon', 'symbol' ),
				),
			),
		);

		if ( ! empty( $conditions ) ) {
			$conditions_popover['terms'][] = $conditions;
		}

		$this->element->add_control(
			$this->get_prefix( 'heading' ),
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
				'conditions' => $conditions,
			)
		);

		$this->element->add_responsive_control(
			$this->get_prefix( 'space_between' ),
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'vw' => array(
						'max' => 50,
					),
				),
				'size_units' => array( 'px', 'vw', 'em' ),
				'selectors' => array(
					$this->get_sep_selector() => '--cmsmasters-separator-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
				'conditions' => $conditions,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'type' ),
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersElementorControlsManager::CHOOSE_TEXT,
				'toggle' => false,
				'default' => 'symbol',
				'label_block' => false,
				'options' => array(
					'symbol' => array(
						'title' => __( 'Symbol', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'condition' => $condition,
				'conditions' => $conditions,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'content' ),
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'render_type' => 'template',
				'condition' => array_merge( array(
					$this->get_condition_prefix( 'type' ) => array( 'symbol' ),
				), $condition ),
				'conditions' => $conditions,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'icon' ),
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'render_type' => 'template',
				'condition' => array_merge( array(
					$this->get_condition_prefix( 'type' ) => 'icon',
				), $condition ),
				'conditions' => $conditions,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'color' ),
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->get_sep_selector() => 'color: {{VALUE}};',
				),
				'condition' => $condition,
				'conditions' => $conditions_popover,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'popover_toggle' ),
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
				'condition' => $condition,
				'conditions' => $conditions_popover,
			)
		);

		$this->parent->start_popover();

		$this->element->add_responsive_control(
			$this->get_prefix( 'size' ),
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default' => array(
					'size' => 1,
					'unit' => 'em',
				),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'em' => array(
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$this->get_sep_selector() => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $condition, array(
					"{$this->get_condition_prefix( 'popover_toggle' )}!" => '',
				) ),
				'conditions' => $conditions_popover,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'margin_x' ),
			array(
				'label' => __( 'Margin X', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'selectors' => array(
					$this->get_sep_selector() => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $condition, array(
					"{$this->get_condition_prefix( 'popover_toggle' )}!" => '',
				) ),
				'conditions' => $conditions_popover,
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'margin_y' ),
			array(
				'label' => __( 'Margin Y', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'selectors' => array(
					$this->get_sep_selector() => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $condition, array(
					"{$this->get_condition_prefix( 'popover_toggle' )}!" => '',
				) ),
				'conditions' => $conditions_popover,
			)
		);

		$this->parent->end_popover();
	}

	public function get_sep_selector() {
		return "{$this->args['selector']} > .item-sep";
	}

	private function get_settings( $key ) {
		$key = $this->get_prefix( $key );

		if ( $this->args['skin'] ) {
			return $this->args['skin']->get_instance_value( $key );
		}

		return $this->parent->get_settings_for_display( $key );
	}

	private function get_separator() {
		switch ( $this->get_settings( 'type' ) ) {
			case 'symbol':
				return $this->get_content();
			case 'icon':
				return Utils::get_render_icon( $this->get_settings( 'icon' ), array( 'aria-hidden' => 'true' ) );
		}
	}

	public function render() {
		echo $this->get_render();
	}

	public function get_content() {
		switch ( $this->get_settings( 'type' ) ) {
			case 'symbol':
				return $this->get_settings( 'content' );
		}
	}

	public function get_render() {
		if ( ! $this->render_cache ) {
			$separator = $this->get_separator();

			if ( empty( $separator ) ) {
				$separator = '';
			}

			$this->render_cache = '<span class="item-sep">';

			if ( 'icon' !== $this->get_settings( 'type' ) ) {
				$this->render_cache .= esc_html( $separator );
			} else {
				$this->render_cache .= $separator;
			}

			$this->render_cache .= '</span>';
		}

		return $this->render_cache;
	}
}
