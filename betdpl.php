<?php


namespace betdpl;


/**
 * Block editor taxonomy description
 *
 * @link              http://chomovva.ru/
 * @package           betdpl
 *
 * @wordpress-plugin
 * Plugin Name:       Term description by means of block editor
 * Plugin URI:        https://github.com/chomovva/block-editor-taxonomy-description
 * Description:       The plugin adds the ability to create category descriptions using the block editor (Gutenberg).
 * Version:           1.0.5
 * Author:            chomovva
 * Author URI:        https://chomovva.ru/
 * Text Domain:       block-editor-taxonomy-description
 * Domain Path:       /languages
 * Network:           FALSE
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'BETDPL_VERSION', '1.0.2' );

define( 'BETDPL_TEXTDOMAIN', 'block-editor-taxonomy-description' );

if ( ! defined( 'BETDPL_FILE' ) ) {
	define( 'BETDPL_FILE', __FILE__ );
}


if ( ! defined( 'BETDPL_BASENAME' ) ) {
	define( 'BETDPL_BASENAME', plugin_basename( BETDPL_FILE ) );
}


if ( ! defined( 'BETDPL_NAME' ) ) {
	define( 'BETDPL_NAME', 'betdpl' );
}


if ( ! defined( 'BETDPL_POST_TYPE_NAME' ) ) {
	define( 'BETDPL_POST_TYPE_NAME', 'term_desc' );
}


/**
 * Код который выполнится при активации плагина
 * Скрипт находится в файле includes/class-activator.php
 */
function activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Activator::activate();
}


/**
 * Код который выполняется при деактивации плагина.
 * Скрипт находится в файле includes/class-deactivator.php
 */
function deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'betdpl\activate' );
register_deactivation_hook( __FILE__, 'betdpl\deactivate' );


/**
 * Подключение менеджер плагина в котором запускаются хуки, фильтры
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-main.php';

require plugin_dir_path( __FILE__ ) . 'includes/functions.php';


/**
 * Запуск плагина
 */
function run() {
	$plugin = new Main();
	$plugin->run();
}

run();