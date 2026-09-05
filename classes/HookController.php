<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The class handles the actions in WP
 */
class ESQ_Classes_HookController {

	/**
	 * @var array the WP actions list from admin
	 */
	private $admin_hooks = array();
	private $front_hooks = array();
	private $block_hooks = array();

	public function __construct() {
		$this->admin_hooks = array(
			'init'        => 'init',
			'head'        => 'admin_head',
			'footer'      => 'admin_footer',
			'menu'        => 'admin_menu',
			'networkMenu' => 'network_admin_menu',
			'notices'     => 'admin_notices',
		);

		//called in frontend
		$this->front_hooks = array(
			// --
			'init'        => 'init',
			'preload'     => 'template_redirect',
			'frontinit'   => 'init',
			'fronthead'   => 'wp_head',
			'frontfooter' => 'wp_footer',
		);

		$this->block_hooks = array( 'getContent' => 'getContent' );
	}

	/**
	 * Calls the specified action in WP
	 *
	 * @param object $instance The parent class instance
	 *
	 * @return void
	 */
	public function setHooks( $instance ) {
		if ( is_admin() || is_network_admin() ) {
			$this->setAdminHooks( $instance );
		} else {
			$this->setFrontHooks( $instance );
		}
	}

	/**
	 * Calls the specified action in WP
	 *
	 * @param object $instance The parent class instance
	 *
	 * @return void
	 */
	public function setAdminHooks( $instance ) {

		/* for each admin action check if is defined in class and call it */
		foreach ( $this->admin_hooks as $hook => $value ) {

			if ( is_callable( array( $instance, 'hook' . ucfirst( $hook ) ) ) ) {
				//call the WP add_action function
				add_action( $value, array( $instance, 'hook' . ucfirst( $hook ) ), 5 );
			}
		}
	}

	/**
	 * Calls the specified action in WP
	 *
	 * @param object $instance The parent class instance
	 *
	 * @return void
	 */
	public function setFrontHooks( $instance ) {
		/* for each admin action check if is defined in class and call it */
		foreach ( $this->front_hooks as $hook => $value ) {
			if ( is_callable( array( $instance, 'hook' . ucfirst( $hook ) ) ) ) {
				//call the WP add_action function
				add_action( $value, array( $instance, 'hook' . ucfirst( $hook ) ), 5 );
			}
		}
	}

	/**
	 * Calls the specified action in WP
	 *
	 * @param string $action
	 * @param array $callback Contains the class name or object and the callback function
	 *
	 * @return void
	 */
	public function setAction( $action, $obj, $callback ) {

		/* calls the custom action function from WP */
		add_action( $action, array( $obj, $callback ), 10 );
	}

	/**
	 * Calls the specified action in WP
	 *
	 * @param object $instance The parent class instance
	 *
	 * @return void
	 */
	public function setBlockHooks( $instance ) {
		$param_arr = array();

		/* for each admin action check if is defined in class and call it */
		foreach ( $this->block_hooks as $hook => $value ) {
			if ( is_callable( array( $instance, 'hook' . ucfirst( $hook ) ) ) ) {
				//call the WP add_action function
				call_user_func_array( array( $instance, 'hook' . ucfirst( $hook ) ), $param_arr );
			}
		}
	}

}