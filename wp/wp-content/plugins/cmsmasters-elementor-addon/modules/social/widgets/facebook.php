<?php
namespace CmsmastersElementor\Modules\Social\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Social\Classes\Facebook_SDK_Manager;
use CmsmastersElementor\Modules\Social\Traits\Social_Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elementor facebook widget.
 *
 * Elementor widget lets you easily embed and promote any public
 * facebook on your website.
 *
 * @since 1.0.0
 */
class Facebook extends Base_Widget {

	use Social_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve facebook widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Facebook', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve facebook widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-facebook';
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
		return array( 'facebook' );
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
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
	 * Register facebook widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize
	 * the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'embed_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'page' => __( 'Page', 'cmsmasters-elementor' ),
					'group' => __( 'Group', 'cmsmasters-elementor' ),
					'post' => __( 'Post', 'cmsmasters-elementor' ),
					'comments' => __( 'Comments', 'cmsmasters-elementor' ),
					'video' => __( 'Video', 'cmsmasters-elementor' ),
				),
				'default' => 'page',
			)
		);

		$this->add_control(
			'url_page',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.facebook.com/facebook',
				'placeholder' => __( 'https://www.facebook.com/page', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);

		$this->add_control(
			'url_group',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.facebook.com/groups/ayearofrunning/',
				'placeholder' => __( 'https://www.facebook.com/group', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'group',
				),
			)
		);

		$this->add_control(
			'url_post',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.facebook.com/20531316728/posts/10154009990506729/',
				'placeholder' => __( 'https://www.facebook.com/post', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'post',
				),
			)
		);

		$this->add_control(
			'url_comments',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.facebook.com/zuck/posts/10102577175875681?comment_id=1193531464007751&reply_comment_id=654912701278942',
				'placeholder' => __( 'https://www.facebook.com/comments', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'comments',
				),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'default' => 'https://www.facebook.com/facebook/videos/10153231379946729/',
				'dynamic' => array(
					'active' => true,
				),
				'label_block' => true,
				'condition' => array(
					'embed_type' => 'video',
				),
				'description' => __( 'Hover over the date next to the video, and copy its link address.', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'tabs_page',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => array(
					'timeline' => __( 'Timeline', 'cmsmasters-elementor' ),
					'events' => __( 'Events', 'cmsmasters-elementor' ),
					'messages' => __( 'Messages', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'timeline',
				),
				'multiple' => true,
				'label_block' => true,
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);

		$this->add_control(
			'small_header_page',
			array(
				'label' => __( 'Small Header', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);

		$this->add_control(
			'cover_photo_page',
			array(
				'label' => __( 'Cover Photo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);

		$this->add_control(
			'facepile_page',
			array(
				'label' => __( "Show Friend's Faces", 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Show profile photos when friends like this', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);

		$this->add_control(
			'social_context_group',
			array(
				'label' => __( 'Include Social Context', 'cmsmasters-elementor' ),
				'description' => __( 'Show the number of friends who are members of the group.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'embed_type' => 'group',
				),
			)
		);

		$this->add_control(
			'metadata_group',
			array(
				'label' => __( 'Include Metadata', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => array(
					'embed_type' => 'group',
				),
			)
		);

		$this->add_control(
			'show_text',
			array(
				'label' => __( 'Post Data', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'The setting applies to photo publications. When enabled not only image/video but post text, likes,comments and share buttons will be displayed.', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => array(
						'post',
						'video',
					),
				),
			)
		);

		$this->add_control(
			'video_allowfullscreen',
			array(
				'label' => __( 'Allow Full Screen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => array(
					'embed_type' => 'video',
				),
			)
		);

		$this->add_control(
			'video_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => array(
					'embed_type' => 'video',
				),
			)
		);

		$this->add_control(
			'video_show_captions',
			array(
				'label' => __( 'Captions', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Show captions if available (only on desktop).', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'video',
				),
			)
		);

		$this->add_control(
			'include_parent_comments',
			array(
				'label' => __( 'Include Parent Comment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'will include the parent comment (if relevant to a response comment).', 'cmsmasters-elementor' ),
				'default' => '',
				'condition' => array(
					'embed_type' => 'comments',
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-facebook__wrapper' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-facebook__wrapper .fb_iframe_widget' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'width_comments',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'range' => array(
					'px' => array(
						'min' => 220,
						'max' => 1200,
						'step' => 10,
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'embed_type' => 'comments',
				),
			)
		);

		$this->add_control(
			'width_post',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'description' => __( 'To use floating width, leave this field blank', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'range' => array(
					'px' => array(
						'min' => 350,
						'max' => 750,
						'step' => 10,
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'embed_type' => 'post',
				),
			)
		);

		$this->add_control(
			'width_page',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'range' => array(
					'px' => array(
						'min' => 180,
						'max' => 500,
						'step' => 10,
					),
				),
				'size_units' => array( 'px' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'embed_type',
							'operator' => '===',
							'value' => 'page',
						),
						array(
							'name' => 'embed_type',
							'operator' => '===',
							'value' => 'group',
						),
					),
				),
			)
		);

		$this->add_control(
			'height_page',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'range' => array(
					'px' => array(
						'min' => 70,
						'max' => 1200,
						'step' => 10,
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'embed_type' => 'page',
				),
			)
		);
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->get_settings();

		$widget_name = 'elementor-widget-' . $this->get_name();

		$this->add_render_attribute( 'wrapper', array(
			'class' => "{$widget_name}__wrapper",
			'style' => 'min-height: 1px',
		) );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		switch ( $settings['embed_type'] ) {
			case 'page':
				$this->get_page_html( $settings );
				break;

			case 'group':
				$this->get_group_html( $settings );
				break;

			case 'post':
				$this->get_post_html( $settings );
				break;

			case 'comments':
				$this->get_comments_html( $settings );
				break;

			case 'video':
				$this->get_video_html( $settings );
				break;
		}

		echo '</div>';
	}

	/**
	 * Render page facebook.
	 *
	 * Used to generate the page HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Page HTML.
	 */
	public function get_page_html( $settings ) {
		$attributes = array(
			'class' => 'fb-page',
			'data-href' => esc_url( $settings['url_page'] ),
			'data-tabs' => esc_attr( implode( ',', $settings['tabs_page'] ) ),
			'data-width' => esc_attr( $settings['width_page']['size'] ),
			'data-height' => esc_attr( $settings['height_page']['size'] ),
			'data-small-header' => $settings['small_header_page'] ? 'true' : 'false',
			'data-hide-cover' => $settings['cover_photo_page'] ? 'false' : 'true',
			'data-show-facepile' => $settings['facepile_page'] ? 'true' : 'false',
			'data-adapt-container-width' => 'true',
		);

		$this->add_render_attribute( 'page', $attributes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'page' ); ?> ></div>
		<?php
	}

	/**
	 * Render group facebook.
	 *
	 * Used to generate the group HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Group HTML.
	 */
	public function get_group_html( $settings ) {
		$attributes = array(
			'class' => 'fb-group',
			'data-href' => esc_url( $settings['url_group'] ),
			'data-width' => esc_attr( $settings['width_page']['size'] ),
			'data-show-social-context' => $settings['social_context_group'] ? 'true' : 'false',
			'data-show-metadata' => $settings['metadata_group'] ? 'true' : 'false',
		);

		$this->add_render_attribute( 'group', $attributes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'group' ); ?> ></div>
		<?php
	}

	/**
	 * Render post facebook.
	 *
	 * Used to generate the post HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Post HTML.
	 */
	public function get_post_html( $settings ) {
		$attributes = array(
			'class' => 'fb-post',
			'data-href' => esc_url( $settings['url_post'] ),
			'data-show-text' => ( 'yes' === $settings['show_text'] ? 'true' : 'false' ),
			'data-width' => esc_attr( $settings['width_post']['size'] ),
		);

		$this->add_render_attribute( 'post', $attributes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'post' ); ?> ></div>
		<?php
	}

	/**
	 * Render comments facebook.
	 *
	 * Used to generate the comments HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Comments HTML.
	 */
	public function get_comments_html( $settings ) {
		$attributes = array(
			'class' => 'fb-comment-embed',
			'data-href' => esc_url( $settings['url_comments'] ),
			'data-width' => esc_attr( $settings['width_comments']['size'] ),
			'data-include-parent' => $settings['include_parent_comments'] ? 'true' : 'false',
		);

		$this->add_render_attribute( 'comments', $attributes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'comments' ); ?> ></div>
		<?php
	}

	/**
	 * Render video facebook.
	 *
	 * Used to generate the video HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Video HTML.
	 */
	public function get_video_html( $settings ) {
		$attributes = array(
			'class' => 'fb-video',
			'data-href' => esc_url( $settings['video_url'] ),
			'data-show-text' => $settings['show_text'] ? 'true' : 'false',
			'data-allowfullscreen' => $settings['video_allowfullscreen'] ? 'true' : 'false',
			'data-autoplay' => $settings['video_autoplay'] ? 'true' : 'false',
			'data-show-captions' => $settings['video_show_captions'] ? 'true' : 'false',
		);

		$this->add_render_attribute( 'video', $attributes );

		?>
		<div <?php echo $this->get_render_attribute_string( 'video' ); ?> ></div>
		<?php
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
				'field' => 'url_page',
				'type' => esc_html__( 'URL Page', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_group',
				'type' => esc_html__( 'URL Group', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_post',
				'type' => esc_html__( 'URL Post', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_comments',
				'type' => esc_html__( 'URL Comments', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
