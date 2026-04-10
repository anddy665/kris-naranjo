<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Base;

use Elementor\Controls_Stack;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Base template locations rule class.
 *
 * @since 1.0.0
 */
abstract class Base_Rule extends Controls_Stack {

	/**
	 * @since 1.0.0
	 */
	protected $child_rules = array();

	/**
	 * @since 1.0.0
	 */
	public function get_unique_name() {
		return 'location_' . $this->get_name();
	}

	/**
	 * @since 1.0.0
	 */
	final public static function get_type() {
		return 'location';
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public static function get_group() {
		return 'general';
	}

	/**
	 * @since 1.0.0
	 */
	abstract public function get_title();

	/**
	 * @since 1.0.0
	 */
	public function get_multiple_title() {
		return $this->get_title();
	}

	/**
	 * @since 1.0.0
	 */
	public function __construct( array $data = array() ) {
		parent::__construct( $data );

		$this->register_child_rules();
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public static function get_priority() {
		return 100; // 100
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_args_priority() {
		return static::get_priority();
	}

	/**
	 * @since 1.0.0
	 */
	abstract public function verify_expression();

	/**
	 * @since 1.0.0
	 */
	public function register_child_rules() {}

	/**
	 * @since 1.0.0
	 */
	public function get_location_config() {
		$config = parent::get_config();

		$config['name'] = $this->get_name();
		$config['title'] = $this->get_title();
		$config['multiple_title'] = $this->get_multiple_title();
		$config['child_locations'] = $this->get_child_rules();

		unset( $config['tabs_controls'] );

		return $config;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_child_rules() {
		return $this->child_rules;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param self $rule
	 */
	public function register_child_rule( $rule ) {
		do_action( 'cmsmasters_elementor/documents/locations/register_child_rule', $rule );

		$this->child_rules[] = $rule->get_name();
	}

}
