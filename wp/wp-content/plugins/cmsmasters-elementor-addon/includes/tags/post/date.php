<?php
namespace CmsmastersElementor\Tags\Post;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Post\Traits\Post_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters date.
 *
 * Retrieve the date on which the post was written or changed.
 *
 * @since 1.0.0
 */
class Date extends Tag {

	use Base_Tag, Post_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'date';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Date', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'post_date_gmt' => __( 'Date Published', 'cmsmasters-elementor' ),
					'post_modified_gmt' => __( 'Date Modified', 'cmsmasters-elementor' ),
				),
				'default' => 'post_date_gmt',
			)
		);

		$this->add_control(
			'format',
			array(
				'label' => __( 'Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'F j, Y' => date( 'F j, Y' ),
					'Y-m-d' => date( 'Y-m-d' ),
					'm/d/Y' => date( 'm/d/Y' ),
					'd/m/Y' => date( 'd/m/Y' ),
					'm.d.Y' => date( 'm.d.Y' ),
					'd.m.Y' => date( 'd.m.Y' ),
					'human' => __( 'Human Readable', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'custom_format',
			array(
				'label' => __( 'Custom Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => sprintf( '<a href="%1$s" target="_blank">%2$s</a>.',
					'https://wordpress.org/support/article/formatting-date-and-time',
					__( 'Documentation on date and time formatting', 'cmsmasters-elementor' )
				),
				'condition' => array( 'format' => 'custom' ),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$type = $this->get_settings( 'type' );
		$format = $this->get_settings( 'format' );

		if ( 'human' === $format ) {
			/* translators: Post date dynamic tag human readable format text. %s: Date in human readable format */
			$post_date = sprintf( __( '%s ago', 'cmsmasters-elementor' ), human_time_diff( strtotime( get_post()->{$type} ) ) );
		} else {
			$get_the_date = sprintf( 'get_the_%s', 'post_modified_gmt' === $type ? 'modified_date' : 'date' );

			if ( 'custom' === $format ) {
				$format = $this->get_settings( 'custom_format' );
			}

			$post_date = $get_the_date( $format );
		}

		echo wp_kses_post( $post_date );
	}

}
