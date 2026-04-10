<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters event dates.
 *
 * Retrieve the event dates.
 *
 * @since 1.13.0
 */
class Event_Dates extends Tag {

	use Base_Tag, Tribe_Events_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'event-dates';
	}

	/**
	* Get tag event dates.
	*
	* Returns the event dates of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag event dates.
	*/
	public static function tag_title() {
		return __( 'Event Dates', 'cmsmasters-elementor' );
	}

	/**
	* Register advanced section controls.
	*
	* Registers the advanced section controls of the dynamic tag.
	*
	* Keep Empty to avoid default dynamic tag advanced section.
	*
	* @since 1.13.0
	*/
	protected function register_advanced_section() {}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'posts_in',
			array(
				'label' => __( 'Search & select entries to show.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array(
						'post_type' => array( 'tribe_events' ),
					),
					'display' => 'detailed',
				),
				'export' => false,
			)
		);

		$this->add_control(
			'posts_dates',
			array(
				'label' => __( 'Dates', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'start' => __( 'Start', 'cmsmasters-elementor' ),
					'end' => __( 'End', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'end',
				'condition' => array( 'posts_in!' => '' ),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$settings = $this->get_settings();

		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return;
		}

		$event_id = ( isset( $settings['posts_in'] ) && '' !== $settings['posts_in'] ? $settings['posts_in'] : $event_data->ID );

		$event_start_date = get_post_meta( $event_id, '_EventStartDate', true );
		$event_end_date = get_post_meta( $event_id, '_EventEndDate', true );

		$select_date = ( isset( $settings['posts_dates'] ) && 'start' === $settings['posts_dates'] ? $event_start_date : $event_end_date );

		if ( $select_date ) {
			$timestamp = strtotime( $select_date );
			$gmt_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			$formatted_date = gmdate( 'Y-m-d H:i', $timestamp + $gmt_offset );

			echo wp_kses_post( $formatted_date );
		}
	}

}
