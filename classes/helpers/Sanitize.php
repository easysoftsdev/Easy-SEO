<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Handles the sanitization of the values
 */
class ESQ_Classes_Helpers_Sanitize {

	/**
	 * Sanitize the description
	 *
	 * @param  string $description
	 * @param  int    $max_length
	 *
	 * @return string
	 */
	public static function sanitizeDescription( $description, $max_length = 160 ) {
		//Clear the description
		$description = preg_replace( '/\r\n|\r|\n|[\t ]+/', ' ', $description );
		$description = trim( wp_strip_all_tags( $description ) );
		$description = html_entity_decode( $description, ENT_QUOTES, 'UTF-8' );
		$description = esc_attr( $description );

		//Truncate the description if it is longer than the max length
		if ( function_exists( 'mb_substr' ) ) {
			if ( mb_strlen( $description ) > $max_length ) {
				$description = mb_substr( $description, 0, $max_length - 3 ) . '...';
			}
		} else {
			if ( strlen( $description ) > $max_length ) {
				$description = substr( $description, 0, $max_length - 3 ) . '...';
			}
		}

		return $description;
	}

	/**
	 * Sanitize the title
	 *
	 * @param  string $title
	 * @param  int    $max_length
	 *
	 * @return string
	 */
	public static function sanitizeTitle( $title, $max_length = 60 ) {
		$title = preg_replace( '/\r\n|\r|\n|[\t ]+/', ' ', $title );
		$title = trim( wp_strip_all_tags( $title ) );
		$title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
		$title = esc_attr( $title );

		if ( function_exists( 'mb_substr' ) ) {
			if ( mb_strlen( $title ) > $max_length ) {
				$title = mb_substr( $title, 0, $max_length - 3 ) . '...';
			}
		} else {
			if ( strlen( $title ) > $max_length ) {
				$title = substr( $title, 0, $max_length - 3 ) . '...';
			}
		}

		return $title;
	}

	/**
	 * Sanitize a search value
	 *
	 * @param  string $value
	 *
	 * @return string
	 */
	public static function sanitizeSearch( $value ) {
		if ( $value <> '' ) {
			$value = preg_replace( "/[^a-zA-Z0-9\s\.\-_]/", "", $value );
		}

		return $value;
	}

}