<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Slider trait.
 *
 * Allows to use a group of controls for slider.
 */
trait Slider {

	/**
	 * Group of controls for slider.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_slider( $key = '', $args = array() ) {
		list(
			$columns_available,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'columns_available' => true, // Enable columns controls
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		if ( $columns_available ) {
			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'slides_per_view' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Slides Per View', 'pediatric-clinic' ),
						'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 5,
						'step' => 1,
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'slides_per_view' ),
							4
						),
						'tablet_default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'slides_per_view_tablet' ),
							2
						),
						'mobile_default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'slides_per_view_mobile' ),
							1
						),
					)
				)
			);

			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'slides_to_scroll' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Slides to Scroll', 'pediatric-clinic' ),
						'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'pediatric-clinic' ) . '<br />' . esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 1,
						'max' => 5,
						'step' => 1,
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'slides_to_scroll' ),
							1
						),
						'condition' => array(
							$this->get_control_id_parameter( $key, 'slides_per_view!' ) => 1,
						),
					)
				)
			);

			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'space_between' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Space Between', 'pediatric-clinic' ),
						'description' => esc_html__( 'Distance between slides in px.', 'pediatric-clinic' ) . '<br />' . esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 100,
						'step' => 1,
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'space_between' ),
							0
						),
						'tablet_default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'space_between_tablet' ),
							0
						),
						'mobile_default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'space_between_mobile' ),
							0
						),
					)
				)
			);
		}

		$this->add_control(
			$this->get_control_name_parameter( $key, 'autoplay' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Autoplay', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'autoplay' ),
						'no'
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'autoplay_speed' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Autoplay Speed', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::NUMBER,
					'min' => 500,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'autoplay_speed' ),
						5000
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'autoplay' ) => 'yes',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'animation_speed' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Animation Speed', 'pediatric-clinic' ) . ' (ms)',
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::NUMBER,
					'step' => 100,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'animation_speed' ),
						500
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'pause_on_hover' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Pause On Hover', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'pause_on_hover' ),
						'no'
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'autoplay' ) => 'yes',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'autoplay_reverse' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Autoplay Reverse', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'render_type' => 'none',
					'frontend_available' => true,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'autoplay_reverse' ),
						'no'
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'autoplay' ) => 'yes',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'infinite' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Infinite Loop', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'infinite' ),
						'no'
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'mousewheel' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Mousewheel Control', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'mousewheel' ),
						'no'
					),
				)
			)
		);

		if ( $columns_available ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'centered_slides' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Centered Slides', 'pediatric-clinic' ),
						'description' => esc_html__( 'Turn on for a slider with an even number of slides only.', 'pediatric-clinic' ) . '<br />' . esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
						'type' => Controls_Manager::SWITCHER,
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'centered_slides' ),
							'no'
						),
						'condition' => array(
							$this->get_control_id_parameter( $key, 'slides_per_view!' ) => 1,
						),
					)
				)
			);
		}

		$this->add_control(
			$this->get_control_name_parameter( $key, 'free_mode' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Free Mode / No Fixed Positions', 'pediatric-clinic' ),
					'description' => esc_html__( 'If enable then slides will not have fixed positions.', 'pediatric-clinic' ) . '<br />' . esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'free_mode' ),
						'no'
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'arrows' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Arrows', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'pediatric-clinic' ),
					'label_on' => esc_html__( 'Show', 'pediatric-clinic' ),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'arrows' ),
						'no'
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'navigation' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Navigation', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'none' => esc_html__( 'None', 'pediatric-clinic' ),
						'bullets' => esc_html__( 'Bullets', 'pediatric-clinic' ),
						'progressbar' => esc_html__( 'Progress', 'pediatric-clinic' ),
						'fraction' => esc_html__( 'Fraction', 'pediatric-clinic' ),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'navigation' ),
						'bullets'
					),
				)
			)
		);
	}

}
