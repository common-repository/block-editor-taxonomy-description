<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Файл, который определяет основной класс плагина
 *
 * @link       https://chomovva.ru
 * @since      1.0.0
 *
 * @package    betdpl
 * @subpackage betdpl/includes
 */


/**
 * Основной класс плагина, который запускает все хуки и фильры
 * @since      1.0.0
 * @package    betdpl
 * @subpackage betdpl/includes
 * @author     chomovva <chomovva@gmail.com>
 */
class Main {


	/**
	 * Массив хуков зарегистрирвоанных в WordPress
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    Хуки, зарегистрированные в WordPress, запускаются при загрузке плагина.
	 */
	protected $actions;


	/**
	 * Массив фильтров зарегистрирвоанных в WordPress
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    Фильтры, зарегистрированные в WordPress, запускаются при загрузке плагина.
	 */
	protected $filters;


	/**
	 * Инициализация переменных плагина, подключение файлов.
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->actions = [];
		$this->filters = [];
		$this->load_init_dependencies();
		$this->define_init_hooks();
		if ( is_admin() ) {
			$this->load_admin_dependencies();
			$this->define_admin_hooks();
		} else {
			$this->load_public_dependencies();
			$this->define_public_hooks();
		}
	}

	/**
	 * Подключает файлы с "зависимостями"
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_init_dependencies() {
		require_once dirname( BETDPL_FILE ) . '/includes/class-init.php';
	}


	/**
	 * Подключает файлы с "зависимостями" консоли сайта
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_admin_dependencies() {
		require_once dirname( BETDPL_FILE ) . '/includes/class-admin.php';
	}


	/**
	 * Подключает файлы с "зависимостями" для публичной части сайта
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_public_dependencies() {
		require_once dirname( BETDPL_FILE ) . '/includes/class-public.php';
	}


	/**
	 * Добавляет в коллекцию хуки и фильтры необходимые для работы плагина
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_init_hooks() {
		$init_part = new InitPart();
		$this->actions[] = $this->add( 'plugins_loaded', $init_part, 'load_textdomain', 10, 0 );
		$this->actions[] = $this->add( 'init', $init_part, 'register_post_type', 10, 0 );
		$this->actions[] = $this->add( 'init', $init_part, 'register_taxonomies_for_post_type', 10, 0 );
	}


	/**
	 * Регистрация хуков и фильтров для админ части плагина
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$admin_part = new AdminPart();
		$this->actions[] = $this->add( 'admin_enqueue_scripts', $admin_part, 'remove_taxonomy_panel', 10, 0 );
		$this->actions[] = $this->add( 'admin_menu', $admin_part, 'add_options_page', 10, 0 );
		$this->actions[] = $this->add( 'admin_init', $admin_part, 'register_options', 10, 0 );
		$this->filters[] = $this->add( 'set_screen_option_' . 'lisense_table_per_page', $admin_part, 'save_per_page_new', 10, 3 );
		$this->filters[] = $this->add( 'set-screen-option', $admin_part, 'save_per_page_old', 10, 3 );
		$this->filters[] = $this->add( 'plugin_action_links_' . BETDPL_BASENAME, $admin_part, 'add_plugin_action_links', 10, 4 );
		foreach ( get_plugin_taxonomy_names() as $taxonomy_name ) {
			$this->actions[] = $this->add( $taxonomy_name . '_add_form_fields', $admin_part, 'render_add_term_fields', 10, 0 );
			$this->actions[] = $this->add( $taxonomy_name . '_edit_form_fields', $admin_part, 'render_edit_term_fields', 10, 0 );
			$this->actions[] = $this->add( 'create_' . $taxonomy_name, $admin_part, 'add_term_description_entry', 10, 2 );
			$this->actions[] = $this->add( 'edited_' . $taxonomy_name, $admin_part, 'add_term_description_entry', 10, 2 );
		}
	} 


	/**
	 * Регистрация хуков и фильтров для публично части плагина
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$public_part = new PublicPart();
		foreach ( get_plugin_taxonomy_names() as $taxonomy_name ) {
			$this->filters[] = $this->add( $taxonomy_name . '_description', $public_part, 'render_description', 10, 3 );
		}
	}


	/**
	 * Запск загрузчика для регистрации хукой, фильтров и шорткодов в WordPress
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
		}
	}


	/**
	 * Добавляет фильтры и хуки в коллекцию
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hook, $component, $callback, $priority, $accepted_args ) {
		return [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		];
	}


}