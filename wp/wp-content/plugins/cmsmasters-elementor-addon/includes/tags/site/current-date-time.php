<?php
namespace CmsmastersElementor\Tags\Site;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Site\Traits\Site_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters current date time.
 *
 * Retrieve the current date time.
 *
 * @since 1.5.0
 */
class Current_Date_Time extends Tag {

	use Base_Tag, Site_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.5.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'current-date-time';
	}

	/**
	* Get group title.
	*
	* Returns the title of the dynamic tag group.
	*
	* @since 1.5.0
	*
	* @return string Group title.
	*/
	public static function group_title() {
		return '';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.5.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Current Date Time', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.5.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'date_format',
			array(
				'label' => __( 'Date Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'F j, Y' => gmdate( 'F j, Y' ),
					'Y-m-d' => gmdate( 'Y-m-d' ),
					'm/d/Y' => gmdate( 'm/d/Y' ),
					'd/m/Y' => gmdate( 'd/m/Y' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'time_format',
			array(
				'label' => __( 'Time Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'g:i a' => gmdate( 'g:i a' ),
					'g:i A' => gmdate( 'g:i A' ),
					'H:i' => gmdate( 'H:i' ),
				),
				'default' => 'default',
				'condition' => array(
					'date_format!' => 'custom',
				),
			)
		);

		$this->add_control(
			'custom_format',
			array(
				'label' => esc_html__( 'Custom Format', 'cmsmasters-elementor' ),
				'default' => get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'cmsmasters-elementor' ) ),
				'condition' => array(
					'date_format' => 'custom',
				),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.5.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$settings = $this->get_settings();

		if ( 'custom' === $settings['date_format'] ) {
			$format = $settings['custom_format'];
		} else {
			$date_format = $settings['date_format'];
			$time_format = $settings['time_format'];
			$format = '';

			if ( 'default' === $date_format ) {
				$date_format = get_option( 'date_format' );
			}

			if ( 'default' === $time_format ) {
				$time_format = get_option( 'time_format' );
			}

			if ( $date_format ) {
				$format = $date_format;
				$has_date = true;
			} else {
				$has_date = false;
			}

			if ( $time_format ) {
				if ( $has_date ) {
					$format .= ' ';
				}
				$format .= $time_format;
			}
		}

		$value = date_i18n( $format );

		echo wp_kses_post( $value );
	}

}
