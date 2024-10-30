<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


class InitPart {


	/**
	 * Прикрепляет таксономии к типу поста
	 * @since      1.0.0
	 * */
	public function register_taxonomies_for_post_type() {
		foreach ( get_plugin_taxonomy_names() as $taxonomy_name ) {
			register_taxonomy_for_object_type( $taxonomy_name, BETDPL_POST_TYPE_NAME );
		}
	}


	/**
	 * Подключает текстовый домен
	 * @since      1.0.0
	 * */
	public function load_textdomain() {
		load_plugin_textdomain( BETDPL_TEXTDOMAIN, false, dirname( plugin_basename( BETDPL_FILE ) ) . '/languages' ); 
	}


	/**
	 * Регистрирует тип записи "Услуга автосервиса"
	 * @since    1.0.0
	 */
	public function register_post_type() {
		$options = get_plugin_options();
		register_post_type( BETDPL_POST_TYPE_NAME, [
			'label'  => null,
			'labels' => [
				'name'               => __( 'Description of categories', BETDPL_TEXTDOMAIN ),
				'singular_name'      => __( 'Description of categories', BETDPL_TEXTDOMAIN ),
				'add_new'            => __( 'Add a note', BETDPL_TEXTDOMAIN ),
				'add_new_item'       => __( 'Add new entry', BETDPL_TEXTDOMAIN ),
				'edit_item'          => __( 'Edit entry', BETDPL_TEXTDOMAIN ),
				'new_item'           => __( 'New entry', BETDPL_TEXTDOMAIN ),
				'view_item'          => __( 'View the entry', BETDPL_TEXTDOMAIN ),
				'search_items'       => __( 'Search entry', BETDPL_TEXTDOMAIN ),
				'not_found'          => __( 'Not found', BETDPL_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No entries found in the trash', BETDPL_TEXTDOMAIN ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Category description', BETDPL_TEXTDOMAIN ),
			],
			'description'            => $options[ 'entriesdescription' ],
			'public'                 => false,
			'publicly_queryable'     => $options[ 'entriesqueryable' ],
			'exclude_from_search'    => $options[ 'entriesexcludefromsearch' ],
			'show_ui'                => true,
			'show_in_menu'           => false,
			'show_in_admin_bar'      => false,
			'show_in_nav_menus'      => $options[ 'entriesshowinnavmenus' ],
			'show_in_rest'           => true,
			'rest_base'              => null,
			'menu_position'          => '3.33',
			'menu_icon'              => 'dashicons-media-interactive',
			'hierarchical'           => false,
			'supports'               => [ 'title', 'editor' ],
			'taxonomies'             => [],
			'has_archive'            => $options[ 'entrieshasarchive' ],
			'rewrite'                => [
				'slug'                  => empty( $options[ 'entriesrewriteslug' ] ) ? BETDPL_POST_TYPE_NAME : $options[ 'entriesrewriteslug' ],
			],
			'query_var'              => false,
		] );
	}


}