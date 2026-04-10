<?php
namespace PediatricClinicSpace\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Excerpt handler class.
 *
 * For changing excerpt length and returning excerpt
 */
class Excerpt {

	/**
	 * Excerpt length.
	 */
	public $length = 55;

	/**
	 * Excerpt more text.
	 */
	public $more = '...';

	/**
	 * Constructor for Excerpt.
	 *
	 * Sets excerpt length and more text.
	 *
	 * @param number $length Excerpt length.
	 * @param string $more Excerpt more text.
	 */
	public function __construct( $length, $more ) {
		$this->length = $length;
		$this->more = $more;

		add_filter( 'excerpt_length', function() {
			return $this->length;
		} );

		add_filter( 'excerpt_more', function() {
			return $this->more;
		}, 20 );
	}

	/**
	 * Gets post excerpt.
	 *
	 * @return string Post excerpt.
	 */
	public function get_excerpt() {
		return get_the_excerpt();
	}

}
