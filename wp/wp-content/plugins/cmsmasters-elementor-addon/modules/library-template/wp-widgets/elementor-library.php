<?php
namespace CmsmastersElementor\Modules\LibraryTemplate\WP_Widgets;

use CmsmastersElementor\Modules\LibraryTemplate\Module as LibraryModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Elementor_Library extends \WP_Widget {

	private $sidebar_id;

	public function __construct() {
		parent::__construct(
			'elementor-library',
			esc_html__( 'Elementor Library', 'cmsmasters-elementor' ),
			array( 'description' => esc_html__( 'Embed your saved elements.', 'cmsmasters-elementor' ) )
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 * 
	 * @since 1.12.1 Add checking template.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] .
				apply_filters( 'widget_title', $instance['title'] ) .
			$args['after_title'];
		}

		if ( ! empty( $instance['template_id'] ) ) {
			if ( ! Utils::check_template( $instance['template_id'] ) ) {
				if ( is_admin() ) {
					Utils::render_alert( esc_html__( 'Please choose template!', 'cmsmasters-elementor' ) );
				}
			} else {
				$this->sidebar_id = $args['id'];

				add_filter( 'elementor/frontend/builder_content_data', array( $this, 'filter_content_data' ) );

				echo Plugin::elementor()->frontend->get_builder_content_for_display( $instance['template_id'] );

				remove_filter( 'elementor/frontend/builder_content_data', array( $this, 'filter_content_data' ) );

				unset( $this->sidebar_id );
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Avoid nesting a sidebar within a template that will appear in the sidebar itself.
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function filter_content_data( $data ) {
		if ( empty( $data ) ) {
			return $data;
		}

		$data = Plugin::elementor()->db->iterate_data( $data, function( $element ) {
			if (
				'widget' === $element['elType'] &&
				'sidebar' === $element['widgetType'] &&
				$this->sidebar_id === $element['settings']['sidebar']
			) {
				$element['settings']['sidebar'] = null;
			}

			return $element;
		} );

		return $data;
	}

	/**
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$default = array(
			'title' => '',
			'template_id' => '',
		);

		$instance = array_merge( $default, $instance );

		$templates = LibraryModule::get_templates();

		if ( ! $templates ) {
			echo LibraryModule::no_templates_message();

			return;
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title', 'cmsmasters-elementor' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>"><?php esc_attr_e( 'Choose Template', 'cmsmasters-elementor' ); ?>:</label>
			<select class="widefat elementor-widget-template-select" id="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'template_id' ) ); ?>">
				<option value="">— <?php _e( 'Select', 'cmsmasters-elementor' ); ?> —</option>
				<?php
				foreach ( $templates as $template ) :
					$selected = selected( $template['template_id'], $instance['template_id'] );
					?>
					<option value="<?php echo $template['template_id']; ?>" <?php echo $selected; ?> data-type="<?php echo esc_attr( $template['type'] ); ?>">
						<?php echo $template['title']; ?> (<?php echo $template['type']; ?>)
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
		$style = '';
		$url = add_query_arg(
			'elementor',
			'',
			get_permalink( $instance['template_id'] )
		);

		$template_type = Source_Local::get_template_type( $instance['template_id'] );

		if ( 'cmsmasters_entry' === $template_type ) {
			$style = ' style="display:none"';
		}
		?>
		<p class="elementor-edit-template"<?php echo $style; ?>>
			<a class="button button-small" href="<?php echo esc_url( $url ); ?>" target="_blank">
				<i class="fa fa-pencil"></i> <?php _e( 'Edit Template', 'cmsmasters-elementor' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['template_id'] = $new_instance['template_id'];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
