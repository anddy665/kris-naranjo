<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents\Wordpress;

use CmsmastersElementor\Modules\TemplatePages\Traits\Panel_Categories;

use Elementor\Core\DocumentTypes\Page as WpPage;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Page extends WpPage {

	use Panel_Categories;

}
