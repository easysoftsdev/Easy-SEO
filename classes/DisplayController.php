<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The class handles the media and views loading
 */
class ESQ_Classes_DisplayController {

	/** @var array the registered media */
	private static $media = array();

	/**
	 * Get the view content
	 *
	 * @param  string $view  The view block name
	 * @param  object $view  The controller instance
	 *
	 * @return false|string
	 */
	public function get_view( $block, $view ) {
		if ( file_exists( _ESQ_THEME_DIR_ . $block . '.php' ) ) {
			ob_start();
			include _ESQ_THEME_DIR_ . $block . '.php';

			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Load the media (css/js) for a given class
	 *
	 * @param  $className
	 *
	 * @return void
	 */
	public function loadMedia( $className ) {
		if ( isset( self::$media[ $className ] ) ) {
			return;
		}
		self::$media[ $className ] = true;

		//admin css
		if ( file_exists( _ESQ_ASSETS_DIR_ . 'css/easy-admin.css' ) ) {
			wp_enqueue_style( 'easyseo-admin', _ESQ_ASSETS_URL_ . 'css/easy-admin.css', array( 'dashicons' ), ESQ_VERSION );
		}
	}

	/**
	 * Output the view with media already loaded
	 *
	 * @param  string $view       The view block name
	 * @param  object $controller The controller instance
	 *
	 * @return string
	 */
	public function showView( $view, $controller ) {
		return $this->get_view( $view, $controller );
	}

}