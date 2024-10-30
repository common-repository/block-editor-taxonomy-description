<?php


namespace betdpl;


if ( ! defined( 'WPINC' ) ) {
	die;
}


class PublicPart {


	/**
	 * Заменяет стандартное описание терма на расширенное описание на Gutenberg
	 * @param     $value
	 * @param     $term_id
	 * @param     $context
	 * @return    string
	 * */
	public function render_description( $value, $term_id, $context = '' ) {
		$description_content = get_description_content( $term_id instanceof \WP_Term ? $term_id->term_id : $term_id );
		return ( empty( trim( $description_content ) ) ) ? $value : $description_content;
	}


}