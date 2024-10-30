<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


$options = get_plugin_options();


wp_editor( $options[ 'entriesdescription' ], 'entriesdescription', [
	'wpautop'       => 0,
	'media_buttons' => 0,
	'textarea_name' => BETDPL_NAME . '[entriesdescription]',
	'textarea_rows' => 5,
	'tabindex'      => null,
	'editor_css'    => '',
	'editor_class'  => 'form-editor',
	'teeny'         => 0,
	'dfw'           => 0,
	'tinymce'       => 1,
	'quicktags'     => 0,
	'drag_drop_upload' => false
] );