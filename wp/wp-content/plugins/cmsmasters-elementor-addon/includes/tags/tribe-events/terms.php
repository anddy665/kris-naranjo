<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters terms.
 *
 * Retrieve a event terms as a list.
 *
 * @since 1.13.0
 */
class Terms extends Tag {

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
	public function get_name() {
		return 'event-terms';
	}

	/**
	* Get tag terms.
	*
	* Returns the terms of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag terms.
	*/
	public static function tag_title() {
		return __( 'Event Terms', 'cmsmasters-elementor' );
	}

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
		$fields = array();
		$filter_args = array(
			'show_in_nav_menus' => true,
			'object_type' => array( 'event' ),
		);

		$taxonomies = get_taxonomies( $filter_args, 'objects' );

		foreach ( $taxonomies as $taxonomy => $value ) {
			$fields[ $taxonomy ] = $value->label;
		}

		$this->add_control(
			'taxonomy',
			array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $fields,
				'default' => 'tribe_events_cat',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => ', ',
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
		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return;
		}

		$settings = $this->get_settings();

		$terms = get_the_term_list( get_the_ID(), $settings['taxonomy'], '', $settings['separator'] );

		if ( $terms ) {
			echo $terms;
		}
	}

}
