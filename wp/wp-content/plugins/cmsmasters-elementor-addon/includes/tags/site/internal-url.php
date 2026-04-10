<?php
namespace CmsmastersElementor\Tags\Site;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Controls_Manager as ControlsManager;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Tags\Site\Traits\Site_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters internal url.
 *
 * Retrieves the permalink for an attachment,
 * taxonomy term archive or url to the author page.
 *
 * @since 1.0.0
 */
class Internal_URL extends Data_Tag {

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
		return 'internal-url';
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
		return __( 'Internal URL', 'cmsmasters-elementor' );
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

	public function get_panel_template() {
		return ' ({{ url }})';
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
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'post' => __( 'Content', 'cmsmasters-elementor' ),
					'taxonomy' => __( 'Taxonomy', 'cmsmasters-elementor' ),
					'attachment' => __( 'Media', 'cmsmasters-elementor' ),
					'author' => __( 'Author', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'post_id',
			array(
				'label' => __( 'Search & Select', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => ControlsManager::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => 'any' ),
					'display' => 'detailed',
				),
				'condition' => array( 'type' => 'post' ),
			)
		);

		$this->add_control(
			'taxonomy_id',
			array(
				'label' => __( 'Search & Select', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => ControlsManager::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::TAX_OBJECT,
					'display' => 'detailed',
				),
				'condition' => array( 'type' => 'taxonomy' ),
			)
		);

		$this->add_control(
			'attachment_id',
			array(
				'label' => __( 'Search & Select', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => ControlsManager::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::MEDIA_OBJECT,
					'display' => 'detailed',
				),
				'condition' => array( 'type' => 'attachment' ),
			)
		);

		$this->add_control(
			'author_id',
			array(
				'label' => __( 'Search & Select', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => ControlsManager::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::AUTHOR_OBJECT,
					'display' => 'detailed',
				),
				'condition' => array( 'type' => 'author' ),
			)
		);
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	*
	* @return array Tag value.
	*/
	public function get_value( array $options = array() ) {
		$settings = $this->get_settings();

		$url = '';

		switch ( $settings['type'] ) {
			case 'post':
				if ( ! empty( $settings['post_id'] ) ) {
					$url = get_permalink( (int) $settings['post_id'] );
				}

				break;
			case 'taxonomy':
				if ( ! empty( $settings['taxonomy_id'] ) ) {
					$taxonomy_id = (int) $settings['taxonomy_id'];

					$term = get_term( $taxonomy_id );

					if ( $term && ! is_wp_error( $term ) ) {
						$url = get_term_link( $taxonomy_id );
					}
				}

				break;
			case 'attachment':
				if ( ! empty( $settings['attachment_id'] ) ) {
					$url = get_attachment_link( (int) $settings['attachment_id'] );
				}

				break;
			case 'author':
				if ( ! empty( $settings['author_id'] ) ) {
					$url = get_author_posts_url( (int) $settings['author_id'] );
				}

				break;
		}

		return $url;
	}

}
