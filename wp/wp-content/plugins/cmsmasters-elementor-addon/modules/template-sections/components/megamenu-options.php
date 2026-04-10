<?php
namespace CmsmastersElementor\Modules\TemplateSections\Components;

use CmsmastersElementor\Utils;
use Elementor\Plugin as Elementor_Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Mega Menu Options.
 *
 * The mega menu options handler class is responsible for managing mega menu.
 *
 * @since 1.11.0
 */
class Megamenu_Options {

	/**
	 * Item fields.
	 *
	 * @since 1.11.0
	 *
	 * @var array Item fields.
	 */
	protected $item_fields = array();


	/**
	 * Class constructor.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {
		$this->set_item_fields();

		$this->init_filters();

		$this->init_actions();
	}

	/**
	 * Set item fields.
	 *
	 * @since 1.11.0
	 */
	protected function set_item_fields() {
		$this->item_fields = array(
			'status' => array(
				'type' => 'radio_buttons',
				'label' => esc_html__( 'Mega Menu', 'cmsmasters-elementor' ),
				'label_title' => true,
				'choices' => array(
					'disable' => esc_html__( 'Disable', 'cmsmasters-elementor' ),
					'enable' => esc_html__( 'Enable', 'cmsmasters-elementor' ),
				),
				'default' => 'disable',
			),
			'type' => array(
				'type' => 'radio_buttons',
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'choices' => array(
					'wp-menu' => esc_html__( 'WP Menu', 'cmsmasters-elementor' ),
					'template' => esc_html__( 'Elementor Template', 'cmsmasters-elementor' ),
				),
				'default' => 'wp-menu',
			),
			'template' => array(
				'type' => 'template',
				'label' => esc_html__( 'Template', 'cmsmasters-elementor' ),
				'default' => '',
			),
			'inner_type' => array(
				'type' => 'hidden',
				'default' => 'standard',
			),
		);
	}

	/**
	 * Init filters.
	 *
	 * @since 1.11.0
	 */
	protected function init_filters() {
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'filter_nav_menu_item' ) );
	}

	/**
	 * Init actions.
	 *
	 * @since 1.11.0
	 */
	protected function init_actions() {
		add_action( 'current_screen', array( $this, 'init_new_template_actions' ) );

		add_action( 'wp_ajax_cmsmasters_megamenu_options_get_templates_list', array( $this, 'get_templates_list' ) );

		add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 3 );

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'nav_menu_item_custom_fields' ), 10, 5 );
	}

	/**
	 * Init new template actions.
	 *
	 * @since 1.11.0
	 */
	public function init_new_template_actions() {
		if (
			! function_exists( 'get_current_screen' ) ||
			'nav-menus' !== get_current_screen()->base
		) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		add_action( 'admin_footer', array( $this, 'print_elementor_editor_frame_template' ) );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.11.0
	 */
	public function enqueue_assets() {
		$is_test_mode = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS;
		$maybe_min = ( ! $is_test_mode ) ? '.min' : '';

		wp_enqueue_style(
			'select2',
			ELEMENTOR_ASSETS_URL . "lib/e-select2/css/e-select2{$maybe_min}.css",
			array(),
			'4.0.6-rc.1'
		);

		wp_enqueue_script(
			'select2',
			ELEMENTOR_ASSETS_URL . "lib/e-select2/js/e-select2.full{$maybe_min}.js",
			array( 'jquery' ),
			'4.0.6-rc.1',
			false
		);
	}

	/**
	 * Print elementor editor frame template.
	 *
	 * @since 1.11.0
	 * @since 1.11.10 Added megamenu templates containers.
	 */
	public function print_elementor_editor_frame_template() {
		$url = Elementor_Plugin::$instance->documents->get_create_new_post_url( 'elementor_library', 'container' );

		echo '<div class="cmsmasters-megamenu-new-template-area">
			<span class="cmsmasters-megamenu-new-template-area__close"></span>
			<div class="cmsmasters-megamenu-new-template-area__outer">
				<iframe id="cmsmasters-megamenu-new-template-iframe" src="" frameborder="0"></iframe>
				<input id="cmsmasters-megamenu-new-template-url" type="hidden" value="' . $url . '" />
			</div>
		</div>';
	}

	/**
	 * Get templates list.
	 *
	 * @since 1.11.0
	 * @since 1.11.10 Added megamenu templates containers.
	 */
	public function get_templates_list() {
		$out = array();

		$templates_container = Utils::get_templates_options( 'container' );

		foreach ( $templates_container as $id => $title ) {
			$out[] = array(
				'id' => $id,
				'text' => $title,
			);
		}

		$templates_section = Utils::get_templates_options( 'section' );

		foreach ( $templates_section as $id => $title ) {
			$out[] = array(
				'id' => $id,
				'text' => $title,
			);
		}

		echo json_encode( $out );

		die;
	}

	/**
	 * Filters a navigation menu item object.
	 *
	 * @since 1.11.0
	 *
	 * @param object $menu_item The menu item object.
	 *
	 * @return object Filtered menu item.
	 */
	public function filter_nav_menu_item( $menu_item ) {
		$meta_data = get_post_meta( $menu_item->ID, '_cmsmasters_megamenu', true );

		$menu_item->cmsmasters_megamenu = $this->parse_field_meta_data( $meta_data );

		return $menu_item;
	}

	/**
	 * Update a navigation menu item.
	 *
	 * @since 1.11.0
	 *
	 * @param int $menu_id ID of the updated menu.
	 * @param int $menu_item_db_id ID of the updated menu item.
	 * @param array $args An array of arguments used to update a menu item.
	 */
	public function update_nav_menu_item( $menu_id, $menu_item_db_id, $args ) {
		$meta_data = array();

		if ( isset( $_POST['cmsmasters-megamenu'][ $menu_item_db_id ] ) ) {
			$meta_data = $_POST['cmsmasters-megamenu'][ $menu_item_db_id ];
		}

		$meta_data = $this->parse_field_meta_data( $meta_data );

		update_post_meta( $menu_item_db_id, '_cmsmasters_megamenu', $meta_data );
	}

	/**
	 * Parses the field meta data according to the whitelist.
	 *
	 * @since 1.11.0
	 *
	 * @param array $data Field data.
	 *
	 * @return array Parsed field data.
	 */
	protected function parse_field_meta_data( $data = array() ) {
		$out = array();

		foreach ( $this->item_fields as $key => $args ) {
			if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
				$out[ $key ] = $data[ $key ];
			} else {
				$out[ $key ] = $args['default'];
			}
		}

		return $out;
	}

	/**
	 * Add custom fields for a nav menu item in the menu editor.
	 *
	 * @since 1.11.0
	 *
	 * @param int $item_id Menu item ID.
	 * @param WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of menu item arguments.
	 * @param int $id Nav menu ID.
	 */
	public function nav_menu_item_custom_fields( $item_id, $item, $depth, $args, $id ) {
		echo '<div class="cmsmasters-megamenu-fields">' .
			'<div class="cmsmasters-megamenu-fields__inner">';

		$megamenu_status = empty( $item->cmsmasters_megamenu[ 'status' ] ) ? 'disable' : $item->cmsmasters_megamenu[ 'status' ];
		$megamenu_type = empty( $item->cmsmasters_megamenu[ 'type' ] ) ? 'wp-menu' : $item->cmsmasters_megamenu[ 'type' ];

		foreach ( $this->item_fields as $field_key => $field_args ) {
			$field_classes = array(
				'cmsmasters-megamenu-field',
				'field-cmsmasters-megamenu-' . $field_key,
			);

			if ( 'disable' === $megamenu_status && 'status' !== $field_key ) {
				$field_classes[] = 'cmsmasters-hide';
			}

			if ( 'wp-menu' === $megamenu_type && 'template' === $field_key ) {
				$field_classes[] = 'cmsmasters-hide';
			}

			if ( 'hidden' === $field_args['type'] ) {
				$field_classes[] = 'cmsmasters-hide';
			}
			
			if ( ! empty( $field_args['label_block'] ) ) {
				$field_classes[] = 'cmsmasters-label-block';
			}

			if ( ! empty( $field_args['label_title'] ) ) {
				$field_classes[] = 'cmsmasters-label-title';
			}

			$field_classes = array_unique( $field_classes );

			echo '<div class="' . esc_attr( implode( ' ', $field_classes ) ) . '">' .
				( ! empty( $field_args['label'] ) ? '<span class="cmsmasters-megamenu-field__label">' . $field_args['label'] . '</span>' : '' ) .
				'<div class="cmsmasters-megamenu-field__content">' .
					call_user_func(
						array( $this, 'render_' . $field_args['type'] . '_field' ),
						array(
							'item_id' => $item_id,
							'item_value' => $item->cmsmasters_megamenu[ $field_key ],
							'field_key' => $field_key,
							'field_args' => $field_args,
						)
					) .
				'</div>' .
			'</div>';
		}

			echo '</div>' .
		'</div>';
	}

	/**
	 * Render radio_buttons field.
	 *
	 * @since 1.11.0
	 *
	 * @param array $attrs Attributes.
	 *
	 * @return string Rendered field.
	 */
	protected function render_radio_buttons_field( $attrs = array() ) {
		$attrs = wp_parse_args( $attrs, array(
			'item_id' => '',
			'item_value' => '',
			'field_key' => '',
			'field_args' => array(),
		) );

		foreach ( $attrs as $attr_key => $attr_value ) {
			$$attr_key = $attr_value;
		}

		$out = '<div class="cmsmasters-megamenu-field__radio-buttons">';

		foreach ( $field_args['choices'] as $choice_key => $choice_label ) {
			$attr_id = 'edit-cmsmasters-megamenu-' . $field_key . '-' . $item_id . '-' . $choice_key;

			$out .= '<span>' .
				'<input ' .
					'type="radio" ' .
					'id="' . esc_attr( $attr_id ) . '" ' .
					'class="cmsmasters-megamenu-' . $field_key . '" ' .
					'value="' . $choice_key . '" ' .
					'name="cmsmasters-megamenu[' . $item_id . '][' . $field_key . ']" ' .
					checked( $item_value, $choice_key, false ) .
				' />' .
				'<label for="' . esc_attr( $attr_id ) . '">' . $choice_label . '</label>' .
			'</span>';
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * Render template field.
	 *
	 * @since 1.11.0
	 * @since 1.11.10 Added megamenu templates containers.
	 *
	 * @param array $attrs Attributes.
	 *
	 * @return string Rendered field.
	 */
	protected function render_template_field( $attrs = array() ) {
		$attrs = wp_parse_args( $attrs, array(
			'item_id' => '',
			'item_value' => '',
			'field_key' => '',
			'field_args' => array(),
		) );

		foreach ( $attrs as $attr_key => $attr_value ) {
			$$attr_key = $attr_value;
		}

		$out = '';
		$options = '';

		$templates_container = Utils::get_templates_options( 'container' );

		if ( is_array( $templates_container ) && ! empty( $templates_container ) ) {
			foreach ( $templates_container as $template_id => $template_name ) {
				$options .= '<option value="' . esc_attr( $template_id ) . '"' . selected( $item_value, $template_id, false ) . '>' . esc_html( $template_name ) . '</option>';
			}
		}
		
		$templates_section = Utils::get_templates_options( 'section' );

		if ( is_array( $templates_section ) && ! empty( $templates_section ) ) {
			foreach ( $templates_section as $template_id => $template_name ) {
				$options .= '<option value="' . esc_attr( $template_id ) . '"' . selected( $item_value, $template_id, false ) . '>' . esc_html( $template_name ) . '</option>';
			}
		}

		$out .= '<select ' .
			'id="edit-cmsmasters-megamenu-' . $field_key . '-' . $item_id . '" ' .
			'class="cmsmasters-megamenu-' . $field_key . '" ' .
			'name="cmsmasters-megamenu[' . $item_id . '][' . $field_key . ']" ' .
		'>' .
			'<option value=""' . selected( $item_value, '', false ) . '>' . esc_html__( 'Choose Template', 'cmsmasters-elementor' ) . '</option>' .
			$options .
		'</select>';

		$out .= '<a href="#" class="cmsmasters-megamenu-create-template-button">' . esc_html__( 'Create Template', 'cmsmasters-elementor' ) . '</a>';

		return $out;
	}

	/**
	 * Render hidden field.
	 *
	 * @since 1.11.0
	 *
	 * @param array $attrs Attributes.
	 *
	 * @return string Rendered field.
	 */
	protected function render_hidden_field( $attrs = array() ) {
		$attrs = wp_parse_args( $attrs, array(
			'item_id' => '',
			'item_value' => '',
			'field_key' => '',
			'field_args' => array(),
		) );

		foreach ( $attrs as $attr_key => $attr_value ) {
			$$attr_key = $attr_value;
		}

		$out = '<input ' .
			'type="hidden" ' .
			'id="edit-cmsmasters-megamenu-' . $field_key . '-' . $item_id . '" ' .
			'name="cmsmasters-megamenu[' . $item_id . '][' . $field_key . ']" ' .
			'class="cmsmasters-megamenu-' . $field_key . '" ' .
			'value="' . $item_value . '" ' .
		' />';

		return $out;
	}

}
