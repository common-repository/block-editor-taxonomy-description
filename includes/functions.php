<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Получает список всех описаний
 * @param    $args    array               Список аргументов, в соответствии с которыми будет получен результат
 * @return            array|WP_Error
 * */
function get_entries( $args = [] ) {
	$entries_args = array_merge( [
		'numberposts' => -1,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'suppress_filters' => true,
	], $args );
	$entries_args[ 'post_type' ] = BETDPL_POST_TYPE_NAME;
	return get_posts( apply_filters( 'get_category_description_args', $entries_args ) );
}


/**
 * Получает опции плагина
 * @return   array
 * */
function get_plugin_options() {
	$result = get_option( BETDPL_NAME );
	if ( ! is_array( $result ) ) {
		$result = [];
	}
	$result = array_merge( [
		'taxonomynames'            => [ 'category' ],
		'entriesqueryable'         => false,
		'entriesdescription'       => '',
		'entriesexcludefromsearch' => true,
		'entriesshowinnavmenus'    => false,
		'entrieshasarchive'        => false,
		'entriesrewriteslug'       => BETDPL_POST_TYPE_NAME,
		'version'                  => BETDPL_VERSION,
	], $result );
	return $result;
}


/**
 * Возвращает идентификаторы таксономий с которыми работает плагин
 * @return   array
 * */
function get_plugin_taxonomy_names() {
	$options = get_plugin_options();
	return ( array_key_exists( 'taxonomynames', $options ) ) ? $options[ 'taxonomynames' ] : [];
}


/**
 * Получает идентификатор "описания" терма
 * @param    int        $term_id   идентификатор термина описание которого нужно получить
 * @param    bool       $single    true - возвращиет одно описание, false - все, которые прикреплены
 * @return   int
 * */
function get_description_id( $term_id, $single = true ) {
	$result = 0;
	if ( absint( $term_id ) ) {
		$term = get_term( $term_id, '', OBJECT, 'raw' );
		if ( $term instanceof \WP_Term ) {
			$entries = get_posts( [
				'numberposts' => $single ? 1 : -1,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => BETDPL_POST_TYPE_NAME,
				'suppress_filters' => false,
				'fields'      => 'ids',
				'tax_query'   => [
					'relation'    => 'AND',
					[
						'taxonomy' => $term->taxonomy,
						'field'    => 'term_id',
						'terms'    => $term->term_id,
						'operator' => 'IN',
						'include_children' => false,
					],
				],
			] );
			if ( is_array( $entries ) && ! is_wp_error( $entries ) ) {
				$result = $single ? array_shift( $entries ) : $entries;
			}
		}
	}
	return $result;
}


/**
 * Получает содержимое "описания" терма
 * @param    int        $term_id   идентификатор термина описание которого нужно получить
 * @return   string
 * */
function get_description_content( $term_id ) {
	global $post;
	$result = '';
	$post_id = get_description_id( $term_id );
	if ( $post_id ) {
		$entry = get_post( $post_id, OBJECT, 'raw' );
		if ( $entry instanceof \WP_Post ) {
			$post = $entry;
			setup_postdata( $post );
			$result = apply_filters( 'the_content', $post->post_content );
			wp_reset_postdata();
		}
	}
	return $result;
}


/**
 * Возвращает идентификаторы термов описания
 * @param    int    $post_id    идентификатор описания
 * @return   array
 * */
function get_decription_entry_terms_ids( $post_id = 0 ) {
	$result = [];
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( BETDPL_POST_TYPE_NAME == get_post_type( $post_id ) ) {
		$term_ids = wp_get_post_terms( get_the_ID(), get_plugin_taxonomy_names(), [ 'fields' => 'ids' ] );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			$result = $term_ids;
		}
	}
	return $result;
}
