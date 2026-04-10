<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Base\Base_Widget;

use Elementor\Controls_Manager;

use Give\DonationForms\AsyncData\Actions\LoadAsyncDataAssets;
use Give\ThirdPartySupport\Elementor\Traits\HasFormOptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Donation_Form_Grid extends Base_Widget {

	use HasFormOptions;

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.19.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-donation-form-grid';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.19.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Donation Form Grid', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.19.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-form-grid';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.19.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'giv',
			'givewp',
			'donation',
			'form',
			'grid',
			'forms',
		);
	}

	/**
	 * @inheritDoc
	 * @since 1.19.0
	 */
	public function get_custom_help_url() {
		return 'http://docs.givewp.com/shortcode-form-grid';
	}

	/**
	 * @since 1.19.0
	 */
	protected function get_upsale_data() {
		return array();
	}

	/**
	 * @inheritDoc
	 * @since 1.19.0
	 */
	public function get_script_depends() {
		return array(
			LoadAsyncDataAssets::handleName(),
			'give',
		);
	}

	/**
	 * @inheritDoc
	 * @since 1.19.0
	 */
	public function get_style_depends() {
		return array(
			LoadAsyncDataAssets::handleName(),
			'widget-cmsmasters-give-wp',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.19.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.19.0
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 * @since 1.19.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.19.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'form_grid_settings',
			array( 'label' => esc_html__( 'Form Grid Settings', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'forms_per_page',
			array(
				'label' => esc_html__( 'Forms Per Page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Sets the number of forms to display per page.', 'cmsmasters-elementor' ),
				'min' => 1,
				'max' => 50,
				'default' => 12,
			)
		);

		$this->add_control(
			'columns',
			array(
				'label' => esc_html__( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Sets the number of forms per row.', 'cmsmasters-elementor' ),
				'options' => array(
					'' => esc_html__( 'Best Fit', 'cmsmasters-elementor' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default' => '',
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label' => esc_html__( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Different parameter to set the order for the forms display in the form grid.', 'cmsmasters-elementor' ),
				'options' => array(
					'' => esc_html__( 'Date Created', 'cmsmasters-elementor' ),
					'title' => esc_html__( 'Form Name', 'cmsmasters-elementor' ),
					'amount_donated' => esc_html__( 'Amount Donated', 'cmsmasters-elementor' ),
					'number_donations' => esc_html__( 'Number of Donations', 'cmsmasters-elementor' ),
					'menu_order' => esc_html__( 'Menu Order', 'cmsmasters-elementor' ),
					'post__in' => esc_html__( 'Provided Form IDs', 'cmsmasters-elementor' ),
					'closest_to_goal' => esc_html__( 'Closest To Goal', 'cmsmasters-elementor' ),
					'random' => esc_html__( 'Random', 'cmsmasters-elementor' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => esc_html__( 'Order', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Display forms based on order.', 'cmsmasters-elementor' ),
				'options' => array(
					'' => esc_html__( 'Descending', 'cmsmasters-elementor' ),
					'ASC' => esc_html__( 'Ascending', 'cmsmasters-elementor' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'display_style',
			array(
				'label' => esc_html__( 'Display Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Show form as modal window or redirect to a new page?', 'cmsmasters-elementor' ),
				'options' => array(
					'' => esc_html__( 'Modal', 'cmsmasters-elementor' ),
					'redirect' => esc_html__( 'Redirect', 'cmsmasters-elementor' ),
				),
				'default' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_selection_settings',
			array( 'label' => esc_html__( 'Form Selection', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'selection_type',
			array(
				'label' => esc_html__( 'Form Selection', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Choose how to select forms to display.', 'cmsmasters-elementor' ),
				'options' => array(
					'all' => esc_html__( 'All Forms', 'cmsmasters-elementor' ),
					'include' => esc_html__( 'Include Specific Forms', 'cmsmasters-elementor' ),
					'exclude' => esc_html__( 'Exclude Specific Forms', 'cmsmasters-elementor' ),
				),
				'default' => 'all',
			)
		);

		$this->add_control(
			'ids',
			array(
				'label' => esc_html__( 'Include Forms', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'description' => esc_html__( 'Select specific forms to include in the grid.', 'cmsmasters-elementor' ),
				'multiple' => true,
				'options' => $this->getFormOptions(),
				'condition' => array( 'selection_type' => 'include' ),
			)
		);

		$this->add_control(
			'exclude',
			array(
				'label' => esc_html__( 'Exclude Forms', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'description' => esc_html__( 'Select specific forms to exclude from the grid.', 'cmsmasters-elementor' ),
				'multiple' => true,
				'options' => $this->getFormOptions(),
				'condition' => array( 'selection_type' => 'exclude' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'display_options',
			array( 'label' => esc_html__( 'Display Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'show_title',
			array(
				'label' => esc_html__( 'Show Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Show/Hide the form title.', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->add_control(
			'show_featured_image',
			array(
				'label' => esc_html__( 'Show Featured Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Do you want to display the featured image?', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label' => esc_html__( 'Image Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Select the size of the featured image.', 'cmsmasters-elementor' ),
				'options' => array(
					'thumbnail' => esc_html__( 'Thumbnail', 'cmsmasters-elementor' ),
					'medium' => esc_html__( 'Medium', 'cmsmasters-elementor' ),
					'large' => esc_html__( 'Large', 'cmsmasters-elementor' ),
					'full' => esc_html__( 'Full Size', 'cmsmasters-elementor' ),
				),
				'default' => 'medium',
				'condition' => array( 'show_featured_image' => 'true' ),
			)
		);

		$this->add_control(
			'image_height_options',
			array(
				'label' => esc_html__( 'Image Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Set the height of the featured image.', 'cmsmasters-elementor' ),
				'options' => array(
					'auto' => esc_html__( 'Auto', 'cmsmasters-elementor' ),
					'fixed' => esc_html__( 'Fixed', 'cmsmasters-elementor' ),
				),
				'default' => 'auto',
				'condition' => array( 'show_featured_image' => 'true' ),
			)
		);

		$this->add_control(
			'image_height',
			array(
				'label' => esc_html__( 'Image Height (px)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Set a fixed height for images in pixels.', 'cmsmasters-elementor' ),
				'default' => 200,
				'condition' => array(
					'show_featured_image' => 'true',
					'image_height_options' => 'fixed',
				),
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label' => esc_html__( 'Show Excerpt', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Do you want to display the excerpt?', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label' => esc_html__( 'Excerpt Length (words)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Number of words to display in the excerpt.', 'cmsmasters-elementor' ),
				'default' => 16,
				'condition' => array( 'show_excerpt' => 'true' ),
			)
		);

		$this->add_control(
			'show_goal',
			array(
				'label' => esc_html__( 'Show Goal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Do you want to display the goal\'s progress bar?', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->add_control(
			'show_donate_button',
			array(
				'label' => esc_html__( 'Show Donate Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Show/Hide the donate button on each form.', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->add_control(
			'paged',
			array(
				'label' => esc_html__( 'Enable Pagination', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Enable pagination for the form grid.', 'cmsmasters-elementor' ),
				'label_on' => esc_html__( 'Enable', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Disable', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'true',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_options',
			array( 'label' => esc_html__( 'Style Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'progress_bar_color',
			array(
				'label' => esc_html__( 'Progress Bar Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'description' => esc_html__( 'Choose the color for the goal progress bar.', 'cmsmasters-elementor' ),
				'default' => '#69b86b',
				'condition' => array( 'show_goal' => 'true' ),
			)
		);

		$this->add_control(
			'tag_background_color',
			array(
				'label' => esc_html__( 'Tag Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'description' => esc_html__( 'Background color for form tags.', 'cmsmasters-elementor' ),
				'default' => '#69b86b',
			)
		);

		$this->add_control(
			'tag_text_color',
			array(
				'label' => esc_html__( 'Tag Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'description' => esc_html__( 'Text color for form tags.', 'cmsmasters-elementor' ),
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'donate_button_text_color',
			array(
				'label' => esc_html__( 'Donate Button Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'description' => esc_html__( 'Text color for the donate button.', 'cmsmasters-elementor' ),
				'default' => '#69b86b',
				'condition' => array( 'show_donate_button' => 'true' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_options',
			array( 'label' => esc_html__( 'Filter Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'categories',
			array(
				'label' => esc_html__( 'Categories', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'description' => esc_html__( 'Filter forms by specific categories.', 'cmsmasters-elementor' ),
				'multiple' => true,
				'options' => $this->get_category_options(),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label' => esc_html__( 'Tags', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'description' => esc_html__( 'Filter forms by specific tags.', 'cmsmasters-elementor' ),
				'multiple' => true,
				'options' => $this->get_tag_options(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get available GiveWP form categories.
	 *
	 * @since 1.19.0
	 */
	protected function get_category_options() {
		$categories = get_terms( array(
			'taxonomy' => 'give_forms_category',
			'hide_empty' => false,
		) );

		$options = array();

		if ( ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$options[ $category->term_id ] = $category->name;
			}
		}

		return $options;
	}

	/**
	 * Get available GiveWP form tags.
	 *
	 * @since 1.19.0
	 */
	protected function get_tag_options() {
		$tags = get_terms( array(
			'taxonomy' => 'give_forms_tag',
			'hide_empty' => false,
		) );

		$options = array();

		if ( ! is_wp_error( $tags ) ) {
			foreach ( $tags as $tag ) {
				$options[ $tag->term_id ] = $tag->name;
			}
		}

		return $options;
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * @since 1.19.0
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		$give_wp_settings = get_option( 'give_settings' );
		$show_featured_image = ( isset( $settings['show_featured_image'] ) ? $settings['show_featured_image'] : '' );
		$form_featured_img = ( isset( $give_wp_settings['form_featured_img'] ) ? $give_wp_settings['form_featured_img'] : 'enabled' );

		if ( 'yes' !== $show_featured_image || 'enabled' !== $form_featured_img ) {
			$this->add_render_attribute( '_wrapper', array(
				'class' => 'cmsmasters-give-wp-form-grid-no-image',
			) );
		}

		// Build shortcode attributes
		$attributes = array();

		if ( isset( $settings['forms_per_page'] ) ) {
			$attributes[] = sprintf( 'forms_per_page="%s"', esc_attr( $settings['forms_per_page'] ) );
		}

		if ( isset( $settings['columns'] ) && ! empty( $settings['columns'] ) ) {
			$attributes[] = sprintf( 'columns="%s"', esc_attr( $settings['columns'] ) );
		}

		if ( isset( $settings['orderby'] ) && ! empty( $settings['orderby'] ) ) {
			$attributes[] = sprintf( 'orderby="%s"', esc_attr( $settings['orderby'] ) );
		}

		if ( isset( $settings['order'] ) && ! empty( $settings['order'] ) ) {
			$attributes[] = sprintf( 'order="%s"', esc_attr( $settings['order'] ) );
		}

		if ( isset( $settings['display_style'] ) && ! empty( $settings['display_style'] ) ) {
			$attributes[] = sprintf( 'display_style="%s"', esc_attr( $settings['display_style'] ) );
		}

		// Handle form selection
		if ( isset( $settings['selection_type'] ) ) {
			if ( 'include' === $settings['selection_type'] && isset( $settings['ids'] ) && ! empty( $settings['ids'] ) ) {
				$ids = is_array( $settings['ids'] ) ? implode( ',', $settings['ids'] ) : $settings['ids'];
				$attributes[] = sprintf( 'ids="%s"', esc_attr( $ids ) );
			} elseif ( 'exclude' === $settings['selection_type'] && isset( $settings['exclude'] ) && ! empty( $settings['exclude'] ) ) {
				$exclude = is_array( $settings['exclude'] ) ? implode( ',', $settings['exclude'] ) : $settings['exclude'];
				$attributes[] = sprintf( 'exclude="%s"', esc_attr( $exclude ) );
			}
		}

		// Handle categories and tags
		if ( isset( $settings['categories'] ) && ! empty( $settings['categories'] ) ) {
			$cats = is_array( $settings['categories'] ) ? implode( ',', $settings['categories'] ) : $settings['categories'];
			$attributes[] = sprintf( 'cats="%s"', esc_attr( $cats ) );
		}

		if ( isset( $settings['tags'] ) && ! empty( $settings['tags'] ) ) {
			$tags = is_array( $settings['tags'] ) ? implode( ',', $settings['tags'] ) : $settings['tags'];
			$attributes[] = sprintf( 'tags="%s"', esc_attr( $tags ) );
		}

		// Handle display options (only add if false to override defaults)
		if ( isset( $settings['show_title'] ) && 'true' !== $settings['show_title'] ) {
			$attributes[] = 'show_title="false"';
		}

		if ( isset( $settings['show_goal'] ) && 'true' !== $settings['show_goal'] ) {
			$attributes[] = 'show_goal="false"';
		}

		if ( isset( $settings['show_excerpt'] ) && 'true' !== $settings['show_excerpt'] ) {
			$attributes[] = 'show_excerpt="false"';
		}

		if ( isset( $settings['show_featured_image'] ) && 'true' !== $settings['show_featured_image'] ) {
			$attributes[] = 'show_featured_image="false"';
		}

		if ( isset( $settings['show_donate_button'] ) && 'true' !== $settings['show_donate_button'] ) {
			$attributes[] = 'show_donate_button="false"';
		}

		if ( isset( $settings['paged'] ) && 'true' !== $settings['paged'] ) {
			$attributes[] = 'paged="false"';
		}

		// Handle optional settings with values
		if ( isset( $settings['excerpt_length'] ) && ! empty( $settings['excerpt_length'] ) ) {
			$attributes[] = sprintf( 'excerpt_length="%s"', esc_attr( $settings['excerpt_length'] ) );
		}

		if ( isset( $settings['image_size'] ) && ! empty( $settings['image_size'] ) ) {
			$attributes[] = sprintf( 'image_size="%s"', esc_attr( $settings['image_size'] ) );
		}

		if ( isset( $settings['image_height_options'] ) && ! empty( $settings['image_height_options'] ) ) {
			$attributes[] = sprintf( 'image_height_options="%s"', esc_attr( $settings['image_height_options'] ) );
		}

		if ( isset( $settings['image_height'] ) && ! empty( $settings['image_height'] ) && 'fixed' === $settings['image_height_options'] ) {
			$attributes[] = sprintf( 'image_height="%s"', esc_attr( $settings['image_height'] ) );
		}

		// Handle color settings
		if ( isset( $settings['progress_bar_color'] ) && ! empty( $settings['progress_bar_color'] ) ) {
			$attributes[] = sprintf( 'progress_bar_color="%s"', esc_attr( $settings['progress_bar_color'] ) );
		}

		if ( isset( $settings['tag_background_color'] ) && ! empty( $settings['tag_background_color'] ) ) {
			$attributes[] = sprintf( 'tag_background_color="%s"', esc_attr( $settings['tag_background_color'] ) );
		}

		if ( isset( $settings['tag_text_color'] ) && ! empty( $settings['tag_text_color'] ) ) {
			$attributes[] = sprintf( 'tag_text_color="%s"', esc_attr( $settings['tag_text_color'] ) );
		}

		if ( isset( $settings['donate_button_text_color'] ) && ! empty( $settings['donate_button_text_color'] ) ) {
			$attributes[] = sprintf( 'donate_button_text_color="%s"', esc_attr( $settings['donate_button_text_color'] ) );
		}

		$shortcode = '[give_form_grid ' . implode( ' ', $attributes ) . ']';

		echo do_shortcode( $shortcode );
	}
}
