<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<p>
	<a
		href="<?php echo esc_url( admin_url( add_query_arg( [ 'post_type' => BETDPL_POST_TYPE_NAME ], 'post-new.php' ) ) ); ?>"
		target="_blank"
		class="button button-primary"
	>
		<?php _e( 'Add new description', BETDPL_TEXTDOMAIN ); ?>
	</a>
</p>

<form method="POST">
	<?php $GLOBALS[ 'Entry_List_Table' ]->display(); ?>
</form>';