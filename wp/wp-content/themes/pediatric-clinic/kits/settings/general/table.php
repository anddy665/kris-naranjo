<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Table settings.
 */
class Table extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'table';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Table', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		$toggle_name = self::get_toggle_name();

		return parent::get_control_id_prefix() . "_{$toggle_name}";
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_control(
			'notice',
			array(
				'raw' => esc_html__( 'Used in: default table, Gutenberg editor.', 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$parts = array(
			'tbody' => '',
			'thead' => esc_html__( 'Header', 'pediatric-clinic' ),
			'tfoot' => esc_html__( 'Footer', 'pediatric-clinic' ),
		);

		foreach ( $parts as $part => $label ) {
			if ( 'tbody' === $part ) {
				$subkey = '';
				$var_subkey = '';
			} else {
				$subkey = $part . '_';
				$var_subkey = $part;

				$this->add_control(
					"{$subkey}heading_control",
					array(
						'label' => $label,
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					)
				);
			}

			$this->add_var_group_control( $var_subkey, self::VAR_TYPOGRAPHY );

			$this->add_control(
				"{$subkey}colors_heading_control",
				array(
					'label' => esc_html__( 'Colors', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				"{$subkey}colors_text",
				array(
					'label' => esc_html__( 'Text', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}colors_text" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"{$subkey}colors_link",
				array(
					'label' => esc_html__( 'Link', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}colors_link" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"{$subkey}colors_hover",
				array(
					'label' => esc_html__( 'Link Hover', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}colors_hover" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"{$subkey}colors_bg",
				array(
					'label' => esc_html__( 'Background', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}colors_bg" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"{$subkey}colors_bd",
				array(
					'label' => esc_html__( 'Border', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}colors_bd" ) . ': {{VALUE}};',
					),
				)
			);

			if ( 'tbody' === $part ) {
				$this->add_responsive_control(
					"{$subkey}padding",
					array(
						'label' => esc_html__( 'Padding', 'pediatric-clinic' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( '', "{$subkey}padding_top" ) . ': {{TOP}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( '', "{$subkey}padding_right" ) . ': {{RIGHT}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( '', "{$subkey}padding_bottom" ) . ': {{BOTTOM}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( '', "{$subkey}padding_left" ) . ': {{LEFT}}{{UNIT}};',
						),
					)
				);
			}
		}
	}

}
