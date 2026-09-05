<?php defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' ); ?>
<?php
/*
 * Plugin Name: Easy SEO
 * Plugin URI: https://wordpress.org/plugins/
 * Description: A lightweight SEO plugin that brings you the essential on-page SEO features: meta titles and descriptions, focus keywords, canonical URLs, Open Graph and Twitter Cards, JSON-LD, and XML sitemaps.
 * Author: EasyDev
 * Author URI: https://example.com
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: easy-seo
 * Domain Path: /languages
 */

if ( ! defined( 'ESQ_VERSION' ) ) {
	/* SET THE CURRENT VERSION ABOVE AND BELOW */
	define( 'ESQ_VERSION', '1.0.0' );

	// Call config files
	try {
		include_once dirname( __FILE__ ) . '/config/config.php';
		include_once dirname( __FILE__ ) . '/debug/index.php';

		/* important to check the PHP version */
		// inport main classes
		include_once _ESQ_CLASSES_DIR_ . 'ObjController.php';

		// Load helpers
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_Helpers_Tools' );
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_Helpers_Sanitize' );
		// Load the Front and Block controller
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_FrontController' );
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_BlockController' );

		// Register the activation and deactivation hooks.
		register_activation_hook( __FILE__, array(
			ESQ_Classes_ObjController::getClass( 'ESQ_Classes_Helpers_Tools' ),
			'esq_activate'
		) );
		register_deactivation_hook( __FILE__, array(
			ESQ_Classes_ObjController::getClass( 'ESQ_Classes_Helpers_Tools' ),
			'esq_deactivate'
		) );

		if ( ESQ_Classes_Helpers_Tools::isBackedAdmin() ) {
			ESQ_Classes_ObjController::getClass( 'ESQ_Classes_FrontController' )->runAdmin();
		} else {
			ESQ_Classes_ObjController::getClass( 'ESQ_Classes_FrontController' )->runFrontend();
		}

	} catch ( Exception $e ) {
	}
}