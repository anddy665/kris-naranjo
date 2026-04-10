<?php
namespace CmsmastersElementor\Controls;

use CmsmastersElementor\Controls_Manager;

use Elementor\Control_Animation;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Control_Custom_Animation extends Control_Animation {

	public function get_type() {
		return Controls_Manager::CUSTOM_ANIMATION;
	}

	public static function get_default_animations() {
		$animations = array(
			'Fading' => array(
				'fadeIn' => 'Fade In',
				'fadeInDown' => 'Fade In Down',
				'fadeInLeft' => 'Fade In Left',
				'fadeInRight' => 'Fade In Right',
				'fadeInUp' => 'Fade In Up',
			),
			'Zooming' => array(
				'zoomIn' => 'Zoom In',
				'zoomInDown' => 'Zoom In Down',
				'zoomInLeft' => 'Zoom In Left',
				'zoomInRight' => 'Zoom In Right',
				'zoomInUp' => 'Zoom In Up',
			),
			'Bouncing' => array(
				'bounceIn' => 'Bounce In',
				'bounceInDown' => 'Bounce In Down',
				'bounceInLeft' => 'Bounce In Left',
				'bounceInRight' => 'Bounce In Right',
				'bounceInUp' => 'Bounce In Up',
			),
			'Sliding' => array(
				'slideInDown' => 'Slide In Down',
				'slideInLeft' => 'Slide In Left',
				'slideInRight' => 'Slide In Right',
				'slideInUp' => 'Slide In Up',
			),
			'Rotating' => array(
				'rotateIn' => 'Rotate In',
				'rotateInDownLeft' => 'Rotate In Down Left',
				'rotateInDownRight' => 'Rotate In Down Right',
				'rotateInUpLeft' => 'Rotate In Up Left',
				'rotateInUpRight' => 'Rotate In Up Right',
			),
			'Attention Seekers' => array(
				'bounce' => 'Bounce',
				'flash' => 'Flash',
				'pulse' => 'Pulse',
				'rubberBand' => 'Rubber Band',
				'shake' => 'Shake',
				'headShake' => 'Head Shake',
				'swing' => 'Swing',
				'tada' => 'Tada',
				'wobble' => 'Wobble',
				'jello' => 'Jello',
			),
			'Light Speed' => array(
				'lightSpeedIn' => 'Light Speed In',
			),
			'Specials' => array(
				'rollIn' => 'Roll In',
			),
		);

		$custom_animations = self::get_custom_animations();

		return array_merge( $custom_animations, $animations );
	}

	public static function get_custom_animations() {
		$custom_animations = array(
			'Primary' => array(
				'typing' => 'Typing',
				'slide' => 'SlideUp',
			),
			'Flip' => array(
				'flip' => 'flip',
				'flipInX' => 'flipInX',
				'flipInY' => 'flipInY',
			),
		);

		return apply_filters( 'cmsmasters_elementor/includes/controls/custom_animations', $custom_animations );
	}
}
