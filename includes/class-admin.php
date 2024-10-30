<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


class AdminPart {


	/**
	 * Удаляет "панели" таксономий на странице редактирования поста "описания"
	 * */
	function remove_taxonomy_panel() {
		if ( BETDPL_POST_TYPE_NAME == get_post_type( get_the_ID() ) ) {
			wp_enqueue_script( 'wp-edit-post' );
			foreach ( get_plugin_taxonomy_names() as $taxonomy_name ) {
				wp_add_inline_script( 'wp-edit-post', 'wp.data.dispatch( \'core/edit-post\').removeEditorPanel( \'taxonomy-panel-' . $taxonomy_name . '\' );', 'after' );
			}
		}
	}


	/**
	 * WP 5.4.2. Cохранение опции экрана per_page. Нужно вызывать до события 'admin_menu'
	 * */
	public function save_per_page_new( $status, $option, $value ) {
		return ( int ) $value;
	}


	/**
	 * WP < 5.4.2. сохранение опции экрана per_page. Нужно вызывать рано до события 'admin_menu'
	 * */
	public function save_per_page_old( $status, $option, $value ) {
		return ( $option == 'logs_per_page' ) ? ( int ) $value : $status;
	}


	/**
	 * Добавляет дополнительные ссылки в меню плагина
	 * @param    string|array    $actions        Массив ссылок на действия плагина.
	 * @param    string          $plugin_file    Путь к файлу плагина относительно каталога плагинов
	 * @param    array           $plugin_data    Массив данных плагина
	 * @param    string          $context        Контекст плагина. По умолчанию это может включать  'all', 'active', 'inactive', 'recently_activated', 'upgrade', 'mustuse', 'dropins', and 'search'
	 * */
	public function add_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
		if ( 'all' == $context ) {
			$actions[ 'settings' ] = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( [ 'tab' => 'settings', 'page' => BETDPL_NAME ], admin_url( 'options-general.php' ) ) ), __( 'Settings', BETDPL_TEXTDOMAIN ) );
		}
		return $actions;
	}


	/**
	 * Прикрепляет описание к терму при его редактировании
	 * @param    init    $term_id    идентификатор терма
	 * @param    init    $tt_id      идентификатор терма
	 * @return   init                идентификатор терма
	 * */
	public function add_term_description_entry( $term_id, $tt_id ) {
		if ( current_user_can( 'edit_term', $term_id ) && array_key_exists( BETDPL_NAME, $_POST ) ) {
			$new_description_id = absint( $_POST[ BETDPL_NAME ] );
			$term = get_term( $term_id, '', OBJECT, 'raw' );
			if ( $term instanceof \WP_Term && $new_description_id ) {
				$old_description_id = get_description_id( $term->term_id );
				if ( $old_description_id != $new_description_id ) {
					if ( $old_description_id ) {
						wp_remove_object_terms( $old_description_id, $term->term_id, $term->taxonomy );
					}
					if ( $new_description_id ) {
						wp_set_object_terms( $new_description_id, $term->term_id, $term->taxonomy, true );
					}
				}
			}
		}
		return $term_id;
	}


	/**
	 * Выводи дополнительные поля при добавлении терма
	 * */
	public function render_add_term_fields() {
		include dirname( BETDPL_FILE ) . '/partials/term-add-form-field.php';
	}


	/**
	 * Выводи дополнительные поля для редактирования таксономии
	 * */
	public function render_edit_term_fields() {
		include dirname( BETDPL_FILE ) . '/partials/term-edit-form-field.php';
	}


	/**
	 * Добавляет страницу настроек
	 * @since      1.0.0
	 * */
	public function add_options_page() {
		$hook = add_options_page(
			__( 'Description of categories', BETDPL_TEXTDOMAIN ),
			__( 'Description of categories', BETDPL_TEXTDOMAIN ),
			'manage_options',
			BETDPL_NAME,
			[ $this, 'render_page' ]
		);
		add_action( 'load-' . $hook, [ $this, 'list_table_page_load' ] );
	}


	/**
	 * Подключаем файл с классом для создания списка постов-описаний 
	 * */
	public function list_table_page_load() {
		require_once dirname( BETDPL_FILE ) . '/includes/class-wp-list-table.php';
		require_once dirname( BETDPL_FILE ) . '/includes/class-entry-list-table.php';
		$GLOBALS[ 'Entry_List_Table' ] = new Entry_List_Table();
	}


	/**
	 * Регистрирует настройки плагина
	 * @since      1.0.0
	 * */
	public function register_options() {
		register_setting( BETDPL_NAME, BETDPL_NAME, [ $this, 'sanitize_setting_callback' ] );
		add_settings_section( 'taxonomy', __( 'Taxonomies', BETDPL_TEXTDOMAIN ), '', BETDPL_NAME );
		add_settings_section( 'entries', __( 'Description options', BETDPL_TEXTDOMAIN ), '', BETDPL_NAME );
		add_settings_field( 'taxonomynames', __( 'List', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'taxonomy', 'taxonomynames' );
		add_settings_field( 'entriesqueryable', __( 'Public viewing', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entriesqueryable' );
		add_settings_field( 'entriesdescription', __( 'Record type description', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entriesdescription' );
		add_settings_field( 'entriesexcludefromsearch', __( 'Exclude from search', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entriesexcludefromsearch' );
		add_settings_field( 'entriesshowinnavmenus', __( 'Show in menu', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entriesshowinnavmenus' );
		add_settings_field( 'entrieshasarchive', __( 'There is an archive', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entrieshasarchive' );
		add_settings_field( 'entriesrewriteslug', __( 'CNC prefix', BETDPL_TEXTDOMAIN ), [ $this, 'render_setting_field'], BETDPL_NAME, 'entries', 'entriesrewriteslug' );
	}


	/**
	 * Очистка данных
	 * @since    1.0.0
	 * @var      array    $options
	 */
	public function sanitize_setting_callback( $options ) {
		$result = [];
		foreach ( $options as $name => &$value ) {
			$new_value = null;
			switch ( $name ) {
				case 'entriesqueryable':
				case 'entriesexcludefromsearch':
				case 'entriesshowinnavmenus':
				case 'entrieshasarchive':
					$new_value = ( $value == 'on' ) ? true : false;
					break;
				case 'entriesdescription':
					$new_value = wp_kses_post( $value );
					break;
				case 'entriesrewriteslug':
					$new_value = sanitize_key( $value );
					break;
				case 'taxonomynames':
					if ( ! is_array( $value ) ) {
						$value = [ $value ];
					}
					$new_value = array_filter( $value, 'taxonomy_exists' );
					break;
			}
			if ( null != $new_value && ! empty( $new_value ) ) {
				$result[ $name ] = $new_value;
			}
		}
		return $result;
	}


	/**
	 * Формирует и вывоит html-код элементов формы настроек плагина
	 * @since    1.0.0
	 * @param    string    $id       идентификатор опции
	 */
	public function render_setting_field( $id ) {
		$name = BETDPL_NAME . '[' . $id . ']';
		$field_file_path = dirname( BETDPL_FILE ) . '/partials/field-' . $id . '.php';
		if ( file_exists( $field_file_path ) ) {
			include $field_file_path;
		} else {
			do_action( BETDPL_NAME . '_settings-field', $current_tab );
		}
	}


	/**
	 * Генерируем html код страницы настроек
	 */
	public function render_page() {
		global $post;
		$tabs = apply_filters( BETDPL_NAME . '_settings-tabs', [
			'list'      => __( 'Description list', BETDPL_TEXTDOMAIN ),
			'settings'  => __( 'Settings', BETDPL_TEXTDOMAIN ),
		] );
		$request_tab = isset( $_GET[ 'tab' ] ) ? sanitize_key( $_GET[ 'tab' ] ) : '';
		$current_tab = array_key_exists( $request_tab, $tabs ) ? $request_tab : array_keys( $tabs )[ 0 ];
		?>
			<div class="wrap">
				<h2><?php echo esc_html( get_admin_page_title() ) ?></h2>
				<nav class="nav-tab-wrapper wp-clearfix">
					<?php foreach ( $tabs as $slug => $label ) : ?>
						<a href="<?php echo esc_url( add_query_arg( [ 'tab' => $slug ] ) ); ?>" class="nav-tab <?php if ( $slug == $current_tab ) : echo 'nav-tab-active'; endif; ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
				<?php
					$tab_file_path = dirname( BETDPL_FILE ) . '/partials/tab-' . $current_tab . '.php';
					if ( file_exists( $tab_file_path ) ) {
						include $tab_file_path;
					} else {
						do_action( BETDPL_NAME . '_settings-form', $current_tab );
					}
				?>
			</div>
		<?php
	}


}