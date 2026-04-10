<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Dropzone extends Base_Field {

	public function get_name() {
		return Fields_Manager::DROPZONE;
	}

	public function get_field() {
		ob_start();

		$input_attributes = array(
			'class' => 'box__file',
			'accept' => $this->parameters['accept'],
		);

		$multiple = Utils::get_if_isset( $this->parameters, 'multiple', false );
		$plural_number = (int) $multiple;

		if ( $multiple ) {
			$input_attributes['multiple'] = true;
		}

		?>
		<div class="elementor-field elementor-field-dropzone">
			<div class="box__input">
				<div class="elementor--dropzone--upload__icon">
					<i class="eicon-library-upload"></i>
				</div>
				<?php
				/** @var WordpressModule $wordpress_module */
				$wordpress_module = WordpressModule::instance();
				$postmeta = $wordpress_module->get_post_meta_manager();

				$postmeta->open_fields_box( 'dropzone' );

				$postmeta->add_field(
					$this->parameters['id'],
					Fields_Manager::INPUT,
					array(
						'type' => 'file',
						'output' => 'raw',
						'attributes' => $input_attributes,
					)
				);

				echo $postmeta->render_metabox_fields( 'dropzone' );

				$title_text = esc_html( _n( 'Drop file here to upload', 'Drop files here to upload', ++$plural_number, 'cmsmasters-elementor' ) );
				echo Utils::generate_html_tag( 'h4', array(), $title_text );
				echo Utils::generate_html_tag( 'span', array( 'class' => 'description' ), esc_html__( 'or', 'cmsmasters-elementor' ) );

				$button_text = esc_html( _n( 'Select file', 'Select files', $plural_number, 'cmsmasters-elementor' ) );
				echo Utils::generate_html_tag( 'div',
					array( 'class' => 'elementor-button elementor--dropzone--upload__browse' ),
					Utils::generate_html_tag( 'span', array(), $button_text )
				);

				if ( ! empty( $this->parameters['description'] ) ) {
					echo Utils::generate_html_tag( 'p', array( 'class' => 'description' ), wp_kses_post( $this->parameters['description'] ) );
				}
				?>
			</div>
			<div class="box__uploading"><?php esc_html_e( 'Uploading&hellip;', 'cmsmasters-elementor' ); ?></div>
			<div class="box__success"><?php esc_html_e( 'Done!', 'cmsmasters-elementor' ); ?></div>
			<div class="box__error"><?php esc_html_e( 'Error!', 'cmsmasters-elementor' ); ?></div>
		</div>
		<?php

		return ob_get_clean();
	}

}
