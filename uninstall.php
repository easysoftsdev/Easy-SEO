<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( 'Cheatin\' uh?' );
}

global $wpdb;

//Delete the plugin options
delete_option( ESQ_OPTION );

//Delete the SEO metas for all the posts
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_esq_%'" );

//Clear any cached data
wp_cache_flush();