<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Запускается при активации плагина
 *
 * @link       http://chomovva.ru
 * @since      1.0.0
 *
 * @package    betdpl
 * @subpackage betdpl/includes
 */

/**
 * Запускается при активации плагина.
 * В этом классе находится весь код, который необходимый при активации плагина.
 * @since      1.0.0
 * @package    betdpl
 * @subpackage betdpl/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Activator {

	/**
	 * Действия которые необходимо выполнить при активации
	 * @since    1.0.0
	 */
	public static function activate() {
		$options = [
			'taxonomynames'            => [ 'category' ],
			'entriesqueryable'         => false,
			'entriesdescription'       => '',
			'entriesexcludefromsearch' => true,
			'entriesshowinnavmenus'    => false,
			'entrieshasarchive'        => false,
			'entriesrewriteslug'       => BETDPL_POST_TYPE_NAME,
			'version'                  => BETDPL_VERSION,
		];
		update_option( BETDPL_NAME, $options );
	}


}