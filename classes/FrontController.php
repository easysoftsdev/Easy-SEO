<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The main class for controllers
 */
class ESQ_Classes_FrontController {

	/**
	 *
	 *
	 * @var object of the model class
	 */
	public $model;

	/**
	 *
	 *
	 * @var boolean
	 */
	public $flush = true;

	/**
	 *
	 *
	 * @var string name of the  class
	 */
	private $name;

	public function __construct() {
		// Load error class
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_Error' );

		/* get the name of the current class */
		$this->name = get_class( $this );

		/* load the model and hooks here for wordpress actions to take efect */
		/* create the model and view instances */
		$model_classname = str_replace( 'Controllers', 'Models', $this->name );
		if ( ESQ_Classes_ObjController::getClassPath( $model_classname ) ) {
			$this->model = ESQ_Classes_ObjController::getClass( $model_classname );
		}

		//IMPORTANT TO LOAD HOOKS HERE
		/* check if there is a hook defined in the controller clients class */
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_HookController' )->setHooks( $this );

		/* Load the Submit Actions Handler */
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_ActionController' );
		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_DisplayController' );
	}

	public function getClass() {
		return $this->name;
	}

	/**
	 * load sequence of classes
	 * Function called usualy when the controller is loaded in WP
	 *
	 * @return mixed
	 */
	public function init() {
		$class = ESQ_Classes_ObjController::getClassPath( $this->name );

		if ( ! $this->flush ) {
			return $this->get_view( $class['name'] );
		}

		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_DisplayController' )->loadMedia( $class['name'] );
		$this->show_view( $class['name'] );

		return false;
	}

	/**
	 * Get the view block
	 *
	 * @param string $view Class name
	 *
	 * @return mixed
	 */
	public function get_view( $view ) {
		return ESQ_Classes_ObjController::getClass( 'ESQ_Classes_DisplayController' )->get_view( $view, $this );
	}

	/**
	 * Deprecated since version 1.0.0
	 *
	 * @param $view
	 *
	 * @return mixed
	 */
	public function getView( $view ) {
		return $this->get_view( $view );
	}

	/**
	 * Show the view block
	 *
	 * @param string $view Class name
	 *
	 * @return void
	 */
	public function show_view( $view ) {
		$content = ESQ_Classes_ObjController::getClass( 'ESQ_Classes_DisplayController' )->get_view( $view, $this );

		//echo the file from /view directory
		//already escaped in the view
		//Contains HTML output
		echo $content;
	}

	/**
	 * Called as menu callback to show the block
	 */
	public function show() {
		$this->flush = true;

		echo $this->init();
	}

	/**
	 * initialize settings
	 * Called from index
	 *
	 * @return void
	 */
	public function runAdmin() {
		// show the admin menu and post actions
		ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Menu' );
	}

	/**
	 * Run from frontend
	 */
	public function runFrontend() {
		//Load Frontend controller
		ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Frontend' );
	}

	/**
	 * first function call for any class
	 */
	protected function action() {
	}

	/**
	 * This function will load the media in the header for each class
	 *
	 * @return void
	 */
	public function hookHead() {
	}

}