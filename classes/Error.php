<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The error class used to store and show messages
 */
class ESQ_Classes_Error {

	/** @var array of errors */
	public static $errors = array();

	/**
	 * Set a message in the errors list
	 *
	 * @param  $msg
	 *
	 * @return void
	 */
	public static function setMessage( $msg ) {
		self::$errors[] = $msg;
	}

	/**
	 * Get all the errors
	 *
	 * @return array
	 */
	public static function getMessages() {
		return self::$errors;
	}

	/**
	 * Check if there are errors
	 *
	 * @return bool
	 */
	public static function hasErrors() {
		return ! empty( self::$errors );
	}

}