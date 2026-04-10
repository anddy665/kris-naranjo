<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<script type="text/template" id="cmsmasters-local-font-template-header">
	<div class="cmsmasters-meta-box-header cmsmasters-font-face-header">
		<div title="<?php esc_attr_e( 'Local font family name', 'cmsmasters-elementor' ); ?>">
			<span class="cmsmasters-meta-box-header-meta"><?php esc_html_e( 'Font Family', 'cmsmasters-elementor' ); ?>: </span>
			<span class="cmsmasters-meta-box-header-meta-value">{{label}}</span>
		</div>
		<div title="<?php esc_attr_e( 'Formats of uploaded local font files', 'cmsmasters-elementor' ); ?>">
			<span class="cmsmasters-meta-box-header-meta"><?php esc_html_e( 'Formats', 'cmsmasters-elementor' ); ?>: </span>
			<span class="cmsmasters-meta-box-header-meta-value">{{formats}}</span>
		</div>
		<div class="cmsmasters-meta-box-header-meta-count">
			<span class="cmsmasters-meta-box-header-meta-value" title="{{count}} <?php esc_attr_e( 'styles', 'cmsmasters-elementor' ); ?>">{{count}}</span>
		</div>
	</div>
</script>

<script type="text/template" id="cmsmasters-local-font-template-duplicated-font">
	<div class="cmsmasters-local-font-duplicated-font"><?php
	esc_html_e(
		'The Local Font with such name already exists on your website. In order to avoid conflicts we recommend to use a unique Font family names. Please rename or remove this font to fix the problem.',
		'cmsmasters-elementor'
	);
	?></div>
</script>
