<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
    die;
}



/**
 * Запускается при деактивации плагина
 *
 * В этом классе находится весь код, который необходимый при деактивации плагина.
 *
 * @since      1.0.0
 * @package    betdpl
 * @subpackage betdpl/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Deactivator {

	/**
	 * Действия при деактивации
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( BETDPL_NAME );
	}

}
