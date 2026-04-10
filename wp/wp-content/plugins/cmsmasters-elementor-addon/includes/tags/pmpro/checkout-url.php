<?php
namespace CmsmastersElementor\Tags\PMPro;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\PMPro\Traits\PMPro_Group;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters url.
 *
 * Retrieves url field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class Checkout_URL extends Data_Tag {

	use Base_Tag, PMPro_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'checkout-url';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Checkout URL', 'cmsmasters-elementor' );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.0.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*/
	protected function register_controls() {
		$levels = pmpro_getAllLevels();
		$levels_options = array();

		if ( ! empty( $levels ) ) {
			foreach ( $levels as $level ) {
				$levels_options[ $level->id ] = $level->name;
			}
		}

		$this->add_control(
			'level',
			array(
				'label' => __( 'Level', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $levels_options,
			)
		);
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	*/
	public function get_value( array $options = array() ) {
		$level_id = $this->get_settings( 'level' );

		if ( ! empty( $level_id ) ) {
			return pmpro_url( 'checkout', '?level=' . $level_id );
		}

		return pmpro_url( 'checkout' );
	}

}
