<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive trait.
 *
 * Allows to use a group of controls for archive.
 */
trait Archive {

	/**
	 * Group of controls for archive.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_archive( $key = '', $args = array() ) {
		list(
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'layout' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Layout', 'pediatric-clinic' ),
					'label_block' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'l-sidebar' => array(
							'title' => esc_html__( 'Left', 'pediatric-clinic' ),
							'description' => esc_html__( 'Left Sidebar', 'pediatric-clinic' ),
						),
						'fullwidth' => array(
							'title' => esc_html__( 'Full', 'pediatric-clinic' ),
							'description' => esc_html__( 'Full Width', 'pediatric-clinic' ),
						),
						'r-sidebar' => array(
							'title' => esc_html__( 'Right', 'pediatric-clinic' ),
							'description' => esc_html__( 'Right Sidebar', 'pediatric-clinic' ),
						),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'layout' ),
						'r-sidebar'
					),
					'toggle' => false,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'type' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Type', 'pediatric-clinic' ),
					'label_block' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'large' => esc_html__( 'Large', 'pediatric-clinic' ),
						'grid' => esc_html__( 'Grid', 'pediatric-clinic' ),
						'compact' => esc_html__( 'Compact', 'pediatric-clinic' ),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'type' ),
						'large'
					),
					'toggle' => false,
				)
			)
		);

		$default_large_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'type' ) => 'large' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'large_elements_heading_control' ),
			array_merge_recursive(
				$default_large_args,
				array(
					'label' => esc_html__( 'Elements Order', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'large_elements' ),
			array_merge_recursive(
				$default_large_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::SELECTIZE,
					'options' => array(
						'media' => esc_html__( 'Media', 'pediatric-clinic' ),
						'title' => esc_html__( 'Title', 'pediatric-clinic' ),
						'meta_first' => esc_html__( 'Meta Data 1', 'pediatric-clinic' ),
						'meta_second' => esc_html__( 'Meta Data 2', 'pediatric-clinic' ),
						'content' => esc_html__( 'Content', 'pediatric-clinic' ),
						'more' => esc_html__( 'Read More', 'pediatric-clinic' ),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'large_elements' ),
						array(
							'media',
							'title',
							'meta_first',
							'content',
							'meta_second',
							'more',
						)
					),
					'multiple' => true,
				)
			)
		);

		$default_grid_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'type' ) => 'grid' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'grid_style' ),
			array_merge_recursive(
				$default_grid_args,
				array(
					'label' => esc_html__( 'Style', 'pediatric-clinic' ),
					'label_block' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'fit-rows' => esc_html__( 'Fit Rows', 'pediatric-clinic' ),
						'masonry' => esc_html__( 'Masonry', 'pediatric-clinic' ),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'grid_style' ),
						'masonry'
					),
					'toggle' => false,
					'separator' => 'before',
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'grid_columns' ),
			array_merge_recursive(
				$default_grid_args,
				array(
					'label' => esc_html__( 'Columns', 'pediatric-clinic' ),
					'label_block' => true,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'1' => array(
							'title' => '1',
							'description' => esc_html__( 'One', 'pediatric-clinic' ),
						),
						'2' => array(
							'title' => '2',
							'description' => esc_html__( 'Two', 'pediatric-clinic' ),
						),
						'3' => array(
							'title' => '3',
							'description' => esc_html__( 'Three', 'pediatric-clinic' ),
						),
						'4' => array(
							'title' => '4',
							'description' => esc_html__( 'Four', 'pediatric-clinic' ),
						),
						'5' => array(
							'title' => '5',
							'description' => esc_html__( 'Five', 'pediatric-clinic' ),
						),
					),
					'desktop_default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'grid_columns' ),
						'4'
					),
					'tablet_default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'grid_columns_tablet' ),
						'2'
					),
					'mobile_default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'grid_columns_mobile' ),
						'1'
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'grid_columns' ) . ': {{VALUE}};',
					),
					'toggle' => true,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'grid_elements_heading_control' ),
			array_merge_recursive(
				$default_grid_args,
				array(
					'label' => esc_html__( 'Elements Order', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'grid_elements' ),
			array_merge_recursive(
				$default_grid_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::SELECTIZE,
					'options' => array(
						'media' => esc_html__( 'Media', 'pediatric-clinic' ),
						'title' => esc_html__( 'Title', 'pediatric-clinic' ),
						'meta_first' => esc_html__( 'Meta Data 1', 'pediatric-clinic' ),
						'meta_second' => esc_html__( 'Meta Data 2', 'pediatric-clinic' ),
						'content' => esc_html__( 'Content', 'pediatric-clinic' ),
						'more' => esc_html__( 'Read More', 'pediatric-clinic' ),
					),
					'multiple' => true,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'grid_elements' ),
						array(
							'media',
							'title',
							'meta_first',
							'content',
							'meta_second',
							'more',
						)
					),
				)
			)
		);

		$default_compact_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'type' ) => 'compact' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'media_heading_control' ),
			array_merge_recursive(
				$default_compact_args,
				array(
					'label' => esc_html__( 'Media', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'compact_media_visibility' ),
			array_merge_recursive(
				$default_compact_args,
				array(
					'label' => esc_html__( 'Visibility', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'pediatric-clinic' ),
					'label_on' => esc_html__( 'Show', 'pediatric-clinic' ),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'compact_media_visibility' ),
						'yes'
					),
				)
			)
		);

		$default_compact_media_args = array_merge_recursive(
			$default_compact_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'compact_media_visibility' ) => 'yes' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'compact_media_position' ),
			array_merge_recursive(
				$default_compact_media_args,
				array(
					'label' => esc_html__( 'Position', 'pediatric-clinic' ),
					'label_block' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'left' => array(
							'icon' => 'eicon-h-align-left',
							'title' => esc_html__( 'Left', 'pediatric-clinic' ),
						),
						'right' => array(
							'icon' => 'eicon-h-align-right',
							'title' => esc_html__( 'Right', 'pediatric-clinic' ),
						),
					),
					'toggle' => false,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'compact_media_position' ),
						'left'
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'compact_media_width' ),
			array_merge_recursive(
				$default_compact_media_args,
				array(
					'label' => esc_html__( 'Width', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range' => array(
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'compact_media_width' ) . ': {{SIZE}}%;',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'compact_vertical_alignment' ),
			array_merge_recursive(
				$default_compact_media_args,
				array(
					'label' => esc_html__( 'Vertical Alignment', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'flex-start' => array(
							'icon' => 'eicon-v-align-top',
							'title' => esc_html__( 'Top', 'pediatric-clinic' ),
						),
						'center' => array(
							'icon' => 'eicon-v-align-middle',
							'title' => esc_html__( 'Center', 'pediatric-clinic' ),
						),
						'flex-end' => array(
							'icon' => 'eicon-v-align-bottom',
							'title' => esc_html__( 'Bottom', 'pediatric-clinic' ),
						),
					),
					'toggle' => false,
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'compact_vertical_alignment' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'compact_elements_heading_control' ),
			array_merge_recursive(
				$default_compact_args,
				array(
					'label' => esc_html__( 'Elements Order', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'compact_elements' ),
			array_merge_recursive(
				$default_compact_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::SELECTIZE,
					'options' => array(
						'title' => esc_html__( 'Title', 'pediatric-clinic' ),
						'meta_first' => esc_html__( 'Meta Data 1', 'pediatric-clinic' ),
						'meta_second' => esc_html__( 'Meta Data 2', 'pediatric-clinic' ),
						'content' => esc_html__( 'Content', 'pediatric-clinic' ),
						'more' => esc_html__( 'Read More', 'pediatric-clinic' ),
					),
					'multiple' => true,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'compact_elements' ),
						array(
							'title',
							'meta_first',
							'content',
							'meta_second',
							'more',
						)
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
