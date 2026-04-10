<?php
namespace CmsmastersElementor\Modules\AnimatedText\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elementor animated text widget.
 *
 * Elementor widget lets you easily embed and promote any public
 * animated text on your website.
 *
 * @since 1.0.0
 */
class Animated_Text extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve animated text widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Animated Text', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve animated text widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-animated-text';
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
			'animated text',
			'heading',
		);
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
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'lettering',
			'textillate',
			'anime',
		), parent::get_script_depends() );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'animate',
			'widget-cmsmasters-animated-text',
		);
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
	 * Register animated text widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void Widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'animated_text_content',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'select_effect_resource',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'resource_1' => array(
						'title' => __( 'Single', 'cmsmasters-elementor' ),
					),

					'resource_2' => array(
						'title' => __( 'Multiple', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'resource_2',
				'label_block' => true,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animated_text_string',
			array(
				'label' => __( 'Animated Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Feugiat sed lectus vestibulum mattis ullamcorper velit', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_1',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_text',
			array(
				'label' => __( 'Animated Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'default' => __( 'Feugiat sed lectus vestibulum mattis ullamcorper velit', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'animated_text_list',
			array(
				'label' => __( 'Animated Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'default' => array(
					array( 'item_text' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod.', 'cmsmasters-elementor' ) ),
					array( 'item_text' => __( 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.', 'cmsmasters-elementor' ) ),
					array( 'item_text' => __( 'Duis aute irure dolor in reprehenderit in voluptate velit esse.', 'cmsmasters-elementor' ) ),
				),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ item_text }}}',
				'frontend_available' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_2',
				),
			)
		);

		$this->add_control(
			'animation_tag',
			array(
				'label' => __( 'Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => __( 'H1', 'cmsmasters-elementor' ),
					'h2' => __( 'H2', 'cmsmasters-elementor' ),
					'h3' => __( 'H3', 'cmsmasters-elementor' ),
					'h4' => __( 'H4', 'cmsmasters-elementor' ),
					'h5' => __( 'H5', 'cmsmasters-elementor' ),
					'h6' => __( 'H6', 'cmsmasters-elementor' ),
					'div' => __( 'div', 'cmsmasters-elementor' ),
					'p' => __( 'p', 'cmsmasters-elementor' ),
				),
				'default' => 'h3',
			)
		);

		$this->add_control(
			'animation_effect',
			array(
				'label' => __( 'Animation Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'effect_1' => __( 'Effect 1', 'cmsmasters-elementor' ),
					'effect_2' => __( 'Effect 2', 'cmsmasters-elementor' ),
					'effect_3' => __( 'Effect 3', 'cmsmasters-elementor' ),
					'effect_4' => __( 'Effect 4', 'cmsmasters-elementor' ),
					'effect_5' => __( 'Effect 5', 'cmsmasters-elementor' ),
					'effect_6' => __( 'Effect 6', 'cmsmasters-elementor' ),
					'effect_7' => __( 'Effect 7', 'cmsmasters-elementor' ),
				),
				'default' => 'effect_1',
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_1',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_animation',
			array(
				'separator' => 'before',
				'condition' => array(
					'select_effect_resource' => 'resource_2',
				),
			)
		);

		$this->start_controls_tab(
			'in_animation',
			array( 'label' => __( 'In', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'in_animation_effect',
			array(
				'label' => __( 'Animation Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => array(
					array(
						'label' => __( 'Flip', 'cmsmasters-elementor' ),
						'options' => array(
							'flipInX' => __( 'Flip In X', 'cmsmasters-elementor' ),
							'flipInY' => __( 'Flip In Y', 'cmsmasters-elementor' ),
						),
					),
					array(
						'label' => __( 'Fade', 'cmsmasters-elementor' ),
						'options' => array(
							'fadeIn' => __( 'Fade In', 'cmsmasters-elementor' ),
							'fadeInUp' => __( 'Fade In Up', 'cmsmasters-elementor' ),
							'fadeInDown' => __( 'Fade In Down', 'cmsmasters-elementor' ),
							'fadeInLeft' => __( 'Fade In Left', 'cmsmasters-elementor' ),
							'fadeInRight' => __( 'Fade In Right', 'cmsmasters-elementor' ),
							'fadeInUpBig' => __( 'Fade In Up Big', 'cmsmasters-elementor' ),
							'fadeInDownBig' => __( 'Fade In Down Big', 'cmsmasters-elementor' ),
							'fadeInLeftBig' => __( 'Fade In Left Big', 'cmsmasters-elementor' ),
							'fadeInRightBig' => __( 'Fade In Right Big', 'cmsmasters-elementor' ),
						),
					),
					array(
						'label' => __( 'Bounce', 'cmsmasters-elementor' ),
						'options' => array(
							'bounceIn' => __( 'Bounce In', 'cmsmasters-elementor' ),
							'bounceInDown' => __( 'Bounce In Down', 'cmsmasters-elementor' ),
							'bounceInUp' => __( 'Bounce In Up', 'cmsmasters-elementor' ),
							'bounceInLeft' => __( 'Bounce In Left', 'cmsmasters-elementor' ),
							'bounceInRight' => __( 'Bounce In Right', 'cmsmasters-elementor' ),
						),
					),
					array(
						'label' => __( 'Rotate', 'cmsmasters-elementor' ),
						'options' => array(
							'rotateIn' => __( 'Rotate In', 'cmsmasters-elementor' ),
							'rotateInDownLeft' => __( 'Rotate In Down Left', 'cmsmasters-elementor' ),
							'rotateInDownRight' => __( 'Rotate In Down Right', 'cmsmasters-elementor' ),
							'rotateInUpLeft' => __( 'Rotate In Up Left', 'cmsmasters-elementor' ),
							'rotateInUpRight' => __( 'Rotate In Up Right', 'cmsmasters-elementor' ),
							'rollIn' => __( 'RollIn', 'cmsmasters-elementor' ),
						),
					),
				),
				'default' => 'fadeInLeftBig',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'in_animation_type',
			array(
				'label' => __( 'Effect Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'sequence' => __( 'Sequence', 'cmsmasters-elementor' ),
					'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
					'sync' => __( 'Sync', 'cmsmasters-elementor' ),
					'shuffle' => __( 'Shuffle', 'cmsmasters-elementor' ),
				),
				'default' => 'sequence',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'in_animation_delay',
			array(
				'label' => __( 'Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 50,
				'description' => __( 'Set a Delay time between each character/word.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'in_animation_delay_scale',
			array(
				'label' => __( 'Delay Scale', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1.5,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'description' => __( 'Set the delay factor applied to each consecutive character/word.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'out_animation',
			array( 'label' => __( 'Out', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'out_animation_effect',
			array(
				'label' => __( 'Animation Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'flipOutX' => __( 'Flip Out X', 'cmsmasters-elementor' ),
					'flipOutY' => __( 'Flip Out Y', 'cmsmasters-elementor' ),
					'fadeOut' => __( 'Fade Out', 'cmsmasters-elementor' ),
					'fadeOutUp' => __( 'Fade Out Up', 'cmsmasters-elementor' ),
					'fadeOutDown' => __( 'Fade Out Down', 'cmsmasters-elementor' ),
					'fadeOutLeft' => __( 'Fade Out Left', 'cmsmasters-elementor' ),
					'fadeOutRight' => __( 'Fade Out Right', 'cmsmasters-elementor' ),
					'fadeOutUpBig' => __( 'Fade Out Up Big', 'cmsmasters-elementor' ),
					'fadeOutDownBig' => __( 'Fade Out Down Big', 'cmsmasters-elementor' ),
					'fadeOutLeftBig' => __( 'Fade Out Left Big', 'cmsmasters-elementor' ),
					'fadeOutRightBig' => __( 'Fade Out Right Big', 'cmsmasters-elementor' ),
					'bounceOut' => __( 'Bounce Out', 'cmsmasters-elementor' ),
					'bounceOutDown' => __( 'Bounce Out Down', 'cmsmasters-elementor' ),
					'bounceOutUp' => __( 'Bounce Out Up', 'cmsmasters-elementor' ),
					'bounceOutLeft' => __( 'Bounce Out Left', 'cmsmasters-elementor' ),
					'bounceOutRight' => __( 'Bounce Out Right', 'cmsmasters-elementor' ),
					'rotateOut' => __( 'Rotate Out', 'cmsmasters-elementor' ),
					'rotateOutDownLeft' => __( 'Rotate Out Down Left', 'cmsmasters-elementor' ),
					'rotateOutDownRight' => __( 'Rotate Out Down Right', 'cmsmasters-elementor' ),
					'rotateOutUpLeft' => __( 'Rotate Out Up Left', 'cmsmasters-elementor' ),
					'rotateOutUpRight' => __( 'Rotate Out Up Right', 'cmsmasters-elementor' ),
					'hinge' => __( 'Hinge', 'cmsmasters-elementor' ),
					'rollOut' => __( 'RollOut', 'cmsmasters-elementor' ),
				),
				'default' => 'hinge',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'out_animation_type',
			array(
				'label' => __( 'Effect Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'sequence' => __( 'Sequence', 'cmsmasters-elementor' ),
					'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
					'sync' => __( 'Sync', 'cmsmasters-elementor' ),
					'shuffle' => __( 'Shuffle', 'cmsmasters-elementor' ),
				),
				'default' => 'shuffle',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'out_animation_delay',
			array(
				'label' => __( 'Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 45,
				'description' => __( 'Set a Delay time between each character/word.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'out_animation_delay_scale',
			array(
				'label' => __( 'Delay Scale', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'description' => __( 'Set the delay factor applied to each consecutive character/word.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => __( 'Additional', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'animated_text_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'animated_text_pause_time',
			array(
				'label' => __( 'Pause Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'description' => __( 'Set the time (in milliseconds) the word/string should stay visible.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_2',
				),
			)
		);

		$this->add_control(
			'animated_text_start_delay',
			array(
				'label' => __( 'Start Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'description' => __( 'Set the time (in milliseconds) how long animation should be delayed. Example: when set to 5000 animation starts in 5 seconds. ', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_2',
				),
			)
		);

		$this->add_control(
			'animated_text_type',
			array(
				'label' => __( 'Animated Text Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'char' => array(
						'title' => __( 'Character', 'cmsmasters-elementor' ),
					),
					'word' => array(
						'title' => __( 'Word', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'default' => 'char',
				'frontend_available' => true,
				'condition' => array(
					'select_effect_resource' => 'resource_2',
				),
			)
		);

		$this->add_control(
			'animated_scroll',
			array(
				'label' => __( 'Delayed Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'description' => __( 'If enabled animation starts when a page is scrolled to the widget (not on the page load).', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animated_distance',
			array(
				'label' => __( 'Distance', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 60,
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
						'step' => 1,
					),
				),
				'description' => __( 'Indicates a distance from a bottom of a browser window when animation should start.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animated_scroll' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'animated_text_style_tab',
			array(
				'label' => __( 'Animated Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'animated_text_align',
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
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-animated-text__animated-text' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'animated_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-animated-text__animated-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'animated_text_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-animated-text__animated-text',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-animated-text__animated-text',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag = $settings['animation_tag'];

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-animated-text__animated-text-wrapper' );

		if ( $settings['animated_scroll'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-animated-text__scroll-animated' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '> 
			<' . Utils::validate_html_tag( $tag ) . ' class="elementor-widget-cmsmasters-animated-text__animated-text">';

		if ( 'resource_1' === $settings['select_effect_resource'] ) {
			$this->animated_elements_single();
		} else {
			$this->animated_elements_multiple();
		}

			echo '</' . Utils::validate_html_tag( $tag ) . '>
		</div>';
	}

	/**
	 * Render multiple elements.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Fixed validation errors.
	 */
	protected function animated_elements_multiple() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['animated_text_list'] ) ) {
			return;
		}

		echo '<span class="elementor-widget-cmsmasters-fancy-text__list-items texts">';

		foreach ( $settings['animated_text_list'] as $item ) {
			if ( ! empty( $item['item_text'] ) ) {
				echo '<span class="elementor-widget-cmsmasters-fancy-text__list-item">' . wp_kses_post( $item['item_text'] ) . '</span>';
			}
		}

		echo '</span>';
	}

	/**
	 * Render single element.
	 *
	 * @since 1.0.0
	 */
	protected function animated_elements_single() {
		$settings = $this->get_settings_for_display();

		$string = $settings['animated_text_string'];

		echo '<span class="elementor-widget-cmsmasters-animated-text__animated-text-single elementor-widget-cmsmasters-animated-text__effect ' . esc_attr( $settings['animation_effect'] ) . '">
			<span>' . wp_kses_post( $string ) . '</span>
		</span>';
	}

	/**
	 * Render fancy text widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Fixed validation errors.
	 */
	protected function content_template() {
		?>
		<# const tag = settings.animation_tag;

		view.addRenderAttribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-animated-text__animated-text-wrapper' );

		if ( settings.animated_scroll ) {
			view.addRenderAttribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-animated-text__scroll-animated' );
		}
		#>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}} >
			<{{{tag}}} class="elementor-widget-cmsmasters-animated-text__animated-text">

		<# if ( 'resource_1' === settings.select_effect_resource ) {
			const string = settings.animated_text_string;
			const effect = settings.animation_effect; #>
			<span class="elementor-widget-cmsmasters-animated-text__animated-text-single elementor-widget-cmsmasters-animated-text__effect {{{effect}}}">
				<span>{{{string}}}</span>
			</span>
		<# } else { #>

			<span class="elementor-widget-cmsmasters-fancy-text__list-items texts">

			<# _.each ( settings.animated_text_list, ( item ) => {
					if ( '' !== item.item_text ) { #>
						<span class="elementor-widget-cmsmasters-fancy-text__list-item">{{{ item.item_text }}}</span>
					<# }
			} ); #>

			</span>
		<# } #>

			</{{{tag}}}>
		</div>
		<?php
	}

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
				'field' => 'animated_text_string',
				'type' => esc_html__( 'Animated Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
		);
	}

	/**
	 * Get fields_in_item config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields in item config.
	 */
	public static function get_wpml_fields_in_item() {
		return array(
			'animated_text_list' => array(
				array(
					'field' => 'item_text',
					'type' => esc_html__( 'Animated Text', 'cmsmasters-elementor' ),
					'editor_type' => 'AREA',
				),
			),
		);
	}
}