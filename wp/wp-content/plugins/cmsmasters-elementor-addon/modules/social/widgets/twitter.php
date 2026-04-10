<?php
namespace CmsmastersElementor\Modules\Social\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Social\Traits\Social_Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Scheme_Color;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elementor twitter widget.
 *
 * Elementor widget lets you easily embed and promote any public
 * twitter on your website.
 *
 * @since 1.0.0
 */
class Twitter extends Base_Widget {

	use Social_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve twitter widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'X Twitter', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve twitter widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-x-twitter';
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
		return array( 'twitter' );
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
	 * Register twitter widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize
	 * the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Added default height for tweeter timeline.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'embed_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'collection' => __( 'Collection', 'cmsmasters-elementor' ),
					'tweet' => __( 'Tweet', 'cmsmasters-elementor' ),
					'profile' => __( 'Profile', 'cmsmasters-elementor' ),
					'list' => __( 'List', 'cmsmasters-elementor' ),
					'moments' => __( 'Moments', 'cmsmasters-elementor' ),
					'likes' => __( 'Likes ', 'cmsmasters-elementor' ),
				),
				'default' => 'tweet',
			)
		);

		$this->add_control(
			'url_collection',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/TwitterDev/timelines/539487832448843776',
				'placeholder' => __( 'https://twitter.com/collection', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'collection',
				),
			)
		);

		$this->add_control(
			'url_tweet',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/Interior/status/463440424141459456',
				'placeholder' => __( 'https://twitter.com/tweet', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'tweet',
				),
			)
		);

		$this->add_control(
			'url_profile',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/TwitterDev',
				'placeholder' => __( 'https://twitter.com/profile', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'profile',
				),
			)
		);

		$this->add_control(
			'url_list',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/TwitterDev',
				'placeholder' => __( 'https://twitter.com/list', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'list',
				),
			)
		);

		$this->add_control(
			'url_moments',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/i/moments/625792726546558977',
				'placeholder' => __( 'https://twitter.com/moments', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'moments',
				),
			)
		);

		$this->add_control(
			'url_likes',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://twitter.com/TwitterDev/likes',
				'placeholder' => __( 'https://twitter.com/likes', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'likes',
				),
			)
		);

		$this->add_control(
			'expanded_tweet',
			array(
				'label' => __( 'Expanded', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'embed_type' => 'tweet',
				),
			)
		);

		$this->add_control(
			'alignment_tweet',
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
				'default' => 'center',
				'condition' => array(
					'embed_type' => 'tweet',
				),
			)
		);

		$this->add_control(
			'replies',
			array(
				'label' => __( 'Show Replies', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Show Tweets in response to another Tweet or account', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => array(
						'collection',
						'profile',
						'list',
						'likes',
					),
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => array(
					'noheader' => __( 'No Header', 'cmsmasters-elementor' ),
					'nofooter' => __( 'No Footer', 'cmsmasters-elementor' ),
					'noborders' => __( 'No Borders', 'cmsmasters-elementor' ),
					'noscrollbar' => __( 'No Scroll Bar', 'cmsmasters-elementor' ),
					'transparent' => __( 'Transparent', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'condition' => array(
					'embed_type' => array(
						'collection',
						'profile',
						'list',
						'likes',
					),
				),
			)
		);

		$this->add_control(
			'theme',
			array(
				'label' => __( 'Theme', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'light' => __( 'Light', 'cmsmasters-elementor' ),
					'dark' => __( 'Dark', 'cmsmasters-elementor' ),
				),
				'default' => 'light',
				'condition' => array(
					'embed_type!' => 'moments',
				),
			)
		);

		$this->add_control(
			'height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '400',
				),
				'range' => array(
					'px' => array(
						'min' => 250,
						'max' => 1300,
						'step' => 10,
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'embed_type' => array( 'collection', 'profile', 'list', 'likes' ),
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'embed_type' => array(
						'collection',
						'profile',
						'list',
						'likes',
					),
					'layout!' => 'noborders',
				),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label' => __( 'Tweets Limit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => 'Range: 1-20',
				'separator' => 'before',
				'condition' => array(
					'embed_type!' => 'tweet',
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
		$settings = $this->get_settings_for_display();

		$widget_name = 'elementor-widget-' . $this->get_name();

		$this->add_render_attribute( 'wrapper', array(
			'class' => "{$widget_name}__wrapper",
			'style' => 'min-height: 1px',
		) );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		switch ( $settings['embed_type'] ) {
			case 'collection':
			case 'profile':
			case 'list':
			case 'likes':
				$this->get_timeline_html( $settings );
				break;

			case 'tweet':
				$this->get_tweet_html( $settings );
				break;

			case 'moments':
				$this->get_moments_html( $settings );
				break;
		}

		echo '</div>';
	}

	/**
	 * Render timeline twitter.
	 *
	 * Used to generate the timeline HTML.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed the display of the number of tweets without limits.
	 *
	 * @return string Timeline HTML.
	 */
	public function get_timeline_html( $settings ) {
		$data_chrome = '';

		if ( isset( $settings['layout'] ) && ! empty( $settings['layout'] ) ) {
			$data_chrome = implode( ' ', $settings['layout'] );
		}

		switch ( $settings['embed_type'] ) {
			case 'collection':
				$url_timeline = $settings['url_collection'];
				break;

			case 'profile':
				$url_timeline = $settings['url_profile'];
				break;

			case 'list':
				$url_timeline = $settings['url_list'];
				break;

			case 'likes':
				$url_timeline = $settings['url_likes'];
				break;
		}

		$replies = ( $settings['replies'] ) ? 'true' : 'false';

		$attributes = array(
			'id' => $this->get_id(),
			'class' => 'twitter-timeline',
			'data-lang' => get_locale(),
			'data-partner' => 'twitter-deck',
			'href' => esc_url( $url_timeline ),
			'data-height' => esc_attr( $settings['height']['size'] ),
			'data-theme' => esc_attr( $settings['theme'] ),
			'data-border-color' => esc_attr( $settings['border_color'] ),
			'data-show-replies' => $replies,
			'data-chrome' => esc_attr( $data_chrome ),
		);

		if ( ! empty( $settings['limit'] ) ) {
			$attributes['data-tweet-limit'] = absint( $settings['limit'] );
		}

		$this->add_render_attribute( 'timeline', $attributes );

		?>
		<a <?php echo $this->get_render_attribute_string( 'timeline' ); ?> ></a>
		<?php
	}

	/**
	 * Render tweet twitter.
	 *
	 * Used to generate the tweet HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Tweet HTML.
	 */
	public function get_tweet_html( $settings ) {
		$attributes = array(
			'id' => $this->get_id(),
			'class' => 'twitter-tweet',
			'data-lang' => get_locale(),
			'data-partner' => 'twitter-deck',
			'data-cards' => ( $settings['expanded_tweet'] ? esc_attr( $settings['expanded_tweet'] ) : 'hidden' ),
			'data-theme' => esc_attr( $settings['theme'] ),
			'data-align' => esc_attr( $settings['alignment_tweet'] ),
		);

		$this->add_render_attribute( 'tweet', $attributes );

		?>
		<blockquote <?php echo $this->get_render_attribute_string( 'tweet' ); ?>>
			<a href="<?php echo esc_url( $settings['url_tweet'] ); ?>"></a>
		</blockquote>
		<?php
	}

	/**
	 * Render moment twitter.
	 *
	 * Used to generate the moment HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Moment HTML.
	 */
	public function get_moments_html( $settings ) {
		$attributes = array(
			'id' => $this->get_id(),
			'class' => 'twitter-moment',
			'href' => esc_url( $settings['url_moments'] ),
			'data-lang' => get_locale(),
			'data-partner' => 'twitter-deck',
			'data-limit' => esc_attr( absint( $settings['limit'] ) ),
		);

		$this->add_render_attribute( 'moments', $attributes );

		?>
		<a <?php echo $this->get_render_attribute_string( 'moments' ); ?> ></a>
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
				'field' => 'url_collection',
				'type' => esc_html__( 'Collection URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_tweet',
				'type' => esc_html__( 'Tweet URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_profile',
				'type' => esc_html__( 'Profile URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_list',
				'type' => esc_html__( 'List URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_moments',
				'type' => esc_html__( 'Moments URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_likes',
				'type' => esc_html__( 'Likes URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
