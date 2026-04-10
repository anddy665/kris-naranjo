<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Utils;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager as ElementorControls;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Group_Control_Flex_Align extends Group_Control_Base {
	protected static $fields;

	public static function get_type() {
		return Controls_Manager::FLEX_ALIGN_GROUP;
	}

	protected function filter_fields() {
		$fields = parent::filter_fields();
		$args = $this->get_args();
		$positions_names = array(
			'vertical' => array(
				'ai_horizontal',
				'ai_vertical',
			),
			'horizontal' => array(
				'jc_horizontal',
				'jc_vertical',
			),
		);

		if ( ! empty( $args['exclude_property'] ) ) {
			foreach ( $positions_names as $position_name => $position_fields_name ) {
				$exclude_position = Utils::get_if_isset( $args['exclude_property'], $position_name );

				if ( ! $exclude_position ) {
					continue;
				}

				if ( ! is_array( $exclude_position ) ) {
					$exclude_position = array( $exclude_position );
				}

				foreach ( $position_fields_name as $field_name_jc ) {
					if ( ! isset( $fields[ $field_name_jc ] ) ) {
						continue;
					}

					$fields[ $field_name_jc ]['options'] = array_diff_key(
						$fields[ $field_name_jc ]['options'],
						array_fill_keys( $exclude_position, '' )
					);
				}
			}
		}

		return $fields;
	}

	protected function init_fields() {
		$fields = array();
		$label_horizontal = __( 'Horizontal Alignment', 'cmsmasters-elementor' );
		$label_vertical = __( 'Vertical Alignment', 'cmsmasters-elementor' );

		$justify_content = array(
			'type' => ElementorControls::CHOOSE,
			'label_block' => false,
			'responsive' => true,
			'toggle' => false,
			'selectors' => array(
				'{{SELECTOR}}' => 'justify-content: {{VALUE}}',
			),
		);
		$align_items = array(
			'type' => ElementorControls::CHOOSE,
			'label_block' => false,
			'toggle' => false,
			'responsive' => true,
			'selectors' => array(
				'{{SELECTOR}}' => 'align-items: {{VALUE}}; align-content: {{VALUE}};',
			),
		);

		$fields['position'] = array(
			'label' => __( 'Position', 'cmsmasters-elementor' ),
			'type' => ElementorControls::SELECT,
			'label_block' => false,
			'default' => '',
			'options' => array(
				'' => __( 'Horizontal', 'cmsmasters-elementor' ),
				'column' => __( 'Vertical', 'cmsmasters-elementor' ),
			),
			'selectors' => array(
				'{{SELECTOR}}' => 'flex-direction: {{VALUE}};',
			),
		);

		$fields['jc_horizontal'] = array_merge(
			$justify_content,
			array(
				'label' => $label_horizontal,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'space-between' => array(
						'title' => __( 'Space Between', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'condition' => array(
					'position' => '',
				),
			)
		);

		$fields['jc_vertical'] = array_merge(
			$justify_content,
			array(
				'label' => $label_vertical,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'space-between' => array(
						'title' => __( 'Space Between', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					),
				),
				'condition' => array(
					'position' => 'column',
				),
			)
		);

		$fields['ai_horizontal'] = array_merge(
			$align_items,
			array(
				'label' => $label_horizontal,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => ' eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'condition' => array(
					'position' => 'column',
				),
			)
		);

		$fields['ai_vertical'] = array_merge(
			$align_items,
			array(
				'label' => $label_vertical,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => ' eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'condition' => array(
					'position' => '',
				),
			)
		);

		return $fields;
	}

	protected function get_default_options() {
		return array(
			'popover' => false,
		);
	}
}
