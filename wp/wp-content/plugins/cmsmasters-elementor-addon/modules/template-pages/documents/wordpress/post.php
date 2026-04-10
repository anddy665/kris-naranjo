<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents\Wordpress;

use CmsmastersElementor\Modules\TemplatePages\Traits\Panel_Categories;

use Elementor\Core\DocumentTypes\Post as WpPost;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post extends WpPost {

	use Panel_Categories;

}
