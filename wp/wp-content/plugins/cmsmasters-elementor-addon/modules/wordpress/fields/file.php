<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class File extends Base_Field {

	public function get_name() {
		return Fields_Manager::FILE;
	}

	public function get_field() {
		$parameters = $this->parameters;
		$value = $this->value;

		if ( ! isset( $value['id'] ) || ! isset( $value['url'] ) ) {
			$value = array(
				'id' => '',
				'url' => '',
			);
		}

		$output = '<ul></ul>';

		/** @var WordpressModule $wordpress_module */
		$wordpress_module = WordpressModule::instance();
		$postmeta = $wordpress_module->get_post_meta_manager();

		$postmeta->open_fields_box( 'file_field' );

		$postmeta->add_field(
			"{$parameters['id']}[id]",
			Fields_Manager::INPUT,
			array(
				'type' => 'hidden',
				'value' => $value['id'],
				'output' => 'raw',
			)
		);

		$postmeta->add_field(
			"{$parameters['id']}[url]",
			Fields_Manager::INPUT,
			array(
				'type' => 'text',
				'value' => $value['url'],
				'output' => 'raw',
				'attributes' => array(
					'class' => 'elementor-field-input',
					'placeholder' => $parameters['description'],
				),
			)
		);

		$postmeta->add_field(
			$parameters['id'],
			Fields_Manager::INPUT,
			array(
				'type' => 'button',
				'value' => '',
				'output' => 'raw',
				'attributes' => array(
					'class' => 'button elementor-button elementor-upload-btn',
					'data-mime_type' => Utils::get_if_isset( $parameters, 'mime' ),
					'data-ext' => Utils::get_if_isset( $parameters, 'ext' ),
					'data-box_title' => Utils::get_if_isset( $parameters, 'box_title' ),
					'data-box_action' => Utils::get_if_isset( $parameters, 'box_action' ),
					'data-preview_anchor' => Utils::get_if_isset( $parameters, 'preview_anchor', 'none' ),
					'data-upload_text' => __( 'Upload', 'cmsmasters-elementor' ),
					'data-remove_text' => __( 'Delete', 'cmsmasters-elementor' ),
				),
			)
		);

		$output .= $postmeta->render_metabox_fields( 'file_field' );

		return $output;
	}

}
