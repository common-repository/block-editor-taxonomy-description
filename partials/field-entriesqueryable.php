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
			name="<?php echo esc_attr( BETDPL_NAME . '[entriesqueryable]' ); ?>"
			<?php checked( true, $options[ 'entriesqueryable' ], true ); ?>
			value="on"
		/>
		<?php _e( 'Enable public viewing of posts of this type - this means that URL requests for this post type will be processed in the front-end', BETDPL_TEXTDOMAIN ); ?>
	</label>
</div>