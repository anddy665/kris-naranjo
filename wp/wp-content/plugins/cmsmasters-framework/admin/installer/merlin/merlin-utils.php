<?php
namespace CmsmastersFramework\Admin\Installer\Merlin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Merlin_Utils handler class is responsible for different utility methods.
 *
 * @since 1.0.0
 */
class Merlin_Utils {

	/**
	 * Get server limits to increase for pre installation notice.
	 *
	 * @since 1.0.0
	 *
	 * @return string pre installation notice part.
	 */
	public static function get_server_limits_to_increase() {
		if ( ! function_exists( 'ini_get' ) ) {
			return array();
		}

		$recommended_limits = array(
			'max_execution_time' => '300',
			'max_input_time' => '300',
			'post_max_size' => '64M',
			'upload_max_filesize' => '64M',
			'memory_limit' => '256M',
		);

		$limits = array();

		foreach ( $recommended_limits as $key => $value ) {
			$ini_limit = ini_get( $key );
			$ini_limit = ( -1 == $ini_limit || 0 == $ini_limit ? $value : $ini_limit );

			if ( wp_convert_hr_to_bytes( $value ) > wp_convert_hr_to_bytes( $ini_limit ) ) {
				$limits[] = $key . ' ' . $value;
			}
		}

		return $limits;
	}

	/**
	 * Get php modules to include for pre installation notice.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Removed imagick from required php modules.
	 *
	 * @return string pre installation notice part.
	 */
	public static function get_php_modules_to_include() {
		$test_php_extensions = \WP_Site_Health::get_instance()->get_test_php_extensions();

		if ( 'good' === $test_php_extensions['status'] ) {
			return array();
		}

		$pattern = '/<\/span?[^>]+>\s(.*?)<\/li/';

		preg_match_all( $pattern, $test_php_extensions['description'], $matches );

		if ( ! is_array( $matches[1] ) || empty( $matches[1] ) ) {
			return array();
		}

		$result = array();

		foreach ( $matches[1] as $item ) {
			if ( false !== stripos( $item, 'imagick' ) ) {
				continue;
			}

			$result[] = $item;
		}

		return $result;
	}

	/**
	 * Return SVG markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 *     @type string $icon  Required SVG icon filename.
	 *     @type string $title Optional SVG title.
	 *     @type string $desc  Optional SVG description.
	 * }
	 *
	 * @return string SVG markup.
	 */
	public static function get_svg( $args = array() ) {
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return __( 'Please define default parameters in the form of an array.', 'cmsmasters-framework' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return __( 'Please define an SVG icon filename.', 'cmsmasters-framework' );
		}

		// Set defaults.
		$defaults = array(
			'icon' => '',
			'title' => '',
			'desc' => '',
			'aria_hidden' => true, // Hide from screen readers.
			'fallback' => false,
		);

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$aria_hidden = '';

		if ( true === $args['aria_hidden'] ) {
			$aria_hidden = ' aria-hidden="true"';
		}

		// Set ARIA.
		$aria_labelledby = '';

		if ( $args['title'] && $args['desc'] ) {
			$aria_labelledby = ' aria-labelledby="title desc"';
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon--' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img">';

		// If there is a title, display it.
		if ( $args['title'] ) {
			$svg .= '<title>' . esc_html( $args['title'] ) . '</title>';
		}

		// If there is a description, display it.
		if ( $args['desc'] ) {
			$svg .= '<desc>' . esc_html( $args['desc'] ) . '</desc>';
		}

		$svg .= '<use xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use>';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon--' . esc_attr( $args['icon'] ) . '"></span>';
		}

		$svg .= '</svg>';

		return wp_kses( $svg, array(
			'svg' => array(
				'class'       => array(),
				'aria-hidden' => array(),
				'role'        => array(),
			),
			'use' => array(
				'xlink:href' => array(),
			),
		) );
	}

	/**
	 * Loading merlin-spinner.
	 *
	 * @since 1.0.0
	 */
	public static function get_loading_spinner() {
		return '<span class="merlin__button--loading__spinner">
			<div class="merlin-spinner">
				<svg class="merlin-spinner__svg" viewbox="25 25 50 50">
					<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="6" stroke-miterlimit="10"></circle>
				</svg>
			</div>
		</span>';
	}

	/**
	 * Get notice HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Notice HTML.
	 * @param array $add_classes additional classes.
	 *
	 * @return string Notice HTML.
	 */
	public static function get_notice( $content = '', $add_classes = array(), $close_type = 'button' ) {
		if ( empty( $content ) ) {
			return '';
		}

		$add_class = array( 'cmsmasters-installer-notice' );

		$add_class = array_merge( $add_class, $add_classes );

		$out = '<div class="' . esc_attr( implode( ' ', $add_class ) ) . '">
			<div class="cmsmasters-installer-notice__outer">';

				if ( 'button' === $close_type ) {
					$out .= '<span class="cmsmasters-installer-notice__close cmsmasters-installer-notice__close-js"></span>';
				} elseif ( is_array( $close_type ) ) {
					$out .= '<a href="' . esc_url( $close_type['link'] ) . '" class="cmsmasters-installer-notice__close-link">' . esc_html( $close_type['text'] ) . '</a>';
				}

				$out .= '<div class="cmsmasters-installer-notice__inner">' .
					$content .
				'</div>
			</div>
		</div>';

		return $out;
	}

	/**
	 * Get notice list.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items List items.
	 * @param string $type grouped/separated type of list.
	 *
	 * @return array List HTML.
	 */
	public static function get_notice_list( $items = array(), $type = 'grouped' ) {
		if ( empty( $items ) ) {
			return '';
		}

		$out = '<ul class="cmsmasters-installer-notice__list cmsmasters-installer-notice__list-' . esc_attr( $type ) . '">';

			foreach ( $items as $item ) {
				$out .= '<li>' . esc_html( $item ) . '</li>';
			}

		$out .= '</ul>';

		return $out;
	}

	/**
	 * Get notice title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text.
	 *
	 * @return string Notice title.
	 */
	public static function get_notice_title( $text = '' ) {
		if ( empty( $text ) ) {
			return '';
		}

		return '<p class="cmsmasters-installer-notice__title">' . wp_kses_post( $text ) . '</p>';
	}

	/**
	 * Get notice img.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src Image src.
	 *
	 * @return string Notice img.
	 */
	public static function get_notice_img( $src = '' ) {
		if ( empty( $src ) ) {
			return '';
		}

		return '<div class="cmsmasters-installer-notice__img">
			<img src="' . esc_url( $src ) . '" />
		</div>';
	}

	/**
	 * Get notice info.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text.
	 *
	 * @return string Notice info.
	 */
	public static function get_notice_info( $text = '' ) {
		if ( empty( $text ) ) {
			return '';
		}

		return '<div class="cmsmasters-installer-notice__info">
			<p>' . wp_kses_post( $text ) . '</p>
		</div>';
	}

	/**
	 * Get notice text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text.
	 *
	 * @return string Notice text.
	 */
	public static function get_notice_text( $text = '' ) {
		if ( empty( $text ) ) {
			return '';
		}

		return '<div class="cmsmasters-installer-notice__text">
			<p>' . wp_kses_post( $text ) . '</p>
		</div>';
	}

	/**
	 * Get notice button.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string Notice button.
	 */
	public static function get_notice_button( $atts = array() ) {
		$req_vars = array(
			'tag' => 'button', // button/a tag
			'text' => '',
			'link' => '',
			'target' => '_blank',
			'add_classes' => array(),
			'add_attrs' => array(),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( empty( $text ) ) {
			return '';
		}

		$button_attrs = array(
			'class' => esc_attr( implode( ' ', array_merge( array( 'cmsmasters-installer-notice__button' ), $add_classes ) ) ),
		);

		if ( 'a' === $tag && ! empty( $link ) ) {
			$button_attrs['href'] = esc_url( $link );
			$button_attrs['target'] = esc_attr( $target );
		}

		$button_attrs_out = '';

		foreach ( $button_attrs as $button_attr_key => $button_attr_value ) {
			$button_attrs_out .= $button_attr_key . '="' . esc_attr( $button_attr_value ) . '"';
		}

		if ( ! empty( $add_attrs ) ) {
			$button_attrs_out .= implode( ' ', $add_attrs );
		}

		return '<div class="cmsmasters-installer-notice__button-wrap">
			<' . $tag . ' ' . $button_attrs_out . '>
				<span>' . esc_html( $text ) . '</span>
			</' . $tag . '>
		</div>';
	}

}
