<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


global $post;


$entries = get_entries();


?>


<div class="form-field">

	<?php if ( is_array( $entries ) && ! empty( $entries ) ) : ?>
		<label for="select-gutenberg-taxonomy-description">
			<?php _e( 'Description by means of Gutenberg', BETDPL_TEXTDOMAIN ); ?>
		</label>
		<select class="postform" name="<?php echo esc_attr( BETDPL_NAME ); ?>">
			<option value="">-</option>
			<?php foreach ( $entries as $entry ) : setup_postdata( $post = $entry ); ?>
				<option value="<?php echo get_the_ID(); ?>" >
					<?php echo esc_attr( get_the_title( $post ) ); ?>
				</option>
			<?php endforeach; wp_reset_postdata(); ?>
		</select>
		<p class="description">
			<?php
				printf(
					__( 'Add <a href="%s" target="_blank"> new description </a> or select an existing one from the list below', BETDPL_TEXTDOMAIN ),
					esc_url( admin_url( add_query_arg( [ 'post_type' => BETDPL_POST_TYPE_NAME ], 'post-new.php' ) ) )
				);
			?>
		</p>
	<?php else : ?>
		<p>
			<?php
				printf(
					__( 'No description has yet been created by means of Gutenberg. Add a <a href="%s" target="_blank">new description</a>.', BETDPL_TEXTDOMAIN ),
					esc_url( admin_url( add_query_arg( [ 'post_type' => BETDPL_POST_TYPE_NAME ], 'post-new.php' ) ) )
				);
			?>
		</p>
	<?php endif; ?>

</div>