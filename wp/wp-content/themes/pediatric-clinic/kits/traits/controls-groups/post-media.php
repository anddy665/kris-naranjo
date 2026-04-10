<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Post Media trait.
 *
 * Allows to use a group of controls for post media.
 */
trait Post_Media {

	/**
	 * Group of controls for post media.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_post_media( $key = '', $args = array() ) {
		list(
			$media_size_tabs,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'media_size_tabs' => true, // Media size controls in tabs
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		if ( $media_size_tabs ) {
			$this->start_controls_tabs(
				$this->get_control_name_parameter( $key, 'media_size_tabs' )
			);

			$this->start_controls_tab(
				$this->get_control_name_parameter( $key, 'image_tab' ),
				array( 'label' => esc_html__( 'Image', 'pediatric-clinic' ) )
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array_merge_recursive(
					$default_args,
					array(
						'name' => $this->get_control_name_parameter( $key, 'image' ),
						'separator' => 'none',
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'image_size' ),
							'large'
						),
					)
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'image_size_notice' ),
				array_merge_recursive(
					$default_args,
					array(
						'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-control-field-description',
						'render_type' => 'ui',
					)
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				$this->get_control_name_parameter( $key, 'gallery_tab' ),
				array( 'label' => esc_html__( 'Gallery', 'pediatric-clinic' ) )
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array_merge_recursive(
					$default_args,
					array(
						'name' => $this->get_control_name_parameter( $key, 'gallery_image' ),
						'separator' => 'none',
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'gallery_image_size' ),
							'large'
						),
					)
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'gallery_image_size_notice' ),
				array_merge_recursive(
					$default_args,
					array(
						'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-control-field-description',
						'render_type' => 'ui',
					)
				)
			);

			$this->add_controls_group(
				$this->get_control_name_parameter( $key, 'slider' ),
				Settings_Tab_Base::CONTROLS_SLIDER,
				array_merge_recursive(
					$default_args,
					array(
						'columns_available' => false,
					)
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				$this->get_control_name_parameter( $key, 'video_tab' ),
				array( 'label' => esc_html__( 'Video', 'pediatric-clinic' ) )
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'video_height' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Height', 'pediatric-clinic' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'%' => array(
								'min' => 0,
								'max' => 200,
							),
						),
						'size_units' => array( '%' ),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'video_height' ) . ': {{SIZE}}%;',
						),
					)
				)
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
		} else {
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array_merge_recursive(
					$default_args,
					array(
						'name' => $this->get_control_name_parameter( $key, 'image' ),
						'separator' => 'none',
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'image_size' ),
							'large'
						),
					)
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'image_size_notice' ),
				array_merge_recursive(
					$default_args,
					array(
						'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-control-field-description',
						'render_type' => 'ui',
					)
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array_merge_recursive(
					$default_args,
					array(
						'name' => $this->get_control_name_parameter( $key, 'gallery_image' ),
						'separator' => 'none',
						'fields_options' => array(
							'size' => array( 'label' => esc_html__( 'Gallery Image Size', 'pediatric-clinic' ) ),
						),
						'default' => $this->get_default_setting(
							$this->get_control_name_parameter( $key, 'gallery_image_size' ),
							'large'
						),
					)
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'gallery_image_size_notice' ),
				array_merge_recursive(
					$default_args,
					array(
						'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-control-field-description',
						'render_type' => 'ui',
					)
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, 'video_height' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Video Height', 'pediatric-clinic' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'%' => array(
								'min' => 0,
								'max' => 200,
							),
						),
						'size_units' => array( '%' ),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'video_height' ) . ': {{SIZE}}%;',
						),
					)
				)
			);
		}

		$this->add_control(
			$this->get_control_name_parameter( $key, 'box_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Container', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_controls_group(
			$this->get_control_name_parameter( $key, 'box' ),
			Settings_Tab_Base::CONTROLS_CONTAINER_BOX,
			array_merge_recursive(
				$default_args,
				array(
					'popover' => false,
					'excludes' => array(
						'alignment',
						'box_shadow',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'apply_settings' ),
			array_merge_recursive(
				$default_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'type' => Controls_Manager::BUTTON,
					'text' => esc_html__( 'Save & Reload', 'pediatric-clinic' ),
					'event' => 'cmsmasters:theme_settings:apply_settings',
					'separator' => 'before',
				)
			)
		);
	}

}
