<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplatePages\Module as TemplatePagesModule;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Author Box widget.
 *
 * Addon widget that displays author information of current post.
 *
 * @since 1.0.0
 */
class Author_Box extends Base_Widget {

	use Singular_Widget;

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
		return __( 'Author Box', 'cmsmasters-elementor' );
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
		return 'cmsicon-author-box';
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
			'author',
			'user',
			'profile',
			'biography',
			'avatar',
			'box',
			'block',
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
			'widget-cmsmasters-author-box',
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
	 * Register Author Box widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed Avatar Position gap on responsive.
	 * @since 1.1.0 Added background gradient to link as button.
	 * @since 1.2.0 Added `Text Decoration` control for button on hover state.
	 * Fixed applying background color on normal and hover state.
	 * @since 1.10.1 Added `Border Color` control for social icons.
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.11.11 Added `Image Resolution` control for avatar.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_author_info',
			array(
				'label' => __( 'Author Info', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'current' => __( 'Current Author', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'current',
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
				'label_block' => false,
				'prefix_class' => 'cmsmasters-align%s-',
			)
		);

		$this->add_control(
			'avatar_heading',
			array(
				'label' => __( 'Avatar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'elementor-widget-cmsmasters-author-box__avatar_',
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'render_type' => 'template',
				'condition' => array( 'source!' => 'custom' ),
			)
		);

		//This controls for custom source
		$this->add_control(
			'author_avatar',
			array(
				'label' => __( 'Profile Picture', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'source' => 'custom' ),
			)
		);
		//END

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'author_avatar',
				'default' => 'full',
				'separator' => 'none',
				'exclude' => array( 'custom' ),
				'condition' => array(
					'source' => 'custom',
					'author_avatar[id]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'layout',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'description' => __( 'Doesn`t work with Avatar Inline.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'tablet_default' => 'left',
				'mobile_default' => 'top',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-layout-image%s-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_avatar[url]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'show_avatar',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'author_box_order',
			array(
				'label' => __( 'Content Order', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'options' => array(
					'name' => __( 'Name', 'cmsmasters-elementor' ),
					'biography' => __( 'Biography', 'cmsmasters-elementor' ),
					'link' => __( 'Link', 'cmsmasters-elementor' ),
					'social' => __( 'Social Media', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'name',
					'biography',
					'link',
					'social',
				),
				'separator' => 'before',
				'label_block' => true,
				'multiple' => true,
				'control_options' => array(
					'plugins' => array(
						'remove_button',
						'drag_drop',
					),
				),
			)
		);

		$inline_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'author_box_order',
					'operator' => 'contains',
					'value' => 'name',
				),
				array(
					'name' => 'author_box_order',
					'operator' => 'contains',
					'value' => 'social',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_social_media_type',
							'operator' => '=',
							'value' => 'icon',
						),
						array(
							'name' => 'social_media_type',
							'operator' => '=',
							'value' => 'icon',
						),
					),
				),
			),
		);

		$this->add_control(
			'inline_title_social',
			array(
				'label' => __( 'Name & Social Icons Inline', 'cmsmasters-elementor' ),
				'description' => __( 'It works only if they are next to each other', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-title-inline-',
				'conditions' => $inline_conditions,
			)
		);

		$this->add_control(
			'avatar_inline',
			array(
				'label' => __( 'Avatar inline', 'cmsmasters-elementor' ),
				'description' => __( 'Avatar is on block with Name & Social Icons.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-avatar-inline-',
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						$inline_conditions,
						array(
							'name' => 'inline_title_social',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'custom',
										),
										array(
											'name' => 'author_avatar[url]',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'source',
											'operator' => '!==',
											'value' => 'custom',
										),
										array(
											'name' => 'show_avatar',
											'operator' => '=',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'inline_bottom_gap',
			array(
				'label' => __( 'Bottom Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-title-inline-yes' => '--inline-margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'inline_title_social',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'name',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'social',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'custom_social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
								array(
									'name' => 'social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'inline_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'description' => __( 'Add minimal gap between Name & Social.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-title-inline-yes' => '--inline-margin-between: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'inline_title_social',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'name',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'social',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'custom_social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
								array(
									'name' => 'social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'inline_gap_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'alignment',
							'operator' => '=',
							'value' => 'center',
						),
						array(
							'name' => 'inline_title_social',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'name',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'social',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'custom_social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
								array(
									'name' => 'social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
							),
						),
					),
				),
			)
		);

		//This control for custom source
		$this->add_control(
			'author_name',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'default' => __( 'John Doe', 'cmsmasters-elementor' ),
				'condition' => array(
					'source' => 'custom',
					'author_box_order' => 'name',
				),
			)
		);
		//END

		$this->add_control(
			'author_name_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
				),
				'default' => 'h4',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'website' => __( 'Website', 'cmsmasters-elementor' ),
					'posts_archive' => __( 'Posts Archive', 'cmsmasters-elementor' ),
				),
				'description' => __( 'Link for the Author Name and Image', 'cmsmasters-elementor' ),
				'condition' => array(
					'source!' => 'custom',
					'author_box_order' => 'name',
				),
			)
		);

		$this->add_control(
			'author_website',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'description' => __( 'Link for the Author Name and Image', 'cmsmasters-elementor' ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array(
					'source' => 'custom',
					'author_box_order' => 'name',
				),
			)
		);

		$this->add_control(
			'author_bio',
			array(
				'label' => __( 'Biography', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'separator' => 'before',
				'default' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'cmsmasters-elementor' ),
				'rows' => 4,
				'condition' => array(
					'source' => 'custom',
					'author_box_order' => 'biography',
				),
			)
		);

		$this->add_control(
			'archive_page_heading',
			array(
				'label' => __( 'Link to an Archive page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'link_button',
			array(
				'label' => __( 'Show As Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-button-',
				'default' => 'no',
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'posts_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array(
					'source' => 'custom',
					'author_box_order' => 'link',
				),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label' => __( 'Link Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'All Posts', 'cmsmasters-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'link',
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_media',
			array(
				'label' => __( 'Social Media', 'cmsmasters-elementor' ),
				'condition' => array(
					'source!' => 'custom',
					'author_box_order' => 'social',
				),
			)
		);

		$this->add_control(
			'social_media_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'title' => array(
						'title' => __( 'Title', 'cmsmasters-elementor' ),
						'description' => __( 'Title', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'icon',
				'toggle' => false,
			)
		);

		$this->add_control(
			'social_media_target_blank',
			array(
				'label' => __( 'Open in a new window', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		/** @var TemplatePagesModule $pages_module */
		$pages_module = TemplatePagesModule::instance();
		$show_fields = $pages_module->get_customer_meta_fields();

		foreach ( $show_fields['social_media']['fields'] as $key => $field ) {
			if ( isset( get_post( get_the_ID() )->post_author ) ) {
				$social_url = get_user_meta( get_post( get_the_ID() )->post_author, $key, true );

				if ( '' !== $social_url ) {
					$this->add_control(
						'social_media_icon_' . $key,
						array(
							'label' => $field['label'],
							'type' => Controls_Manager::ICONS,
							'fa4compatibility' => 'icon',
							'default' => array(
								'value' => 'fab fa-' . $key,
								'library' => 'fa-brands',
							),
							'condition' => array( 'social_media_type' => 'icon' ),
						)
					);
				}
			}
		}

		$this->add_control(
			'social_media_icon_website',
			array(
				'label' => __( 'Website', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-globe',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'social_media_type' => 'icon',
					'social_media_additional_display' => 'yes',
				),
			)
		);

		$this->add_control(
			'social_media_icon_email',
			array(
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-envelope',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'social_media_type' => 'icon',
					'social_media_additional_display' => 'yes',
				),
			)
		);

		$this->add_control(
			'social_media_additional_display',
			array(
				'label' => __( 'Website and Email', 'cmsmasters-elementor' ),
				'description' => __( 'Use to enable Website and Email links', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'social_media_additional_reverse_position',
			array(
				'label' => __( 'Change Website and Email positions', 'cmsmasters-elementor' ),
				'description' => __( 'When enabled allows to change Website and Email positions to choose what link should be displayed first.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'social_media_additional_display' => 'yes' ),
			)
		);

		$this->add_control(
			'social_media_additional_position',
			array(
				'label' => __( 'Position relative to main social icons', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'before' => array(
						'title' => __( 'Before', 'cmsmasters-elementor' ),
						'description' => __( 'Before Social Media', 'cmsmasters-elementor' ),
					),
					'after' => array(
						'title' => __( 'After', 'cmsmasters-elementor' ),
						'description' => __( 'After Social Media', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'default' => 'before',
				'condition' => array( 'social_media_additional_display' => 'yes' ),
			)
		);

		$this->add_control(
			'social_media_display',
			array(
				'label' => __( 'Display', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
						'description' => __( 'All items in line', 'cmsmasters-elementor' ),
					),
					'block' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
						'description' => __( 'All items from new line', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-social-media-display-',
				'selectors_dictionary' => array(
					'inline' => 'display:inline-block; vertical-align:middle;',
					'block' => 'display:block;',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item' => '{{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_vertical_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
				),
				'label_block' => false,
				'prefix_class' => 'cmsmasters-image-valign-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '!==',
							'value' => 'top',
						),
						array(
							'name' => 'avatar_inline',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'image_size',
			array(
				'label' => __( 'Image Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 40,
						'max' => 300,
						'step' => 5,
					),
					'%' => array(
						'min' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--avatar-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__avatar img' => 'width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'image_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
						'step' => 5,
					),
					'%' => array(
						'min' => -20,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--avatar-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_gap_vertical',
			array(
				'label' => __( 'Negative Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Uses for Avatar position on top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 200,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--avatar-negative-gap: -{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__avatar img',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_name_style',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_SECONDARY ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'link_to',
									'operator' => '=',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'author_website[url]',
									'operator' => '=',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name, {{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'name_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Please note that gap doesn`t work for last element.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'inline_title_social',
							'operator' => '!==',
							'value' => 'yes',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'name',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'custom',
										),
										array(
											'name' => 'author_name',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'name_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'link_to',
									'operator' => '=',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'author_website[url]',
									'operator' => '=',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tabs(
			'name_tabs', array(
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'link_to',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'author_website[url]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tab(
			'name_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'name_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_SECONDARY ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'name_text_shadow_normal',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'name_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'name_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_SECONDARY ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'name_text_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a:hover',
			)
		);

		$this->add_control(
			'name_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name a' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'name_stroke_heading',
			array(
				'label' => __( 'Stroke', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'name_stroke_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'name_stroke_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'name_stroke_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name:hover' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'stroke_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__name' => 'transition: all {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'name',
								),
								array(
									'name' => 'author_name',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'name_stroke_width[size]',
									'operator' => '>',
									'value' => '0',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'divider_after_name',
			array(
				'type' => Controls_Manager::DIVIDER,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'author_box_order',
											'operator' => 'contains',
											'value' => 'name',
										),
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'current',
										),
									),
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'author_box_order',
											'operator' => 'contains',
											'value' => 'name',
										),
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'custom',
										),
										array(
											'name' => 'author_name',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
							),
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'author_box_order',
											'operator' => 'contains',
											'value' => 'biography',
										),
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'current',
										),
									),
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'author_box_order',
											'operator' => 'contains',
											'value' => 'biography',
										),
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'custom',
										),
										array(
											'name' => 'author_bio',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_bio_style',
			array(
				'label' => __( 'Biography', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_bio',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__bio' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_bio',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'bio_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__bio',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_bio',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'bio_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__bio',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_TEXT ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_bio',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'bio_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Please note that gap doesn`t work for last element.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__bio' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'biography',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'author_bio',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_archive_link',
			array(
				'label' => __( 'Archive Link', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'author_box_order' => 'link',
					'link_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'link_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Please note that gap doesn`t work for last element.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs(
			'link_tabs', array()
		);

			$this->start_controls_tab(
				'link_link_normal',
				array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'link_color_normal',
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'link_typography_normal',
					'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button',
					'fields_options' => array(
						'typography' => array(
							'separator' => 'before',
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'link_text_shadow_normal',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button',
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'link_link_hover',
				array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'link_color_hover',
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'link_typography_hover',
					'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button:hover',
					'fields_options' => array(
						'typography' => array(
							'separator' => 'before',
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'link_text_shadow_hover',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button:hover',
				)
			);

			$this->add_control(
				'link_transition',
				array(
					'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0.3,
					),
					'range' => array(
						'px' => array(
							'max' => 3,
							'step' => 0.1,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button' => 'transition: all {{SIZE}}s',
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'link_blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => 'Button',
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'author_box_order' => 'link',
					'link_button' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_ACCENT ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'prefix_class' => 'elementor-widget-cmsmasters-author-box__button-border-',
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button',
			)
		);

		$button_states = array(
			'' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		foreach ( $button_states  as $button_key => $label ) {
			$button_selector = '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button';
			$gradient_selector = $button_selector . '::before, ' . $button_selector . '::after';
			$button_id_key = '';

			if ( 'hover' === $button_key ) {
				$button_selector .= ':hover';
				$gradient_selector = $button_selector . ':after';
				$button_id_key = '_' . $button_key;
			}

			if ( 'hover' === $button_key ) {
				$this->start_controls_tab(
					"tab_button{$button_id_key}",
					array( 'label' => $label )
				);

				$button_color_id = 'button_hover_color';
			} else {
				$this->start_controls_tab(
					'tab_button_normal',
					array( 'label' => $label )
				);

				$button_color_id = 'button_text_color';
			}

			$this->add_control(
				$button_color_id,
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => array( 'default' => Kit_Globals::COLOR_SECONDARY ),
					'default' => '',
					'selectors' => array(
						$button_selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"button_background{$button_id_key}_color_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$gradient_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_background_group{$button_id_key}_color_stop.SIZE}}{{button_background_group{$button_id_key}_color_stop.UNIT}}, {{button_background_group{$button_id_key}_color_b.VALUE}} {{button_background_group{$button_id_key}_color_b_stop.SIZE}}{{button_background_group{$button_id_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
						"button_background_group{$button_id_key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_background_group{$button_id_key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_background_group{$button_id_key}_color_stop.SIZE}}{{button_background_group{$button_id_key}_color_stop.UNIT}}, {{button_background_group{$button_id_key}_color_b.VALUE}} {{button_background_group{$button_id_key}_color_b_stop.SIZE}}{{button_background_group{$button_id_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_background_group{$button_id_key}_background" => array( 'gradient' ),
						"button_background_group{$button_id_key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_border{$button_id_key}_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$button_selector => 'border-color: {{VALUE}};',
					),
					'global' => array( 'default' => Kit_Globals::COLOR_SECONDARY ),
					'condition' => array( 'button_border_border!' => 'none' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_box_shadow{$button_id_key}",
					'selector' => $button_selector,
					'fields_options' => array(
						'box_shadow_type' => array(
							'separator' => 'default',
						),
					),
				)
			);

			if ( 'hover' === $button_key ) {
				$this->add_control(
					"button_text_decoration{$button_id_key}",
					array(
						'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'Disable', 'cmsmasters-elementor' ),
							'underline' => __( 'Underline', 'cmsmasters-elementor' ),
							'overline' => __( 'Overline', 'cmsmasters-elementor' ),
							'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
						),
						'selectors' => array(
							$button_selector => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_control(
				"button_border_radius{$button_id_key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$button_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			if ( 'hover' === $button_key ) {
				$this->add_control(
					'button_hover_animation',
					array(
						'label' => __( 'Animation', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::HOVER_ANIMATION,
					)
				);

				$this->add_control(
					'button_hover_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array(
							'size' => 0.3,
						),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button,
							{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button:before,
							{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button:after' => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => __( 'Social Media Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'social',
								),
								array(
									'name' => 'social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'social',
								),
								array(
									'name' => 'custom_social_media_type',
									'operator' => '=',
									'value' => 'icon',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Please note that gap doesn`t work for last element.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__social-list' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'inline_title_social',
							'operator' => '!==',
							'value' => 'yes',
						),
						array(
							'name' => 'author_box_order',
							'operator' => 'contains',
							'value' => 'social',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'current',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'source',
											'operator' => '=',
											'value' => 'custom',
										),
										array(
											'name' => 'author_name',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-social-media-display-inline .elementor-widget-cmsmasters-author-box__icon-item:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.cmsmasters-social-media-display-block .elementor-widget-cmsmasters-author-box__icon-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'social_media_icon_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'elementor-widget-cmsmasters-author-box_view_',
			)
		);

		$this->start_controls_tabs(
			'icon_colors'
		);

		$this->start_controls_tab(
			'icon_colors_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_primary_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a, {{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_default .elementor-widget-cmsmasters-author-box__icon-item a' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a, {{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_default .elementor-widget-cmsmasters-author-box__icon-item a svg' => 'fill: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
			)
		);

		$this->add_control(
			'icon_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a svg' => 'fill: {{VALUE}};',
				),
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'social_media_icon_view' => 'framed',
					'icon_border_width_border!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_box_shadow',
				'fields_options' => array(
					'box_shadow_type' => array(
						'separator' => 'default',
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a',
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_hover_primary_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a:hover, {{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_default .elementor-widget-cmsmasters-author-box__icon-item a:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a:hover, {{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_default .elementor-widget-cmsmasters-author-box__icon-item a:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_stacked .elementor-widget-cmsmasters-author-box__icon-item a:hover svg' => 'fill: {{VALUE}};',
				),
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_hover_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}.elementor-widget-cmsmasters-author-box_view_framed .elementor-widget-cmsmasters-author-box__icon-item a:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'social_media_icon_view' => 'framed',
					'icon_border_width_border!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_hover_box_shadow',
				'fields_options' => array(
					'box_shadow_type' => array(
						'separator' => 'default',
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a:hover',
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'hover_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.3,
				),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_rotate',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a:before, {{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a' => 'transform: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'icon_border_width',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__social-list .elementor-widget-cmsmasters-author-box__icon-item a',
				'condition' => array( 'social_media_icon_view' => 'framed' ),
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'social_media_icon_view!' => 'default' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title_media',
			array(
				'label' => __( 'Social Media Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'social',
								),
								array(
									'name' => 'social_media_type',
									'operator' => '=',
									'value' => 'title',
								),
								array(
									'name' => 'source',
									'operator' => '!==',
									'value' => 'custom',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'author_box_order',
									'operator' => 'contains',
									'value' => 'social',
								),
								array(
									'name' => 'custom_social_media_type',
									'operator' => '=',
									'value' => 'title',
								),
								array(
									'name' => 'source',
									'operator' => '=',
									'value' => 'custom',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'title_margin',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'description' => __( 'Please note that gap doesn`t work for last element.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.cmsmasters-align-center.cmsmasters-title-inline-yes .elementor-widget-cmsmasters-author-box__title-item' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_margin_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-social-media-display-inline .elementor-widget-cmsmasters-author-box__title-item' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.cmsmasters-social-media-display-block .elementor-widget-cmsmasters-author-box__title-item' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs(
			'title_tabs', array()
		);

			$this->start_controls_tab(
				'title_link_normal',
				array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'title_color_normal',
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'title_typography_normal',
					'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item',
					'fields_options' => array(
						'typography' => array(
							'separator' => 'before',
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'title_text_shadow_normal',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item a',
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'title_link_hover',
				array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'title_color_hover',
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item:hover a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'title_typography_hover',
					'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item:hover',
					'fields_options' => array(
						'typography' => array(
							'separator' => 'before',
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'title_text_shadow_hover',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item:hover a',
				)
			);

			$this->add_control(
				'title_transition',
				array(
					'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 0.3,
					),
					'range' => array(
						'px' => array(
							'max' => 3,
							'step' => 0.1,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item a' => 'transition: all {{SIZE}}s',
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_social_media',
			array(
				'label' => __( 'Social Media', 'cmsmasters-elementor' ),
				'condition' => array(
					'source' => 'custom',
					'author_box_order' => 'social',
				),
			)
		);

		$this->add_control(
			'custom_social_media_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'title' => array(
						'title' => __( 'Title', 'cmsmasters-elementor' ),
						'description' => __( 'Title', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'icon',
				'toggle' => false,
			)
		);

		$repeater_icon = new Repeater();

		$repeater_icon->add_control(
			'custom_social_media_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-youtube',
					'library' => 'fa-brands',
				),
			)
		);

		$repeater_icon->add_control(
			'custom_social_media_icon_link',
			array(
				'label' => __( 'Custom Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'custom_social_media_icon_repeater',
			array(
				'label' => __( 'Social Media Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_icon->get_controls(),
				'default' => array(
					array(
						'custom_social_media_icon' => array(
							'value' => 'fab fa-facebook-f',
							'library' => 'fa-brands',
						),
						'custom_social_media_icon_link' => array(
							'url' => 'https://www.facebook.com/',
						),
					),
					array(
						'custom_social_media_icon' => array(
							'value' => 'fab fa-instagram',
							'library' => 'fa-brands',
						),
						'custom_social_media_icon_link' => array(
							'url' => 'https://www.instagram.com/',
						),
					),
				),
				'title_field' => '<span class="{{{ custom_social_media_icon.value }}}"></span>',
				'condition' => array( 'custom_social_media_type' => 'icon' ),
			)
		);

		$repeater_title = new Repeater();

		$repeater_title->add_control(
			'custom_social_media_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Title', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Type your title here', 'cmsmasters-elementor' ),
			)
		);

		$repeater_title->add_control(
			'custom_social_media_title_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'default' => array(
					'url' => '#',
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'custom_social_media_title_repeater',
			array(
				'label' => __( 'Social Media Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_title->get_controls(),
				'title_field' => '<# if ( \'Title\' === custom_social_media_title ) { #> {{{ custom_social_media_title }}} #<span class="cmsmasters-repeat-item-num"></span> <# } else { #> {{{ custom_social_media_title }}} <span class="cmsmasters-repeat-item-num hidden"></span> <# } #>',
				'condition' => array( 'custom_social_media_type' => 'title' ),
			)
		);

		$this->add_control(
			'custom_social_media_display',
			array(
				'label' => __( 'Display', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
						'description' => __( 'All items in line', 'cmsmasters-elementor' ),
					),
					'block' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
						'description' => __( 'All items from new line', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-social-media-display-',
				'selectors_dictionary' => array(
					'inline' => 'display:inline-block; vertical-align:middle;',
					'block' => 'display:block;',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__icon-item' => '{{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-author-box__title-item' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get additional icon\title to social.
	 *
	 * Return list of social icon\title mail & website.
	 *
	 * @since 1.0.0
	 */
	private function print_social_list_add() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' !== $settings['social_media_additional_display'] ) {
			return;
		}

		$icon_list_add = array(
			'email',
			'website',
		);

		$target_blank = ( isset( $settings['social_media_target_blank'] ) ? $settings['social_media_target_blank'] : '' );

		if (
			1 < count( $icon_list_add ) &&
			'yes' === $settings['social_media_additional_reverse_position']
		) {
			$icon_list_add = array_reverse( $icon_list_add );
		}

		foreach ( $icon_list_add as $item ) {
			$pref = ( 'email' === $item ? 'mailto:' : '' );
			$href_item = ( 'website' === $item ? 'url' : $item );
			$get_social_title = ( 'title' === $settings['social_media_type'] ) ? ucfirst( $item ) : '';
			$aria_label = ucfirst( $item );

			$this->add_render_attribute(
				"social-{$item}-link",
				array(
					'href' => $pref . get_the_author_meta( "user_{$href_item}", get_post( get_the_ID() )->post_author ),
					'aria-label' => esc_attr( $aria_label ),
				)
			);

			$social_item_class = 'elementor-widget-cmsmasters-author-box__' . $settings['social_media_type'] . '-item';

			if (
				(
					'icon' === $settings['social_media_type'] &&
					! empty( $settings[ 'social_media_icon_' . $item ] )
				) ||
				! empty( $get_social_title )
			) {
				echo '<li id="' . esc_attr( "{$social_item_class}-{$item}" ) . '" class="' . esc_attr( $social_item_class ) . '">
					<a ' . $this->get_render_attribute_string( "social-{$item}-link" ) . ( $target_blank && 'email' !== $item ? ' target="_blank"' : '' ) . '>';

						if ( 'icon' === $settings['social_media_type'] ) {
							Icons_Manager::render_icon(
								$settings[ 'social_media_icon_' . $item ],
								array(
									'aria-hidden' => 'true',
									'aria-label' => esc_attr( ucfirst( $item ) ),
								)
							);
						} else {
							echo esc_html( $get_social_title );
						}

					echo '</a>' .
				'</li>';
			}
		}
	}

	/**
	 * Get social list.
	 *
	 * Return list of social icons or titles. It depends on settings
	 *
	 * @since 1.0.0
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 */
	private function print_social_list( $type ) {
		$settings = $this->get_settings_for_display();

		/** @var TemplatePagesModule $pages_module */
		$pages_module = TemplatePagesModule::instance();

		$show_fields = $pages_module->get_customer_meta_fields();

		$show_social_fields = $show_fields['social_media']['fields'];

		$wrapper_start = '<ul class="elementor-widget-cmsmasters-author-box__social-list">';

		$target_blank = ( isset( $settings['social_media_target_blank'] ) ? $settings['social_media_target_blank'] : '' );

		$this->print_social_list_wrapper( $show_social_fields, $settings, $wrapper_start );

		if ( 'before' === $settings['social_media_additional_position'] ) {
			$this->print_social_list_add();
		}

		foreach ( $show_social_fields as $key => $field ) {
			$social_url = get_user_meta( get_post( get_the_ID() )->post_author, $key, true );

			if ( '' !== $social_url ) {
				$social_setting_attr = $this->get_repeater_setting_key( 'social-media', 'social', $key );

				$this->add_render_attribute( $social_setting_attr, array(
					'id' => "elementor-widget-cmsmasters-author-box__{$type}-item-{$key}",
					'class' => "elementor-widget-cmsmasters-author-box__{$type}-item",
				) );

				$social_title = ( 'title' === $type ) ? $field['label'] : '';

				$aria_label = ( ! empty( $social_title ) ? $social_title : 'Custom Link' );

				if ( ( isset( $settings[ 'social_media_icon_' . $key ] ) && ! empty( $settings[ 'social_media_icon_' . $key ] ) ) || $social_title ) {
					echo '<li ' . $this->get_render_attribute_string( $social_setting_attr ) . '>' .
						'<a href="' . esc_url( $social_url ) . '"' . ( $target_blank ? ' target="_blank"' : '' ) . ' aria-label="' . esc_attr( $aria_label ) . '">';

							if ( isset( $settings[ 'social_media_icon_' . $key ] ) && ! empty( $settings[ 'social_media_icon_' . $key ] ) && '' === $social_title ) {
								Icons_Manager::render_icon(
									$settings[ 'social_media_icon_' . $key ],
									array(
										'aria-hidden' => 'true',
										'aria-label' => esc_attr( ucfirst( $key ) ),
									)
								);
							} else {
								echo esc_html( $social_title );
							}

						echo '</a>' .
					'</li>';
				}
			}
		}

		if ( 'after' === $settings['social_media_additional_position'] ) {
			$this->print_social_list_add();
		}

		$this->print_social_list_wrapper( $show_social_fields, $settings, '</ul>' );
	}

	/**
	 * Check if any of social icons is available.
	 *
	 * Return html of wrapper.
	 *
	 * @since 1.1.0
	 */
	private function print_social_list_wrapper( $show_social_fields, $settings, $wrapper_html = '' ) {
		$enable_social = false;

		foreach ( $show_social_fields as $key => $field ) {
			$social_url = get_user_meta( get_post( get_the_ID() )->post_author, $key, true );

			if ( '' !== $social_url ) {
				$enable_social = true;
			}
		}

		if (
			'' !== $settings['social_media_additional_display'] &&
			( ! empty( $settings['social_media_icon_email']['value'] ) || ! empty( $settings['social_media_icon_website']['value'] ) )
		) {
			$enable_social = true;
		}

		if ( $enable_social ) {
			echo $wrapper_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get custom social list.
	 *
	 * Return custom list of social icons or titles. It depends on settings
	 *
	 * @since 1.0.0
	 */
	private function print_social_custom_list( $type ) {
		$social = $this->get_settings_for_display( 'custom_social_media_' . $type . '_repeater' );

		$id_int = substr( $this->get_id_int(), 0, 3 );

		echo '<ul class="elementor-widget-cmsmasters-author-box__social-list">';

		foreach ( $social as $index => $item ) {
			$social_count = $index + 1;

			if ( '' !== $item[ 'custom_social_media_' . $type . '_link' ]['url'] ) {
				if ( 'icon' === $type ) {
					$get_social_icon_class = ' class="' . $item[ 'custom_social_media_' . $type ]['value'] . '"';
				} else {
					$this->add_render_attribute( 'title_' . $index, 'class', array(
						'elementor-widget-cmsmasters-author-box__title-item-text',
						( 'Title' === $item['custom_social_media_title'] ) ? 'default' : '',
					) );

					$get_social_icon_class = $this->get_render_attribute_string( 'title_' . $index );
				}

				$get_social_title = ( 'title' === $type ) ? $item['custom_social_media_title'] : '';

				$social_setting_attr = $this->get_repeater_setting_key( 'social-media-' . $type, 'social', $index );

				$this->add_render_attribute( $social_setting_attr, array(
					'id' => "elementor-widget-cmsmasters-author-box__{$type}-item-{$id_int}{$social_count}",
					'class' => "elementor-widget-cmsmasters-author-box__{$type}-item",
					'data-tab' => $social_count,
				) );

				$aria_label = ( isset( $item[ "custom_social_media_{$type}" ] ) && 'title' === $type ? $get_social_title : 'Custom Link' );

				echo '<li ' . $this->get_render_attribute_string( $social_setting_attr ) . '>
					<a href="' . esc_url( $item[ "custom_social_media_{$type}_link" ]['url'] ) . '" aria-label="' . esc_attr( $aria_label ) . '">';

				if ( isset( $item[ "custom_social_media_{$type}" ] ) && 'title' === $type ) {
					echo esc_html( $get_social_title );
				} else {
					Icons_Manager::render_icon(
						$item[ "custom_social_media_{$type}" ],
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Custom Link',
						)
					);
				}

					echo '</a>' .
				'</li>';
			}
		}

		echo '</ul>';
	}

	/**
	 * Render Author Box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed display of the author's avatar in cases of disabling social icons.
	 * Fixed adding the cmsmasters-theme-button class when the `Show As Button` control is off.
	 * @since 1.11.11 Fixed empty author website and posts url in author box widget.
	 * Added apply custom size for author avatar.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$author = array();

		$link_tag = 'div';
		$link_url = '';
		$link_target = '';

		$is_custom_src = 'custom' === $settings['source'];

		if ( ! $is_custom_src ) {
			$user_id = get_the_author_meta( 'ID' );

			$author['avatar'] = get_avatar_url( $user_id, array( 'size' => 300 ) );
			$author['display_name'] = get_the_author_meta( 'display_name' );
			$author['website'] = get_the_author_meta( 'user_url' );
			$author['bio'] = get_the_author_meta( 'description' );
			$author['posts_url'] = get_author_posts_url( $user_id );
			$author['birthday'] = get_the_author_meta( 'birthday' );
		} elseif ( $is_custom_src ) {
			if ( ! empty( $settings['author_avatar']['url'] ) ) {
				$avatar_src = $settings['author_avatar']['url'];

				if ( ! empty( $settings['author_avatar']['id'] ) ) {
					$attachment_size = ( isset( $settings['author_avatar_size'] ) ? $settings['author_avatar_size'] : 'medium' );

					$attachment_image_src = wp_get_attachment_image_src( $settings['author_avatar']['id'], $attachment_size );

					if ( ! empty( $attachment_image_src[0] ) ) {
						$avatar_src = $attachment_image_src[0];
					}
				}

				$author['avatar'] = $avatar_src;
			}

			$author['display_name'] = $settings['author_name'];
			$author['website'] = ( isset( $settings['author_website'] ) ? $settings['author_website']['url'] : '' );
			$author['bio'] = wpautop( $settings['author_bio'] );
			$author['posts_url'] = ( isset( $settings['posts_url'] ) ? $settings['posts_url']['url'] : '' );
		}

		$link_to = ( isset( $settings['link_to'] ) ? $settings['link_to'] : '' );

		if ( ! empty( $link_to ) || $is_custom_src ) {
			if ( ! empty( $author['website'] ) && ( $is_custom_src || 'website' === $link_to ) ) {
				$link_tag = 'a';
				$link_url = $author['website'];

				if ( $is_custom_src ) {
					$link_target = $settings['author_website']['is_external'] ? '_blank' : '';
				} else {
					$link_target = '_blank';
				}
			} elseif ( ! empty( $author['posts_url'] ) && 'posts_archive' === $link_to ) {
				$link_tag = 'a';
				$link_url = $author['posts_url'];
			}

			if ( ! empty( $link_url ) ) {
				$this->add_render_attribute( 'author-link', 'href', $link_url );

				if ( ! empty( $link_target ) ) {
					$this->add_render_attribute( 'author-link', 'target', $link_target );
				}
			}
		}

		$this->add_render_attribute( 'button', 'class', 'elementor-widget-cmsmasters-author-box__button' );

		if ( 'yes' === $settings['link_button'] ) {
			$this->add_render_attribute( 'button', 'class', 'cmsmasters-theme-button' );
		}

		echo '<div class="elementor-widget-cmsmasters-author-box__wrapper">';

		$inline_title_social = ( isset( $settings['inline_title_social'] ) ? $settings['inline_title_social'] : '' );
		$avatar_inline = ( isset( $settings['avatar_inline'] ) ? $settings['avatar_inline'] : '' );

		if ( ( 'yes' === $inline_title_social && '' === $avatar_inline ) || ( '' === $inline_title_social ) ) {
			$this->get_avatar( $author, $link_tag );
		}

		$this->add_render_attribute( 'box-text', 'class', 'elementor-widget-cmsmasters-author-box__text' );

		$type_custom = $is_custom_src ? 'custom_' : '';
		$author_box_order = ( isset( $settings['author_box_order'] ) ? $settings['author_box_order'] : '' );

		$inline_wrap = false;

		if ( 'icon' === $settings[ $type_custom . 'social_media_type' ] && 'yes' === $inline_title_social ) {
			$name_order = array_search( 'name', $author_box_order, true );
			$array_count = count( $author_box_order );

			if ( false !== $name_order && ( --$array_count ) !== $name_order ) {
				$next_element = $author_box_order[ ++$name_order ];
				$next_element_bool = ( 'social' === $next_element ) ? true : $author_box_order[ --$name_order ];
			}

			$name_order_check = $name_order;

			if (
				( isset( $next_element_bool ) && true === $next_element_bool ) ||
				( $name_order && 'social' === $author_box_order[ --$name_order ] )
			) {
				$this->add_render_attribute( 'box-text', 'class', 'cmsmasters-title-inline' );

				if ( $name_order_check !== $name_order ) {
					$name_order = $name_order_check;
				}

				$inline_wrap = true;

				if ( $name_order && 'social' === $author_box_order[ --$name_order ] ) {
					$inline_wrap = 'social';
				}
			}
		}

		echo '<div ' . $this->get_render_attribute_string( 'box-text' ) . '>';

		foreach ( $author_box_order as $author_box ) {
			switch ( $author_box ) {
				case 'name':
					if ( ( ! $is_custom_src ) || ( $is_custom_src && ! empty( $author['display_name'] ) ) ) {
						$author_name = $author['display_name'];

						if ( ! empty( $link_to ) || $is_custom_src ) {
							$author_name = '<' . Utils::validate_html_tag( $link_tag ) . ' ' . $this->get_render_attribute_string( 'author-link' ) . '>' .
								wp_kses_post( $author_name ) .
							'</' . Utils::validate_html_tag( $link_tag ) . '>';
						}

						if ( 'social' !== $inline_wrap && 'yes' === $inline_title_social ) {
							echo '<div class="cmsmasters-title-inline-wrapper">';

							if ( 'yes' === $avatar_inline ) {
								$this->get_avatar( $author, $link_tag );
							}
						}

						echo '<' . Utils::validate_html_tag( $settings['author_name_tag'] ) . ' class="elementor-widget-cmsmasters-author-box__name">' .
							wp_kses_post( $author_name ) .
						'</' . Utils::validate_html_tag( $settings['author_name_tag'] ) . '>';

						if ( 'social' === $inline_wrap && 'yes' === $inline_title_social ) {
							echo '</div>';
						}
					}

					break;
				case 'biography':
					if ( ! $is_custom_src || ( $is_custom_src && ! empty( $author['bio'] ) ) ) {
						echo '<div class="elementor-widget-cmsmasters-author-box__bio">' . wp_kses_post( $author['bio'] ) . '</div>';
					}

					break;
				case 'link':
					if (
						! empty( $settings['link_text'] ) &&
						( ! $is_custom_src || ( $is_custom_src && ! empty( $author['posts_url'] ) ) )
					) {
						$this->add_render_attribute( 'button', 'href', $author['posts_url'] );

						if ( ! empty( $settings['button_hover_animation'] ) ) {
							$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
						}

						echo '<div class="elementor-widget-cmsmasters-author-box__button-wrap">
							<a ' . $this->get_render_attribute_string( 'button' ) . '>' . esc_html( $settings['link_text'] ) . '</a>
						</div>';
					}

					break;
				case 'social':
					if ( 'social' === $inline_wrap && 'yes' === $inline_title_social ) {
						echo '<div class="cmsmasters-title-inline-wrapper">';

						if ( 'yes' === $avatar_inline ) {
							$this->get_avatar( $author, $link_tag );
						}
					}

					$custom_media = ( $is_custom_src ) ? 'custom_' : '';

					$method_name = 'print_social_' . $custom_media . 'list';

					call_user_func( array( $this, $method_name ), $settings[ $custom_media . 'social_media_type' ] );

					if ( 'social' !== $inline_wrap && 'yes' === $inline_title_social ) {
						echo '</div>';
					}

					break;
			}
		}

		echo '</div>
		</div>';
	}

	/**
	 * Get avatar HTML.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Fixed display of the author's avatar in cases of disabling social icons.
	 *
	 * @param array $settings
	 */
	public function get_avatar( $author, $link_tag ) {
		$settings = $this->get_settings_for_display();

		$is_custom_src = 'custom' === $settings['source'];
		$show_avatar = ( isset( $settings['show_avatar'] ) ? $settings['show_avatar'] : '' );

		if (
			( $is_custom_src && ! empty( $author['avatar'] ) ) ||
			( ! $is_custom_src && 'yes' === $show_avatar )
		) {
			$this->add_render_attribute( 'avatar', 'src', $author['avatar'] );

			if ( ! empty( $author['display_name'] ) ) {
				$this->add_render_attribute( 'avatar', 'alt', $author['display_name'] );
			}

			echo '<' . Utils::validate_html_tag( $link_tag ) . ' ' . $this->get_render_attribute_string( 'author-link' ) . ' class="elementor-widget-cmsmasters-author-box__avatar">
				<img ' . $this->get_render_attribute_string( 'avatar' ) . '>
			</' . Utils::validate_html_tag( $link_tag ) . '>';
		}
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
				'field' => 'author_name',
				'type' => esc_html__( 'Author Name', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'author_website' => array(
				'field' => 'url',
				'type' => esc_html__( 'Author Website', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'author_bio',
				'type' => esc_html__( 'Author Biography', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			'posts_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Posts Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'link_text',
				'type' => esc_html__( 'Posts Link Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
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
			'custom_social_media_icon_repeater' => array(
				'custom_social_media_icon_link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Social Icon Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
			'custom_social_media_title_repeater' => array(
				array(
					'field' => 'custom_social_media_title',
					'type' => esc_html__( 'Social Title Text', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'custom_social_media_title_link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Social Title Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}

}
