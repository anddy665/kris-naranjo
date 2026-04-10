<?php
namespace PediatricClinicSpace\Kits\Settings\Single;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Single More Posts settings.
 */
class More_Posts extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'single_more_posts';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'More Posts', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		$toggle_name = self::get_toggle_name();

		return parent::get_control_id_prefix() . '_single';
	}

	/**
	 * Get toggle conditions.
	 *
	 * Retrieve the settings toggle conditions.
	 *
	 * @return array Toggle conditions.
	 */
	protected function get_toggle_conditions() {
		return array(
			'condition' => array(
				$this->get_control_id_parameter( '', 'blocks' ) => 'more_posts',
			),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_control(
			'more_posts_title_text',
			array(
				'label' => esc_html__( 'Title', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'More Posts', 'pediatric-clinic' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'more_posts_image',
				'separator' => 'none',
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'more_posts_image_size' ),
					'large'
				),
			)
		);

		$this->add_control(
			'more_posts_image_size_notice',
			array(
				'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-field-description',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'more_posts_order',
			array(
				'label' => esc_html__( 'Order by', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'recent' => esc_html__( 'Recent', 'pediatric-clinic' ),
					'category' => esc_html__( 'Related by Categories', 'pediatric-clinic' ),
					'post_tag' => esc_html__( 'Related by Tags', 'pediatric-clinic' ),
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'more_posts_order' ),
					'recent'
				),
			)
		);

		$this->add_control(
			'more_posts_count',
			array(
				'label' => esc_html__( 'Count', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'more_posts_count' ),
					8
				),
			)
		);

		$this->add_controls_group( 'more_posts_slider', self::CONTROLS_SLIDER, array(
			'columns_available' => true,
		) );

		$this->add_controls_group( 'more_posts_box', self::CONTROLS_CONTAINER_BOX, array(
			'excludes' => array(
				'alignment',
				'bg_color',
				'box_shadow',
			),
		) );

		$this->add_control(
			'more_posts_apply_settings',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::BUTTON,
				'text' => esc_html__( 'Save & Reload', 'pediatric-clinic' ),
				'event' => 'cmsmasters:theme_settings:apply_settings',
				'separator' => 'before',
			)
		);
	}

}
