<?php
namespace CmsmastersElementor\Modules\Media;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor media module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	const YOUTUBE_VIDEO_URL = 'https://youtu.be/1BI-5EPZp88';
	const YOUTUBE_ALTERNATE_VIDEO_URL = 'https://www.youtube.com/watch?v=XHOmBV4js_E';
	const VIMEO_VIDEO_URL = 'https://vimeo.com/507989553';
	const DAILYMOTION_VIDEO_URL = 'https://www.dailymotion.com/video/k5ndwZP5hyHfQswCFRs';
	const FACEBOOK_VIDEO_URL = 'https://www.facebook.com/elemntor/videos/1683988961912056/';
	const TWITCH_VIDEO_URL = 'https://www.twitch.tv/monstercat';
	const SELF_HOSTED_VIDEO_URL = 'https://cmsmasters.net/files/yourway/yourway-promo.mp4';

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters_media';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array(
			'Video',
			'Video_Stream',
			'Video_Slider',
			'Video_Playlist',
			'Audio',
			'Audio_Playlist',
		);
	}

}
