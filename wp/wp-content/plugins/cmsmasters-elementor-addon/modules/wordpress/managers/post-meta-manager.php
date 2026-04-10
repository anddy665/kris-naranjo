<?php
namespace CmsmastersElementor\Modules\Wordpress\Managers;

use CmsmastersElementor\Base\Base_Actions;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Utils;

use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Meta_Manager extends Base_Actions {

	private $module;

	/**
	 * Meta fields manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Fields_Manager Fields manager.
	 */
	private $fields_manager;

	private $metabox = array();

	private $metabox_fields = array();

	private $active_metabox = null;

	private $previously_active_boxes = array();

	public static function get_name() {
		return 'post-meta-manager';
	}

	public function __construct( $parameters = null ) {
		/** @var WordpressModule $wordpress_module */
		$wordpress_module = WordpressModule::instance();

		$this->fields_manager = $wordpress_module->get_fields_manager();

		if ( ! $parameters ) {
			return;
		}

		$this->module = $parameters;
		$this->metabox = $this->module->meta_box;

		parent::__construct();
	}

	public function render() {
		if ( ! $this->metabox['name'] || ! $this->metabox['label'] ) {
			return;
		}

		$this->add_meta_box();
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->metabox['name'],
			$this->metabox['label'],
			array( $this, 'render_metabox' ),
			$this->module->get_cpt(),
			Utils::get_if_isset( $this->metabox, 'context', 'normal' ),
			Utils::get_if_isset( $this->metabox, 'priority', 'default' )
		);
	}

	public function render_metabox() {
		$metabox_content = $this->render_metabox_fields( $this->metabox['name'] );

		printf( '<div class="elementor-metabox-content">%s</div>', $metabox_content );
	}

	/**
	 * Render stack fields.
	 *
	 * Generate the final HTML for all the registered stack fields.
	 *
	 * @since 1.0.0
	 */
	public function render_metabox_fields( $name ) {
		$this->maybe_close_active_fields_box( $name );

		$fields = $this->get_clean_metabox_fields( $name );

		if ( ! $fields || empty( $fields ) ) {
			return;
		}

		return implode( "\n", $fields );
	}

	public function maybe_close_active_fields_box( $name ) {
		if ( $name === $this->active_metabox ) {
			$this->close_active_fields_box();
		}
	}

	public function close_active_fields_box() {
		$this->active_metabox = array_pop( $this->previously_active_boxes );
	}

	public function get_clean_metabox_fields( $name ) {
		$fields = $this->get_metabox_fields( $name );

		$this->clear_metabox_fields( $name );

		return $fields;
	}

	public function get_metabox_fields( $name ) {
		if ( ! isset( $this->metabox_fields[ $name ] ) ) {
			return false;
		}

		return $this->metabox_fields[ $name ];
	}

	private function clear_metabox_fields( $name ) {
		if ( ! isset( $this->metabox_fields[ $name ] ) ) {
			return;
		}

		unset( $this->metabox_fields[ $name ] );
	}

	public function open_fields_box( $name ) {
		$this->maybe_save_active_fields_box();

		$this->active_metabox = $name;

		if ( ! isset( $this->metabox_fields[ $this->active_metabox ] ) ) {
			$this->metabox_fields[ $this->active_metabox ] = array();
		}
	}

	private function maybe_save_active_fields_box() {
		if ( $this->active_metabox ) {
			$this->save_active_fields_box();
		}
	}

	private function save_active_fields_box() {
		$this->previously_active_boxes[] = $this->active_metabox;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public function add_field( $id, $type, $attributes = array(), $value = '' ) {
		if ( ! $this->fields_manager->is_type_supported( $type ) ) {
			return;
		}

		$this->ensure_active_metabox();

		$attributes['id'] = $id;
		$attributes['field_type'] = $type;

		if ( ! $value ) {
			$value = Utils::get_if_isset( $attributes, 'value' );
		}

		$field = $this->fields_manager->get_field( $attributes, $value );

		if ( 'raw' !== $this->fields_manager->get_field_output_type() ) {
			$field = $this->get_field_row( $attributes, $field );
		}

		$this->metabox_fields[ $this->active_metabox ][ $id ] = $field;
	}

	public function ensure_active_metabox() {
		if ( ! $this->active_metabox ) {
			$this->active_metabox = ElementorUtils::generate_random_string();
		}
	}

	public function get_field_row( $attributes, $field ) {
		$parameters = "id=\"{$attributes['id']}\"";
		$classes = array(
			'elementor-field',
			"elementor-field-{$attributes['field_type']}",
			$attributes['id'],
		);

		if ( isset( $attributes['wrapper'] ) && is_array( $attributes['wrapper'] ) ) {
			$parameters_array = array();

			foreach ( $attributes['wrapper'] as $key => $value ) {
				if ( 'id' === $key ) {
					continue;
				}

				if ( 'class' === $key ) {
					$classes[] = $value;
				}

				$parameters_array[] = sprintf( "{$key}=\"%s\"", esc_attr( $value ) );
			}

			$parameters = sprintf( ' %s', implode( ' ', $parameters_array ) );
		}

		$parameters = sprintf( ' class="%s"', implode( ' ', $classes ) );

		$label = $this->get_field_label( $attributes );
		$description = Utils::get_if_isset( $attributes, 'description' );
		$content = "{$label}{$field}{$description}";

		return Utils::generate_html_tag( 'div', $parameters, $content );
	}

	public function get_field_label( $attributes ) {
		if ( ! isset( $attributes['label'] ) || ! $attributes['label'] ) {
			return '';
		}

		$id = ( 'file' === $attributes['field_type'] ) ? "{$attributes['id']}[url]" : $attributes['id'];
		$label = Utils::generate_html_tag( 'label', array( 'for' => esc_attr( $id ) ), $attributes['label'] );

		return Utils::generate_html_tag( 'p', array( 'class' => 'elementor-field-label' ), $label );
	}

	public static function sanitize_field_recursive( $data ) {
		if ( ! is_array( $data ) ) {
			return sanitize_text_field( $data );
		}

		foreach ( $data as $key => $value ) {
			$data[ $key ] = self::sanitize_field_recursive( $value );
		}

		return $data;
	}

}
