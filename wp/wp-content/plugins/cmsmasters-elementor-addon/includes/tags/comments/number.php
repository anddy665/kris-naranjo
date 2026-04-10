<?php
namespace CmsmastersElementor\Tags\Comments;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Comments\Traits\Comments_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters number.
 *
 * Retrieves the number of comments for the current post.
 *
 * @since 1.0.0
 */
class Number extends Tag {

	use Base_Tag, Comments_Group;

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
		return 'number';
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
		return __( 'Number', 'cmsmasters-elementor' );
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
			'no_comments',
			array(
				'label' => __( 'No Comments Format', 'cmsmasters-elementor' ),
				'default' => __( 'No Comments', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'one_comments',
			array(
				'label' => __( 'One Comment Format', 'cmsmasters-elementor' ),
				'default' => __( 'One Comment', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'many_comments',
			array(
				'label' => __( 'Many Comment Format', 'cmsmasters-elementor' ),
				'default' => __( '{number} Comments', 'cmsmasters-elementor' ),
				'description' => sprintf( '<em>%s</em>', __( 'Where {number} is the number of comments.', 'cmsmasters-elementor' ) ),
			)
		);

		$this->add_control(
			'disabled_comments',
			array(
				'label' => __( 'Disabled Comment Format', 'cmsmasters-elementor' ),
				'default' => __( 'Comments are off for this post.', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'comments_link' => __( 'Comments Link', 'cmsmasters-elementor' ),
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
		$settings = $this->get_settings();

		$comments_number = get_comments_number();

		if ( comments_open() ) {
			if ( ! $comments_number ) {
				$count = $settings['no_comments'];
			} elseif ( '1' === $comments_number ) {
				$count = $settings['one_comments'];
			} else {
				$count = strtr( $settings['many_comments'], array(
					'{number}' => number_format_i18n( $comments_number ),
				) );
			}

			if ( 'comments_link' === $this->get_settings( 'link_to' ) ) {
				$count = sprintf( '<a href="%s">%s</a>', get_comments_link(), $count );
			}
		} else {
			$count = $settings['disabled_comments'];
		}

		if ( ! $count ) {
			echo '';
		}

		echo wp_kses_post( $count );
	}

}
