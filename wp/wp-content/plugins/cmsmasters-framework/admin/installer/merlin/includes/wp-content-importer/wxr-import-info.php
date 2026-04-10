<?php
namespace CmsmastersFramework\Admin\Installer\Merlin\Includes\WpContentImporter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WXR_Import_Info {
	public $home;
	public $siteurl;
	public $title;
	public $users = array();
	public $post_count = 0;
	public $media_count = 0;
	public $comment_count = 0;
	public $term_count = 0;
	public $generator = '';
	public $version;
}