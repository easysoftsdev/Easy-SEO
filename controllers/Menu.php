<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Easy SEO Menu Controller
 */
class ESQ_Controllers_Menu extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_Menu */
	public $model;

	/** @var string Post type from WP_Post */
	public $post_type;

	/**
	 * Menu Construct
	 */
	public function initMenu() {

		if ( ! is_network_admin() ) {

			add_action( 'do_meta_boxes', array( $this, 'addMetabox' ) );

			//Check if we are on the Easy SEO page
			if ( ESQ_Classes_Helpers_Tools::getIsset( 'page' ) ) {
				$page = ESQ_Classes_Helpers_Tools::getValue( 'page' );

				if ( strpos( $page, 'esq_' ) === 0 ) {
					add_filter( 'admin_body_class', array( $this, 'addEsqSettingsClass' ) );
				}
			}
		}
	}

	/**
	 * Hook the Admin load
	 * Called from HookController on init action
	 */
	public function hookInit() {

		// Initialize the plugin menu
		$this->initMenu();

		//Redirect to the settings page after activation
		if ( get_transient( 'esq_activate' ) == 1 ) {
			delete_transient( 'esq_activate' );
			wp_redirect( ESQ_Classes_Helpers_Tools::getAdminUrl( 'esq_dashboard' ) );
			die();
		}

		/* add the plugin menu in admin */
		if ( ESQ_Classes_Helpers_Tools::userCan( 'manage_options' ) ) {
			//Add the Easy SEO columns in Posts list
			ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_PostsList' );

			//Hook the post save action
			ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Post' )->hookPost();
		}

	}

	/**
	 * Creates the Setting menu in WordPress
	 * Called from HookController on admin_menu action
	 *
	 * @return void
	 */
	public function hookMenu() {
		//Get all the post types
		$this->post_type = get_post_types( array( 'public' => true ) );

		$this->model->addMenu( array(
			apply_filters( 'esq_menu_name', _ESQ_MENU_NAME_ ),
			apply_filters( 'esq_menu_name', _ESQ_MENU_NAME_ ),
			'manage_options',
			'esq_dashboard',
			null,
			'dashicons-search'
		) );

		//Load the Easy SEO Menu
		$mainmenu = $this->model->getMainMenu();
		if ( ! empty( $mainmenu ) ) {
			foreach ( $mainmenu as $name => $item ) {

				//Check if the menu is set to show on the left side
				if ( isset( $item['leftmenu'] ) && ! $item['leftmenu'] && ESQ_Classes_Helpers_Tools::getValue( 'page', '' ) <> $name ) {
					continue;
				}

				//Add the page
				$this->model->addSubmenu( array(
					$item['parent'],
					$item['title'],
					$item['title'],
					$item['capability'],
					$name,
					$item['function'],
				) );
			}
		}
	}

	/**
	 * Add Post Editor Meta Box
	 */
	public function addMetabox() {

		if ( ! ESQ_Classes_Helpers_Tools::userCan( 'edit_posts' ) ) {
			return;
		}

		//Add the SEO metabox for the selected post types
		$types = get_post_types( array( 'public' => true ) );
		if ( ! empty( $types ) ) {
			foreach ( $types as $type ) {
				if ( ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' )->isSnippetEnable( $type ) ) {
					$this->model->addMeta( array(
						'esq_seo',
						esc_html__( 'Easy SEO', 'easy-seo' ),
						array( ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Post' ), 'init' ),
						$type,
						'normal',
						'high'
					) );
				}
			}
		}
	}

	/**
	 * Add the Easy SEO Settings class in the Easy SEO pages
	 * Used for personal layout
	 *
	 * @param  $classes
	 *
	 * @return string
	 */
	public function addEsqSettingsClass( $classes ) {
		return "$classes easyseo-settings";
	}

	/**
	 * Hook the Head
	 */
	public function hookHead() {
		//Load the Easy SEO CSS
		if ( strpos( (string) ESQ_Classes_Helpers_Tools::getValue( 'page' ), 'esq_' ) === 0 ) {
			ESQ_Classes_ObjController::getClass( 'ESQ_Classes_DisplayController' )->loadMedia( 'settings' );
		}
	}

}