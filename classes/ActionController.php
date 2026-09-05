<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The class handles the submit actions
 */
class ESQ_Classes_ActionController {

	/**
	 * The nonce for the actions
	 *
	 * @var string
	 */
	public static $nonce_id = 'esq_nonce';

	public function __construct() {
		//Handle the settings save in admin_init when the current user is set up
		add_action( 'admin_init', array( $this, 'handleSettingsSave' ), 10 );
	}

	/**
	 * Handle the settings save request
	 *
	 * @return void
	 */
	public function handleSettingsSave() {
		if ( ESQ_Classes_Helpers_Tools::getValue( 'action' ) === 'esq_save_settings' ) {
			$response = ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_SeoSettings' )->saveSettings();
			if ( is_array( $response ) ) {
				echo wp_json_encode( $response );
			}
			die();
		}
	}

}