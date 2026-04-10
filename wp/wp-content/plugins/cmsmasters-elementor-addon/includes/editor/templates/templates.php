<?php
namespace CmsmastersElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


?>
<script type="text/template" id="tmpl-cmsmasters-widgets-panel-element">
	<#
	var cmsmastersClass = '';

	if ( 'widget' === elType && '' !== widgetType ) {
		var widgetNameParts = widgetType.split( '-' );

		if ( 'cmsmasters' === widgetNameParts[ 0 ] ) {
			cmsmastersClass = ' cmsmasters-elementor-widget';
		}
	}
	#>
	<div class="elementor-element{{ cmsmastersClass }}">
		<# if ( false === obj.editable ) { #>
			<i class="eicon-lock"></i>
		<# } #>
		<div class="icon">
			<i class="{{ icon }}" aria-hidden="true"></i>
		</div>
		<div class="title-wrapper">
			<div class="title">{{{ title }}}</div>
		</div>
	</div>
</script>
