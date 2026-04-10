<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Widgets\Post_Title;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Event title widget.
 *
 * Addon widget that displays title of current Event.
 *
 * @since 1.13.0
 */
class Event_Title extends Post_Title {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Title', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-title';
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'title' => 'cmsmasters-tribe-events-event-title',
			'link' => 'cmsmasters-tribe-events-event-url',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'title_link_switcher',
			array(
				'options' => array(
					'no' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'yes' => array(
						'title' => __( 'Event', 'cmsmasters-elementor' ),
						'description' => __( 'Open Event', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
						'description' => __( 'Custom URL', 'cmsmasters-elementor' ),
					),
				),
			)
		);

		$this->update_control(
			'title_link_hover',
			array(
				'condition' => array( 'title_link_switcher!' => 'no' ),
			)
		);

		$this->update_control(
			'title_color_hover',
			array(
				'condition' => array( 'title_link_switcher!' => 'no' ),
			)
		);

		$this->update_control(
			'title_text_shadow_hover_text_shadow_type',
			array(
				'condition' => array( 'title_link_switcher!' => 'no' ),
			)
		);

		$this->update_control(
			'title_box_shadow_hover_box_shadow_type',
			array(
				'condition' => array( 'title_link_switcher!' => 'no' ),
			)
		);

		$this->update_control(
			'title_transition',
			array(
				'condition' => array( 'title_link_switcher!' => 'no' ),
			)
		);
	}
}
