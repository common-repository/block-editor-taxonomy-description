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
			name="<?php echo esc_attr( BETDPL_NAME . '[entrieshasarchive]' ); ?>"
			<?php checked( true, $options[ 'entrieshasarchive' ], true ); ?>
			value="on"
		/>
		<?php _e( 'Enable support for archive pages for this post type', BETDPL_TEXTDOMAIN ); ?>
	</label>
</div>