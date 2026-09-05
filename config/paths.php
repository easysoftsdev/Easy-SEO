<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

$currentDir = dirname( __FILE__ );

define( '_ESQ_NAME_', 'easyseo' );
define( '_ESQ_MENU_NAME_', 'Easy SEO' );
define( '_ESQ_NAMESPACE_', 'ESQ' );
define( '_ESQ_PLUGIN_NAME_', 'easy-seo' );

defined( 'ESQ_SSL' ) || define( 'ESQ_SSL', ( ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == "on" ) || ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || ( function_exists( 'is_ssl' ) && is_ssl() ) ) ? true : false ) );
define( '_ESQ_SITE_HOST_', parse_url( home_url(), PHP_URL_HOST ) );

/* Directories */
define( '_ESQ_ROOT_DIR_', realpath( dirname( $currentDir ) ) . '/' );
define( '_ESQ_CLASSES_DIR_', _ESQ_ROOT_DIR_ . 'classes/' );
define( '_ESQ_CONTROLLER_DIR_', _ESQ_ROOT_DIR_ . 'controllers/' );
define( '_ESQ_MODEL_DIR_', _ESQ_ROOT_DIR_ . 'models/' );
define( '_ESQ_TRANSLATIONS_DIR_', _ESQ_ROOT_DIR_ . 'translations/' );
define( '_ESQ_CORE_DIR_', _ESQ_ROOT_DIR_ . 'core/' );
define( '_ESQ_THEME_DIR_', _ESQ_ROOT_DIR_ . 'view/' );
define( '_ESQ_ASSETS_DIR_', _ESQ_THEME_DIR_ . 'assets/' );

/* URLS */
define( '_ESQ_URL_', rtrim( plugin_dir_url( _ESQ_ROOT_DIR_ . 'easy-seo.php' ), '/' ) . '/' );
define( '_ESQ_THEME_URL_', _ESQ_URL_ . 'view/' );
define( '_ESQ_ASSETS_URL_', _ESQ_THEME_URL_ . 'assets/' );