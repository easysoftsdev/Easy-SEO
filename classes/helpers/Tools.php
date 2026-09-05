<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Handles the parameters and url
 */
class ESQ_Classes_Helpers_Tools {

	/**
	 *
	 *
	 * @var array Options
	 */
	public static $options = array();

	public function __construct() {
		// Load the options from DB
		self::$options = $this->getOptions();

		//Load the languages pack
		add_action( "init", array( $this, 'loadMultilanguage' ), PHP_INT_MAX );

		ESQ_Classes_ObjController::getClass( 'ESQ_Classes_HookController' )->setHooks( $this );
	}

	/**
	 * This hook will save the current version in database
	 *
	 * @return void
	 */
	function hookInit() {
		//If the version is not yet set, run the installation process
		if ( version_compare( self::getOption( 'esq_version' ), ESQ_VERSION, '<' ) ) {
			self::install();
		}

		//add extra links to the plugin in the Plugins list
		add_filter( "plugin_row_meta", array( $this, 'hookExtraLinks' ), 10, 4 );

		//add setting link in plugin
		add_filter( 'plugin_action_links', array( $this, 'hookActionlink' ), 5, 2 );
	}

	/**
	 * Install the required data
	 *
	 * @return void
	 */
	public static function install() {

		//Set the current version
		self::$options['esq_version'] = ESQ_VERSION;
		self::$options['esq_installed'] = gmdate( 'Y-m-d H:i:s' );
		self::saveOptions();

		//Flush the rewrite rules when the sitemap is enabled
		flush_rewrite_rules();
	}

	/**
	 * Add a link to settings in the plugin list
	 *
	 * @param  array  $links
	 * @param  string $file
	 *
	 * @return array
	 */
	public function hookActionlink( $links, $file ) {
		if ( $file == _ESQ_PLUGIN_NAME_ . '/easy-seo.php' ) {
			$link = '<a href="' . self::getAdminUrl( 'esq_dashboard' ) . '">' . esc_html__( "Getting started", 'easy-seo' ) . '</a>';
			array_unshift( $links, $link );
		}

		return $links;
	}

	/**
	 * Adds extra links to plugin  page
	 *
	 * @param  $meta
	 * @param  $file
	 * @param  $data
	 * @param  $status
	 *
	 * @return array
	 */
	public function hookExtraLinks( $meta, $file, $data = null, $status = null ) {
		if ( $file == _ESQ_PLUGIN_NAME_ . '/easy-seo.php' ) {
			$meta[] = '<a href="' . self::getAdminUrl( 'esq_dashboard' ) . '" target="_blank">' . esc_html__( "Documentation", 'easy-seo' ) . '</a>';
		}

		return $meta;
	}

	/**
	 * Load the multilanguage support from .mo
	 */
	public function loadMultilanguage() {
		$locale = get_user_locale();
		$locale = apply_filters( 'plugin_locale', $locale, _ESQ_PLUGIN_NAME_ );

		if ( strpos( $locale, 'en_' ) !== false ) {
			return;
		}

		load_plugin_textdomain( _ESQ_PLUGIN_NAME_, false, _ESQ_PLUGIN_NAME_ . '/languages/' );
	}

	/**
	 * Get the Options from database
	 *
	 * @param string $action
	 *
	 * @return array
	 */
	public static function getOptions( $action = '' ) {
		$default = array(
			//Global settings
			'esq_version'          => '',
			'esq_installed'        => gmdate( 'Y-m-d H:i:s' ),
			'esq_use_backend'      => 1,
			'esq_complete_uninstall' => 0,

			//Sitemap
			'esq_auto_sitemap'     => 1,
			'esq_sitemap_ping'     => 1,
			'esq_sitemap_exclude_noindex' => 1,
			'esq_sitemap_perpage'  => 500,
			'esq_sitemap_frequency' => 'weekly',
			'esq_sitemap'          => array(
				'sitemap-posts'   => array( 'sitemap-posts.xml', 1 ),
				'sitemap-pages'   => array( 'sitemap-pages.xml', 1 ),
				'sitemap-products' => array( 'sitemap-products.xml', 1 ),
				'sitemap-categories' => array( 'sitemap-categories.xml', 1 ),
				'sitemap-tags'    => array( 'sitemap-tags.xml', 1 ),
			),

			//Metas
			'esq_use'              => 1,
			'esq_auto_title'       => 1,
			'esq_auto_description' => 1,
			'esq_auto_canonical'   => 1,
			'esq_auto_noindex'     => 1,
			'esq_auto_jsonld'      => 1,
			'esq_auto_facebook'    => 1,
			'esq_auto_twitter'     => 1,
			'esq_title_separator'  => 'sc-pipe',
			'esq_home_title'       => '{{sitename}} {{sep}} {{sitedesc}}',
			'esq_home_description' => '{{sitedesc}}',
			'esq_metas'            => array(
				'title_maxlength'       => 60,
				'description_maxlength' => 160,
			),

			//Social
			'esq_og_locale'        => 'en_US',
			'esq_og_image'         => '',
			'esq_tc_image'         => '',
			'esq_socials'          => array(
				'facebook_site'      => "",
				'twitter_site'       => "",
				'twitter_card_type'  => "summary_large_image",
			),

			//Webmasters
			'esq_google_wt'        => "",
			'esq_bing_wt'          => "",
		);

		$options = json_decode( get_option( ESQ_OPTION ), true );

		//Replace the default options with the database data
		if ( is_array( $options ) ) {
			$options = array_replace_recursive( $default, $options );
			self::$options = $options;

			return $options;
		}

		return $default;
	}

	/**
	 * Get the option from database
	 *
	 * @param  $key
	 *
	 * @return mixed
	 */
	public static function getOption( $key ) {
		if ( ! isset( self::$options[ $key ] ) ) {
			self::$options = self::getOptions();

			if ( ! isset( self::$options[ $key ] ) ) {
				self::$options[ $key ] = false;
			}
		}

		return apply_filters( 'esq_option_' . $key, self::$options[ $key ] );
	}

	/**
	 * Save the Options in user option table in DB
	 *
	 * @param null   $key
	 * @param string $value
	 */
	public static function saveOptions( $key = null, $value = '' ) {
		if ( isset( $key ) ) {
			self::$options[ $key ] = $value;
		}

		update_option( ESQ_OPTION, wp_json_encode( self::$options ) );
	}

	/**
	 * Set the header type
	 *
	 * @param string $type
	 */
	public static function setHeader( $type ) {
		switch ( $type ) {
			case 'json':
				header( 'Content-Type: application/json' );
				break;
			case 'xml':
				header( 'Content-Type: text/xml; charset=utf-8' );
				break;
			case 'text':
				header( "Content-type: text/plain" );
				break;
			case 'html':
				header( "Content-type: text/html" );
				break;
		}
	}

	/**
	 * Set the Nonce action
	 *
	 * @param  string $action
	 * @param  string $name
	 * @param  bool   $referer
	 * @param  bool   $echo
	 *
	 * @return string
	 */
	public static function setNonce( $action, $name = 'esq_nonce', $referer = true, $echo = true ) {
		return wp_nonce_field( $action, $name, $referer, $echo );
	}

	/**
	 * Get a value from $_POST / $_GET
	 * if unavailable, take a default value
	 *
	 * @param string $key Value key
	 * @param mixed $defaultValue (optional)
	 *
	 * @return mixed Value
	 */
	public static function getValue( $key, $defaultValue = false ) {
		if ( ! isset( $key ) || ( isset( $key ) && $key == '' ) ) {
			return $defaultValue;
		}

		//Get the params from forms
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$ret = ( isset( $_POST[ $key ] ) ? $_POST[ $key ] : ( isset( $_GET[ $key ] ) ? $_GET[ $key ] : '' ) );
		} else {
			$ret = ( isset( $_GET[ $key ] ) ? $_GET[ $key ] : '' );
		}

		//Start sanitization of each param based on the type
		if ( is_array( $ret ) ) { //if array, sanitize each value from the array
			if ( ! empty( $ret ) ) {
				foreach ( $ret as &$row ) {
					if ( ! is_array( $row ) ) {
						$row = sanitize_text_field( $row ); //sanitize
					}
				}
			}
		} elseif ( is_string( $ret ) && $ret <> '' && function_exists( 'sanitize_textarea_field' ) ) {
			$ret = sanitize_textarea_field( $ret );
		} else {
			$ret = sanitize_text_field( $ret );
		}

		if ( ! $ret ) {
			return $defaultValue;
		} else {
			return wp_unslash( $ret );
		}

	}

	/**
	 * Check if the parameter is set
	 *
	 * @param  string $key
	 *
	 * @return boolean
	 */
	public static function getIsset( $key ) {
		return ( isset( $_GET[ $key ] ) || isset( $_POST[ $key ] ) );
	}

	/**
	 * Get the admin URL for a specific page
	 *
	 * @param  string $page
	 * @param  string $tab
	 *
	 * @return string
	 */
	public static function getAdminUrl( $page = 'esq_dashboard', $tab = '' ) {
		if ( $tab <> '' ) {
			return admin_url( 'admin.php?page=' . $page . '&tab=' . $tab );
		}

		return admin_url( 'admin.php?page=' . $page );
	}

	/**
	 * Check if the current user can perform a capability
	 *
	 * @param  string $capability
	 *
	 * @return bool
	 */
	public static function userCan( $capability ) {
		if ( function_exists( 'current_user_can' ) ) {
			return current_user_can( $capability );
		}

		return false;
	}

	/**
	 * Check if we are in an AJAX call
	 *
	 * @return bool
	 */
	public static function isAjax() {
		return ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	/**
	 * Check if the Gutenberg block editor is active
	 *
	 * @return bool
	 */
	public static function isBlockEditor() {
		return function_exists( 'use_block_editor_for_post' );
	}

	/**
	 * Check if the current page is an admin page (backend)
	 *
	 * @return bool
	 */
	public static function isBackedAdmin() {
		if ( function_exists( 'is_admin' ) && is_admin() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a plugin is installed/active
	 *
	 * @param  string $plugin
	 *
	 * @return bool
	 */
	public static function isPluginInstalled( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}

	/**
	 * Hook the activate process
	 */
	public function esq_activate() {
		set_transient( 'esq_activate', true );

		//Install & Initialize
		self::install();
	}

	/**
	 * Hook the deactivate process
	 */
	public function esq_deactivate() {
		flush_rewrite_rules();
	}

}