<?php
namespace CmsmastersElementor\Tags\Site;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Site\Traits\Site_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters user info.
 *
 * Retrieves the current user information.
 *
 * @since 1.0.0
 */
class User_Info extends Tag {

	use Base_Tag, Site_Group;

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
		return 'user-info';
	}

	/**
	* Get group title.
	*
	* Returns the title of the dynamic tag group.
	*
	* @since 1.0.0
	*
	* @return string Group title.
	*/
	public static function group_title() {
		return '';
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
		return __( 'User Info', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'type',
			array(
				'label' => __( 'Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Select', 'cmsmasters-elementor' ),
					'id' => __( 'ID', 'cmsmasters-elementor' ),
					'display_name' => __( 'Display Name', 'cmsmasters-elementor' ),
					'login' => __( 'Username', 'cmsmasters-elementor' ),
					'first_name' => __( 'First Name', 'cmsmasters-elementor' ),
					'last_name' => __( 'Last Name', 'cmsmasters-elementor' ),
					'description' => __( 'Bio', 'cmsmasters-elementor' ),
					'email' => __( 'Email', 'cmsmasters-elementor' ),
					'url' => __( 'Website', 'cmsmasters-elementor' ),
					'meta' => __( 'User Meta', 'cmsmasters-elementor' ),
				),
				'default' => 'login',
			)
		);

		$this->add_control(
			'user_meta',
			array(
				'label' => __( 'Meta Key', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'type' => 'meta',
				),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$user = wp_get_current_user();
		$type = $this->get_settings( 'type' );

		if ( empty( $type ) || ! is_user_logged_in() ) {
			return;
		}

		$info = '';

		switch ( $type ) {
			case 'login':
			case 'email':
			case 'url':
				$field = 'user_' . $type;

				if ( isset( $user->$field ) ) {
					$info = $user->$field;
				}

				break;
			case 'id':
			case 'description':
			case 'first_name':
			case 'last_name':
			case 'display_name':
				if ( isset( $user->$type ) ) {
					$info = $user->$type;
				}

				break;
			case 'meta':
				$user_meta = $this->get_settings( 'user_meta' );

				if ( ! empty( $user_meta ) ) {
					$info = get_user_meta( $user->ID, $user_meta, true );
				}

				break;
		}

		echo wp_kses_post( $info );
	}

}
