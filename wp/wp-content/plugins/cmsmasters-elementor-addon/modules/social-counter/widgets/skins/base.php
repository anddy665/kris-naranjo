<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Skins;

use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Skin_Base as ElementorSkinBase;
use Elementor\Widget_Base;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon social-counter base skin.
 *
 * @since 1.0.0
 */
abstract class Base extends ElementorSkinBase {

	/**
	 * @var Social_Counter
	 *
	 * @since 1.0.0
	 */
	protected $parent;

	/**
	 * @since 1.0.0
	 */
	protected function _register_controls_actions() {
		add_action( 'cmsmasters_elementor/element/cmsmasters-social-counter/after_init_controls', array( $this, 'register_controls' ) );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
	}

	/**
	 * Get html attrs for social item.
	 *
	 * @since 1.0.0
	 *
	 * @return array $social_item_attrs
	 */
	protected function get_social_item_attrs() {
		$social_item = $this->parent->get_social_item();
		$social_item_attrs = array(
			'class' => array(
				'social-item',
			),
			'data-name' => esc_attr( $social_item::get_name() ),
		);

		$cache_exist = $social_item->cache_exist();
		$cache_checked_expire = $social_item->check_cache_expire();

		if ( ! $cache_checked_expire ) {
			$social_item_attrs['data-cache-id'] = esc_attr( $social_item->get_cache_id() );
			$social_item_attrs['class'][] = 'social-item--cache-expire';
		}

		if ( ! $cache_exist ) {
			$social_item_attrs['class'][] = 'social-item--cache-empty';
		}

		if ( ! $cache_exist || ! $cache_checked_expire ) {
			$social_item_attrs['class'][] = 'social-item--cache-fail';
		}

		return $social_item_attrs;
	}

	/**
	 * Render social.
	 *
	 * @since 1.0.0
	 */
	protected function render_social() {
		$social_item = $this->parent->get_social_item();
		$settings = $this->parent->get_settings();
		$social_link_attr = array(
			'target' => $settings['in_new_window'] ? '_blank' : '_self',
			'href' => esc_url( $social_item->get_profile_url() ),
			'class' => 'social-link',
			'title' => esc_attr( $social_item::get_label() ),
		);

		echo '<div ' . Utils::render_html_attributes( $this->get_social_item_attrs() ) . '>' .
			'<a ' . Utils::render_html_attributes( $social_link_attr ) . '>' .
				'<div class="social-link-outer">' .
					'<div class="social-link-inner">';
						$this->render_social_inner();
					echo '</div>' .
				'</div>' .
			'</a>' .
		'</div>';
	}

	/**
	 * Render number.
	 *
	 * @since 1.0.0
	 */
	protected function render_numbers() {
		?>
		<div class="social-numbers">
			<span>
				<?php echo esc_html( $this->parent->get_numbers_by_format() ); ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Render icon.
	 *
	 * @since 1.0.0
	 */
	protected function render_icon() {
		$icon = $this->parent->get_social_item()->get_icon();

		if ( ! empty( $icon ) ) : ?>
			<div class="social-icon">
				<?php CmsmastersUtils::render_icon( $icon ); ?>
			</div><?php
		endif;
	}

	/**
	 * Render title.
	 *
	 * @since 1.0.0
	 */
	protected function render_title() {
		$social_item = $this->parent->get_social_item();
		$title_type = $this->parent->get_settings_for_display( "title_type_{$social_item::get_name()}" );

		if ( ! $title_type ) {
			return;
		}

		?>
		<div class="social-title">
			<span>
				<?php echo esc_html( $this->parent->get_social_item()->get_title() ); ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Get order settings of types.
	 */
	protected function get_order() {
		return $this->parent->get_settings_for_display( 'order' );
	}

	/**
	 * Render each social item.
	 *
	 * @since 1.0.0
	 */
	protected function render_social_inner() {
		foreach ( $this->get_order() as $item ) {
			$this->render_social_item( $item );
		}
	}

	/**
	 * Render social item.
	 *
	 * @since 1.0.0
	 */
	protected function render_social_item( $item ) {
		switch ( $item ) {
			case 'icon':
				$this->render_icon();

				break;
			case 'title':
				$this->render_title();

				break;
			case 'numbers':
				$this->render_numbers();

				break;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function render() {
		if ( empty( $this->parent->get_settings( 'social_items' ) ) ) {
			return;
		}

		$this->parent->add_render_attribute(
			'main',
			array(
				'class' => array(
					'cmsmasters-social-counter',
					'cmsmasters-social-counter--' . esc_attr( $this->get_id() ),
				),
			)
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'main' ); ?>>
			<div class="cmsmasters-social-counter-inner">
				<?php $this->parent->loop_socials( function () {
					$this->render_social();
				} ); ?>
			</div>
		</div>
		<?php
	}
}
