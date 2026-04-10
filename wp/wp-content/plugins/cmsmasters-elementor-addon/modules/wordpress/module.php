<?php
namespace CmsmastersElementor\Modules\Wordpress;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Wordpress\Managers;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Module extends Base_Module {

	const MODULE_NAMESPACE = __NAMESPACE__;

	public function get_name() {
		return 'wordpress'; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
	}

	public function __construct() {
		$this->add_component( 'query', new Managers\Query_Manager() );

		$this->add_component( 'post_types', new Managers\Post_Types_Manager() );

		$this->add_component( 'fields', new Managers\Fields_Manager() );

		$post_meta_class = sprintf(
			'%1$s\Managers\%2$s',
			self::MODULE_NAMESPACE,
			Utils::generate_class_name( Managers\Post_Meta_Manager::get_name() )
		);

		$this->add_feature( 'post_meta', $post_meta_class );

		parent::__construct();
	}

	/**
	 * Get query manager.
	 *
	 * Retrieve the \Wp_Query manager module component.
	 *
	 * @return Managers\Query_Manager
	 */
	public function get_query_manager() {
		return $this->get_component( 'query' );
	}

	/**
	 * Get post types manager.
	 *
	 * Retrieve the post types manager module component.
	 *
	 * @return Managers\Post_Types_Manager
	 */
	public function get_post_types_manager() {
		return $this->get_component( 'post_types' );
	}

	/**
	 * Get fields manager.
	 *
	 * Retrieve the fields manager module component.
	 *
	 * @return Managers\Fields_Manager
	 */
	public function get_fields_manager() {
		return $this->get_component( 'fields' );
	}

	/**
	 * Get post meta manager.
	 *
	 * Retrieve the post meta manager module feature.
	 *
	 * @param mixed $parameters Module feature parameters.
	 *
	 * @return Managers\Post_Meta_Manager
	 */
	public function get_post_meta_manager( $parameters = null ) {
		return $this->get_feature( 'post_meta', $parameters );
	}

}
