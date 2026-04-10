<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Ticker_Skins;

use CmsmastersElementor\Classes\Separator;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Marquee extends Base {

	public $separator;

	public function get_id() {
		return 'marquee';
	}

	public function get_title() {
		return __( 'Marquee', 'cmsmasters-elementor' );
	}

	public function _register_controls_actions() {
		$this->separator = new Separator( $this->parent, array(
			'name' => 'marquee_separator',
			'selector' => '{{WRAPPER}} .cmsmasters-ticker-posts-marquee-inner',
			'skin' => $this,
		) );

		parent::_register_controls_actions();
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->start_section_marquee();
		$this->add_separator();
	}

	private function add_separator() {
		$this->parent->start_injection( array(
			'of' => 'section_post',
			'type' => 'section',
		) );

		$this->separator->add_controls();

		$this->parent->end_injection();
	}

	private function start_section_marquee() {
		$this->start_controls_section(
			'section_marquee',
			array(
				'label' => __( 'Marquee', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (' . __( 'seconds', 'cmsmasters-elementor' ) . ')',
				'type' => Controls_Manager::NUMBER,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ticker-posts-marquee' => 'animation-duration: {{VALUE}}s',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render_post() {
		parent::render_post();

		if ( $this->parent->get_query()->current_post < ( $this->parent->get_query()->post_count - 1 ) ) {
			$this->separator->render();
		}
	}

	protected function render_posts_loop() {
		?>
		<div class="cmsmasters-ticker-posts-marquee-wrap">
			<div class="cmsmasters-ticker-posts-marquee">
				<div class="cmsmasters-ticker-posts-marquee-inner">
					<?php parent::render_posts_loop(); ?>
				</div>
			</div>
		</div>
		<?php
	}

}
