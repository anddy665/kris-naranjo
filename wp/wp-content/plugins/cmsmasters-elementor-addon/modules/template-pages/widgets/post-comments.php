<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\TemplatePages\Widgets\Skins;
use CmsmastersElementor\Traits\Extendable_Widget;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Post Comments widget.
 *
 * Addon widget that displays comments of current post.
 *
 * @since 1.0.0
 */
class Post_Comments extends Base_Widget {

	use Singular_Widget;
	use Extendable_Widget;

	protected $_has_template_content = false; //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-comments';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Comments', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-post-comments';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'comments',
			'post',
			'response',
			'form',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-post-comments',
		);
	}

	/**
	 * Register widget skins.
	 *
	 * This method is activated while initializing the widget base class.
	 * It is used to assign skins to widgets with `add_skin()` method.
	 *
	 * @since 1.0.0
	 * @since 1.3.8 replacing `_register_skins()` with `register_skins()`.
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Post_Comments_Content_Template( $this ) );
		$this->add_skin( new Skins\Post_Comments_Custom( $this ) );
		$this->add_skin( new Skins\Post_Comments_Facebook( $this ) );

		if ( class_exists( 'Disqus' ) ) {
			$this->add_skin( new Skins\Post_Comments_Disqus( $this ) );
		}
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Comments', 'cmsmasters-elementor' ) )
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'custom_author_text_after',
				'type' => esc_html__( 'Author Text After', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_post_author',
				'type' => esc_html__( 'Post Author', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_date_time_separator_text',
				'type' => esc_html__( 'Time Separator', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_reply_text',
				'type' => esc_html__( 'Reply', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_comment_text',
				'type' => esc_html__( 'Comment', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_name_text',
				'type' => esc_html__( 'Name', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_email_text',
				'type' => esc_html__( 'Email', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_website_text',
				'type' => esc_html__( 'Website', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_submit_button_text',
				'type' => esc_html__( 'Submit Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_comment_title_text',
				'type' => esc_html__( 'Comments Text Only', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_comment_title_single_text',
				'type' => esc_html__( 'Comments Single Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_comment_title_multiple_text',
				'type' => esc_html__( 'Comments Multiple Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_leave_reply_text',
				'type' => esc_html__( 'Leave A Reply Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_leave_reply_to_text',
				'type' => esc_html__( 'Leave A Reply to Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_navigation_text_previous',
				'type' => esc_html__( 'Navigation Previous', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_navigation_text_next',
				'type' => esc_html__( 'Navigation Next', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
