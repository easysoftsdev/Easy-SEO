<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Controllers_SeoSettings extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_SeoSettings */
	public $model;

	/** @var string */
	public $tab = 'general';

	/** @var array */
	public $tabs = array();

	public function init() {

		//Get the current tab
		$this->tab = ESQ_Classes_Helpers_Tools::getValue( 'tab', 'general' );
		$this->tabs = $this->model->getTabs();

		//Show the settings view
		$this->show_view( 'Settings' );
	}

	/**
	 * Save the settings from the form
	 * Called from the ActionController
	 *
	 * @return array
	 */
	public function saveSettings() {
		$response = array( 'success' => false );

		//Check the user capabilities
		if ( ! ESQ_Classes_Helpers_Tools::userCan( 'manage_options' ) ) {
			$response['message'] = esc_html__( 'You do not have permission to save the settings.', 'easy-seo' );

			return $response;
		}

		//Check the nonce
		if ( ! isset( $_POST['esq_nonce'] ) || ! wp_verify_nonce( $_POST['esq_nonce'], 'esq_save_settings' ) ) {
			$response['message'] = esc_html__( 'Invalid nonce. Please refresh the page and try again.', 'easy-seo' );

			return $response;
		}

		//Sanitize and save the settings
		$options = $this->model->sanitizeSettings( ESQ_Classes_Helpers_Tools::getValue( 'esq', array() ) );
		ESQ_Classes_Helpers_Tools::$options = $options;
		ESQ_Classes_Helpers_Tools::saveOptions();

		$response['success'] = true;
		$response['message'] = esc_html__( 'Settings saved successfully.', 'easy-seo' );

		return $response;
	}

}