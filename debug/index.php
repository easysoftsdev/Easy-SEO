<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

// Enable the debug only if ESQ_DEBUG is set to 1
if ( defined( 'ESQ_DEBUG' ) && ESQ_DEBUG ) {
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}