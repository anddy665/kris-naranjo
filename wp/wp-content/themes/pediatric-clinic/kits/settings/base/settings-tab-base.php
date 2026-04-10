<?php
namespace PediatricClinicSpace\Kits\Settings\Base;

use PediatricClinicSpace\Kits\Traits\Base as KitBaseTraits;

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


abstract class Settings_Tab_Base extends Tab_Base {

	use KitBaseTraits\Controls_Group,
		KitBaseTraits\Var_Group_Control;

	/**
	 * Var group controls types.
	 */
	const VAR_BACKGROUND = 'background';
	const VAR_BORDER = 'border';
	const VAR_TYPOGRAPHY = 'typography';
	const VAR_BOX_SHADOW = 'box_shadow';
	const VAR_TEXT_SHADOW = 'text_shadow';

	/**
	 * Controls groups types.
	 */
	const CONTROLS_ARCHIVE = 'archive';
	const CONTROLS_BUTTON_ICON = 'button_icon';
	const CONTROLS_BUTTONS = 'buttons';
	const CONTROLS_CONTAINER = 'container';
	const CONTROLS_CONTAINER_BOX = 'container_box';
	const CONTROLS_CONTENT = 'content';
	const CONTROLS_CUSTOM_HTML = 'custom_html';
	const CONTROLS_ICON = 'icon';
	const CONTROLS_NAV = 'nav';
	const CONTROLS_NAV_BURGER_CONTAINER = 'nav_burger_container';
	const CONTROLS_NAV_DROPDOWN_CONTAINER = 'nav_dropdown_container';
	const CONTROLS_NAV_DROPDOWN_ITEM = 'nav_dropdown_item';
	const CONTROLS_NAV_ITEM = 'nav_item';
	const CONTROLS_NAV_TITLE_ITEM = 'nav_title_item';
	const CONTROLS_POST_CONTAINER = 'post_container';
	const CONTROLS_POST_CONTENT = 'post_content';
	const CONTROLS_POST_MEDIA = 'post_media';
	const CONTROLS_POST_META = 'post_meta';
	const CONTROLS_POST_TITLE = 'post_title';
	const CONTROLS_QUOTES = 'quotes';
	const CONTROLS_SHORT_INFO = 'short_info';
	const CONTROLS_SLIDER = 'slider';
	const CONTROLS_SOCIAL_ICONS = 'social_icons';
	const CONTROLS_STATES = 'states';

	/**
	 * Toggle group.
	 *
	 * @var string
	 */
	protected $group;

	/**
	 * Get ID.
	 *
	 * Retrieve the kit settings tab ID.
	 *
	 * @return string Tab ID.
	 */
	public function get_id() {
		return $this->group;
	}

	/**
	 * Get toggle name.
	 *
	 * Retrieve the settings toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return '';
	}

	/**
	 * Get control ID.
	 *
	 * Retrieve the control ID.
	 *
	 * @param string $control_base_id Control base ID.
	 *
	 * @return string Control ID.
	 */
	protected function get_control_id( $control_base_id ) {
		return static::get_control_id_prefix() . "_{$control_base_id}";
	}

	/**
	 * Get toggle name.
	 *
	 * Retrieve the settings toggle name.
	 */
	protected static function get_control_id_prefix() {
		return 'cmsmasters';
	}

	/**
	 * Get toggle conditions.
	 *
	 * Retrieve the settings toggle conditions.
	 *
	 * @return array Toggle conditions.
	 */
	protected function get_toggle_conditions() {
		return array();
	}

	/**
	 * Constructor.
	 *
	 * Initializing the base class by setting parent stack.
	 *
	 * @param Controls_Stack $parent
	 * @param string $group
	 */
	public function __construct( $parent, $group ) {
		parent::__construct( $parent );

		$this->group = $group;
	}

	/**
	 * Register tab controls.
	 *
	 * Registers the controls of the kit settings tab.
	 */
	protected function register_tab_controls() {
		$group = str_replace( '-', '_', $this->get_id() );
		$name = "{$group}_" . static::get_toggle_name();
		$conditions = $this->get_toggle_conditions();

		$args = array(
			'label' => $this->get_title(),
			'tab' => $this->get_id(),
		);

		if ( ! empty( $conditions ) ) {
			$args[ key( $conditions ) ] = current( $conditions );
		}

		$this->start_controls_section( $name, $args, true );

		$this->add_default_globals_notice();

		$this->register_toggle_controls();

		$this->end_controls_section();
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	abstract protected function register_toggle_controls();

	/**
	 * Get CSS var prefix.
	 *
	 * Retrieve control CSS variable prefix.
	 *
	 * @param mixed $custom_prefix Custom CSS variable prefix.
	 *
	 * @return string CSS variable prefix.
	 */
	public function get_css_var_prefix( $custom_prefix = false ) {
		$default_prefix = static::get_control_id_prefix();

		$prefix = ( $custom_prefix ) ? $custom_prefix : $default_prefix;

		return str_replace( '_', '-', $prefix );
	}

	/**
	 * Get default setting.
	 *
	 * Retrieve default setting value.
	 *
	 * @param string $key Setting key.
	 * @param mixed $default Default value.
	 *
	 * @return string Default setting.
	 */
	public function get_default_setting( $key, $default = false ) {
		$settings = get_option( 'cmsmasters_pediatric-clinic_default_kits', array() );
		$control_id = $this->get_control_id( $key );

		if ( ! isset( $settings[ $control_id ] ) ) {
			return $default;
		}

		return $settings[ $control_id ];
	}

	/**
	 * Get control parameters.
	 *
	 * Get control name and css variables prefix.
	 *
	 * @param string $key Control key.
	 * @param string $name Control name.
	 * @param string $suffix Control group variables suffix.
	 *
	 * @return array Control name and CSS variables prefix.
	 */
	protected function get_control_parameters( $key = '', $name = '', $suffix = '' ) {
		$prefix = static::get_control_id_prefix();
		$prefix .= ( '' !== $key ? "-{$key}" : '' );
		$prefix .= ( '' !== $suffix ? "-{$suffix}" : '' );
		$prefix = str_replace( '_', '-', $prefix );

		return array(
			$this->get_control_name_parameter( $key, $name ),
			$prefix,
		);
	}

	/**
	 * Get control name parameter.
	 *
	 * Get control name.
	 *
	 * @param string $key Control key.
	 * @param string $name Control name.
	 *
	 * @return array Control name.
	 */
	protected function get_control_name_parameter( $key = '', $name = '' ) {
		$control_key = ( '' !== $name ) ? $this->get_control_key_parameter( $key ) : $key;

		return $control_key . $name;
	}

	/**
	 * Get control key parameter.
	 *
	 * Get control key.
	 *
	 * @param string $key Control key.
	 *
	 * @return array Control key.
	 */
	protected function get_control_key_parameter( $key = '' ) {
		if ( '' === $key ) {
			return $key;
		}

		return "{$key}_";
	}

	/**
	 * Get control prefix parameter.
	 *
	 * Get control CSS variables prefix.
	 *
	 * @param string $key Control key.
	 * @param string $suffix Control CSS variables suffix.
	 *
	 * @return array Control CSS variables prefix.
	 */
	protected function get_control_prefix_parameter( $key = '', $suffix = '' ) {
		$control_prefix = static::get_control_id_prefix();

		if ( '' !== $control_prefix ) {
			$control_prefix .= '-';
		}

		$control_key = ( '' !== $suffix ) ? $this->get_control_key_parameter( $key ) : $key;

		$control_prefix .= $control_key . $suffix;

		return str_replace( '_', '-', $control_prefix );
	}

	/**
	 * Get control ID parameter.
	 *
	 * Get control ID.
	 *
	 * @param string $key Control key.
	 * @param string $name Control name.
	 *
	 * @return array Control ID.
	 */
	protected function get_control_id_parameter( $key = '', $name = '' ) {
		$control_name = $this->get_control_name_parameter( $key, $name );

		return $this->get_control_id( $control_name );
	}

	/**
	 * Get controls group required args.
	 *
	 * Get controls group required arguments.
	 *
	 * @param array $group_args Defined group arguments.
	 * @param array $required_args Required group arguments.
	 *
	 * @return array Controls group required arguments.
	 */
	protected function get_controls_group_required_args( $group_args = array(), $required_args = array() ) {
		$args = array();

		if ( empty( $required_args ) ) {
			return $args;
		}

		foreach ( $required_args as $variable_name => $default_value ) {
			if ( array_key_exists( $variable_name, $group_args ) ) {
				array_push( $args, $group_args[ $variable_name ] );
			} else {
				array_push( $args, $default_value );
			}
		}

		return $args;
	}

	/**
	 * Start controls section.
	 *
	 * Used to add a new section of controls to the stack.
	 *
	 * @param string $id Section ID.
	 * @param array $args Section arguments.
	 * @param bool $custom_id Skip `get_control_id()` method if `true`.
	 */
	public function start_controls_section( $id, $args = array(), $custom_id = false ) {
		$section_id = ( ! $custom_id ) ? $this->get_control_id( $id ) : $id;

		$this->parent->start_controls_section( $section_id, $args );
	}

	/**
	 * Start controls tabs.
	 *
	 * Used to add a new set of tabs inside a section.
	 *
	 * @param string $tabs_id Tabs ID.
	 * @param array $args Tabs arguments.
	 */
	public function start_controls_tabs( $tabs_id, array $args = array() ) {
		$this->parent->start_controls_tabs( $this->get_control_id( $tabs_id ), $args );
	}

	/**
	 * Start popover.
	 *
	 * Used to add a new set of controls in a popover.
	 */
	public function start_popover() {
		$this->parent->start_popover();
	}

	/**
	 * End popover.
	 *
	 * Used to close an existing open popover.
	 */
	public function end_popover() {
		$this->parent->end_popover();
	}

	/**
	 * Start injection.
	 *
	 * Used to inject controls and sections to a specific position in the stack.
	 *
	 * @param array $position The position where to start the injection.
	 */
	public function start_injection( array $position ) {
		$this->parent->start_injection( $position );
	}

	/**
	 * End injection.
	 *
	 * Used to close an existing opened injection point.
	 */
	public function end_injection() {
		$this->parent->end_injection();
	}

}
