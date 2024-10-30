<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


?>


<form action="options.php" method="POST">
	<?php
		settings_fields( BETDPL_NAME );
		do_settings_sections( BETDPL_NAME );
		submit_button();
	?>
</form>