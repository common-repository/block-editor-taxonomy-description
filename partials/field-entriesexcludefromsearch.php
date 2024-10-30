<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


$options = get_plugin_options();


?>


<div>
	<label>
		<input
			type="checkbox"
			name="<?php echo esc_attr( BETDPL_NAME . '[entriesexcludefromsearch]' ); ?>"
			<?php checked( true, $options[ 'entriesexcludefromsearch' ], true ); ?>
			value="on"
		/>
		<?php _e( 'Exclude this post type from site search', BETDPL_TEXTDOMAIN ); ?>
	</label>
</div>