<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_Menu {

	public $menu = array();
	public $meta = array();

	public function __construct() {

	}

	/**
	 * Add a menu in WP admin page
	 *
	 * @param array $param
	 *
	 * @return void
	 */
	public function addMenu( $param = null ) {
		if ( $param ) {
			$this->menu = $param;
		}

		if ( is_array( $this->menu ) ) {
			if ( $this->menu[0] <> '' && $this->menu[1] <> '' ) {
				/* add the translation */
				if ( ! isset( $this->menu[5] ) ) {
					$this->menu[5] = null;
				}
				if ( ! isset( $this->menu[6] ) ) {
					$this->menu[6] = null;
				}

				/* add the menu with WP */
				add_menu_page( $this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5], $this->menu[6] );
			}
		}
	}

	/**
	 * Add a submenu in WP admin page
	 *
	 * @param array $param
	 *
	 * @return void
	 */
	public function addSubmenu( $param = null ) {
		if ( $param ) {
			$this->menu = $param;
		}

		if ( is_array( $this->menu ) ) {

			if ( $this->menu[0] <> '' && $this->menu[1] <> '' ) {
				if ( ! isset( $this->menu[5] ) ) {
					$this->menu[5] = null;
				}

				/* add the menu with WP */
				add_submenu_page( $this->menu[0], $this->menu[1], $this->menu[2], $this->menu[3], $this->menu[4], $this->menu[5] );
			}
		}
	}

	/**
	 * Add a box Meta in WP
	 *
	 * @param array $param
	 *
	 * @return void
	 */
	public function addMeta( $param = null ) {
		if ( $param ) {
			$this->meta = $param;
		}

		if ( is_array( $this->meta ) ) {

			if ( $this->meta[0] <> '' && $this->meta[1] <> '' ) {
				if ( ! isset( $this->meta[5] ) ) {
					$this->meta[5] = null;
				}
				if ( ! isset( $this->meta[6] ) ) {
					$this->meta[6] = null;
				}
				/* add the box content with WP */
				add_meta_box( $this->meta[0], $this->meta[1], $this->meta[2], $this->meta[3], $this->meta[4], $this->meta[5] );
			}
		}
	}

	/**
	 * Get the main menu of the plugin
	 *
	 * @return array
	 */
	public function getMainMenu() {
		$menu = array(
			'esq_dashboard' => array(
				'title'      => esc_html__( "Easy SEO", 'easy-seo' ),
				'parent'     => 'esq_dashboard',
				'capability' => 'edit_posts',
				'function'   => array( ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_SeoSettings' ), 'init' ),
				'href'       => false,
				'leftmenu'   => true,
			),
			'esq_seosettings' => array(
				'title'      => esc_html__( "Settings", 'easy-seo' ),
				'parent'     => 'esq_dashboard',
				'capability' => 'manage_options',
				'function'   => array( ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_SeoSettings' ), 'init' ),
				'href'       => false,
				'leftmenu'   => true,
			),
		);

		return apply_filters( 'esq_menu', $menu );
	}

	/**
	 * Get the admin menu tabs
	 *
	 * @param string $category
	 *
	 * @return array
	 */
	public function getTabs( $category ) {
		$tabs = array();
		$tabs['esq_seosettings'] = array(
			'esq_seosettings/general' => array(
				'title'      => esc_html__( "General", 'easy-seo' ),
				'capability' => 'manage_options',
				'icon'       => 'dashicons-admin-generic',
			),
			'esq_seosettings/sitemap' => array(
				'title'      => esc_html__( "Sitemap", 'easy-seo' ),
				'capability' => 'manage_options',
				'icon'       => 'dashicons-admin-site',
			),
			'esq_seosettings/social'  => array(
				'title'      => esc_html__( "Social", 'easy-seo' ),
				'capability' => 'manage_options',
				'icon'       => 'dashicons-share',
			),
		);

		$tabs = array_filter( $tabs );

		return apply_filters( 'esq_menu_' . $category, ( isset( $tabs[ $category ] ) ? $tabs[ $category ] : array() ), $category );
	}

}