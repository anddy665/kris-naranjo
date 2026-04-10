<?php
namespace CmsmastersElementor\Tags\Action;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Action\Traits\Action_Group;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Controls_Manager as cmsmastersControls;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Base\Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Popup extends Tag {

	use Base_Tag, Action_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.9.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'popup';
	}

	/**
	* Get title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.9.0
	*
	* @return string Tag title.
	*/
	public function get_title() {
		return __( 'Popup', 'cmsmasters-elementor' );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.9.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	/**
	* Register advanced section controls.
	*
	* Registers the advanced section controls of the dynamic tag.
	*
	* Keep Empty to avoid default dynamic tag advanced section.
	*
	* @since 1.9.0
	*/
	protected function register_advanced_section() {}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.9.0
	*/
	public function register_controls() {
		$this->add_control(
			'popup_id',
			array(
				'label' => __( 'Choose Popup', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => cmsmastersControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => 'cmsmasters_popup',
							),
						),
					),
				),
				'frontend_available' => true,
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.9.0
	*/
	public function render() {
		$settings = $this->get_settings();

		echo "#cmsmasters-popup-{$settings['popup_id']}";
	}
}
