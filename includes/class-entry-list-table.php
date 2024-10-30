<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
    die;
}


/**
 * Класс для создания списка постов-описаний. Основан на классе WordPress WP_List_table
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

class Entry_List_Table extends WP_List_Table {


	protected $taxonomy_names;


	function __construct() {
		parent::__construct( [
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false,
		] );

		$this->taxonomy_names = get_plugin_taxonomy_names();

		$this->bulk_action_handler();

		add_screen_option( 'per_page', array(
			'label'   => __( 'Show on page', BETDPL_TEXTDOMAIN ),
			'default' => 20,
			'option'  => 'logs_per_page',
		) );

		$this->prepare_items();

		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );
	}

	/**
	 * Получает значения "строк"
	 * */
	function prepare_items(){
		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;
		$cur_page = ( int ) $this->get_pagenum();
		$entries_args = [
			'numberposts' => $per_page,
			'offset'      => $per_page * ( $cur_page - 1 ),
		];
		$entries = get_entries( $entries_args );
		$this->set_pagination_args( array(
			'total_items' => wp_count_posts( BETDPL_POST_TYPE_NAME )->publish,
			'per_page'    => $per_page,
		) );
		$this->items = is_array( $entries ) ? $entries : [];
	}


	/**
	 * Регистрирует колонки таблицы
	 * */
	function get_columns(){
		return array(
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Heading', BETDPL_TEXTDOMAIN ),
			'terms'  => __( 'Thermes', BETDPL_TEXTDOMAIN ),
		);
	}


	/**
	 * Добавляет сортируемые колонки
	 * */
	function get_sortable_columns(){
		return array(
			'title' => [ 'name', 'desc' ],
		);
	}


	/**
	 * Добавляет групповые действия
	 * */
	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', BETDPL_TEXTDOMAIN ),
		);
	}


	/**
	 *  Вывод стилей таблицы
	 * */
	static function _list_table_css(){
		?>
			<style>
				table.logs .column-title { width: 60%; }
				table.logs .column-terms { width: 40%; }
			</style>
		<?php
	}


	/**
	 *  вывод каждой ячейки таблицы...
	 * */
	function column_default( $item, $colname ) {

		$actions = [];

		if ( 'title' === $colname ) {
			return esc_html( $item->post_title ) . $this->row_actions( array_merge( $actions, [
				'edit'  => sprintf( '<a href="%s">%s</a>', get_edit_post_link( $item->ID, 'display' ), __( 'Edit', BETDPL_TEXTDOMAIN ) ),
				'trash' => sprintf( '<a href="%s" onclick="return confirm(\'%s\');">%s</a>', get_delete_post_link( $item->ID, '', true ), __( 'Are you sure?', BETDPL_TEXTDOMAIN ), __( 'Delete', BETDPL_TEXTDOMAIN ) ),
			] ) );
		} elseif ( 'terms' === $colname ) {
			return wp_sprintf( '%l', array_map( function ( $term ) {
				return sprintf( '<a href="%s" target="_blank">%s</a>', get_term_link( $term->term_id, $term->taxonomy ), $term->name );
			}, wp_get_object_terms( $item->ID, $this->taxonomy_names, [] ) ) );
		}

	}


	/**
	 *  заполнение колонки cb
	 * */
	function column_cb( $item ){
		echo '<input type="checkbox" name="licids[]" id="cb-select-'. esc_attr( $item->ID ) .'" value="'. esc_attr( $item->ID ) .'" />';
	}


	/**
	 * Выполняет групповые действия
	 * */
	private function bulk_action_handler(){
		if ( empty( $_POST[ 'licids' ] ) || empty( $_POST[ '_wpnonce' ] ) || ! is_array( $_POST[ 'licids' ] ) ) return;
		if ( ! $action = $this->current_action() ) return;
		if ( ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'bulk-' . $this->_args[ 'plural' ] ) )
			wp_die( 'nonce error' );
		foreach ( array_filter( array_map( 'absint', $_POST[ 'licids' ] ) ) as $post_id ) {
			if ( BETDPL_POST_TYPE_NAME == get_post_type( $post_id ) ) {
				wp_delete_post( $post_id, true );
			}
		}
	}


}