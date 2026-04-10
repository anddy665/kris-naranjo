<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<script type="text/template" id="cmsmasters-local-icons-template-header">
	<div class="cmsmasters-meta-box-header cmsmasters-icons-set-header">
		<div title="<?php esc_attr_e( 'Local icons font family name', 'cmsmasters-elementor' ); ?>">
			<span class="cmsmasters-meta-box-header-meta"><?php _e( 'Font Family', 'cmsmasters-elementor' ); ?>: </span>
			<span class="cmsmasters-meta-box-header-meta-value">{{name}}</span></div>
		<div title="<?php esc_attr_e( 'Local icons font CSS class prefix', 'cmsmasters-elementor' ); ?>">
			<span class="cmsmasters-meta-box-header-meta"><?php _e( 'Class Prefix', 'cmsmasters-elementor' ); ?>: </span>
			<span class="cmsmasters-meta-box-header-meta-value">{{prefix}}</span>
		</div>
		<div class="cmsmasters-meta-box-header-meta-count">
			<span class="cmsmasters-meta-box-header-meta-value" title="{{count}} <?php esc_attr_e( 'icons', 'cmsmasters-elementor' ); ?>">{{count}}</span>
		</div>
	</div>
</script>

<script type="text/template" id="cmsmasters-local-icons-template-duplicated-prefix">
	<div class="cmsmasters-icons-set-duplicated-prefix">
		<?php _e( 'The Local Icons Set prefix already exists on your website. In order to avoid conflicts we recommend to use a unique prefixes. Please remove this icons set and upload it again with unique prefix to fix the possible problem.', 'cmsmasters-elementor' ); ?>
	</div>
</script>
