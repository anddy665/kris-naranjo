<?php
namespace PediatricClinicSpace\Modules;

use PediatricClinicSpace\Core\Utils\Utils;

use Elementor\Plugin;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Page_Preloader handler class is responsible for page preloader methods.
 */
final class Page_Preloader {

	/**
	 * Page_Preloader constructor.
	 */
	public function __construct() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', function () {
			if ( ! $this->should_render() ) {
				return;
			}

			wp_enqueue_style( 'e-animations' );
		} );

		add_action( 'wp_body_open', function () {
			if ( ! $this->should_render() ) {
				return;
			}

			echo '<div class="cmsmasters-page-preloader">';

				$this->render_preloader();
			
			echo '</div>';
		}, 10, 2 );
	}

	/**
	 * Determine if the Page Preloader element should be rendered.
	 */
	private function should_render() {
		if (
			'yes' !== Utils::get_kit_option( 'cmsmasters_page_preloader_visibility', '' ) ||
			Plugin::$instance->frontend->is_static_render_mode()
		) {
			return false;
		}

		return true;
	}

	private function render_preloader() {
		$preloader_type = Utils::get_kit_option( 'cmsmasters_page_preloader_preloader_type' );

		if ( empty( $preloader_type ) ) {
			return;
		}

		$out = '';

		if ( 'icon' === $preloader_type ) {
			$icon = Utils::get_kit_option( 'cmsmasters_page_preloader_preloader_icon', array(
				'value' => 'fas fa-spinner',
				'library' => 'fa-solid',
			) );
	
			if ( empty( $icon ) ) {
				return;
			}

			ob_start();

			Icons_Manager::render_icon( $icon );

			$out .= ob_get_clean();
		}

		if ( 'image' === $preloader_type ) {
			$image = Utils::get_kit_option( 'cmsmasters_page_preloader_preloader_image', array(
				'id' => '',
				'url' => '',
			) );

			if ( empty( $image['url'] ) ) {
				return;
			}

			$out .= '<img src="' . $image['url'] . '">';
		}

		if ( 'animation' === $preloader_type ) {
			$animation_type = Utils::get_kit_option( 'cmsmasters_page_preloader_preloader_animation_type', 'circle' );

			$out .= '<span class="cmsmasters-page-preloader__preloader-animation-element" type="' . esc_attr( $animation_type ) . '">';

				if (
					'bouncing-dots' === $animation_type ||
					'pulsing-dots' === $animation_type
				) {
					$out .= '<span></span>
					<span></span>
					<span></span>
					<span></span>';
				}

			echo '</span>';
		}

		wp_enqueue_style( 'animate' );

		echo '<div class="cmsmasters-page-preloader__preloader">
			<div class="cmsmasters-page-preloader__preloader-' . esc_attr( $preloader_type ) . '">' .
				$out .
			'</div>
		</div>';
	}

}
