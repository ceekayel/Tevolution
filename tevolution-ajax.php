<?php
/**
 * Tevolution CUSTOM AJAX Process Execution.
 *
 * Executing AJAX process.
 */
define( 'DOING_AJAX', true );
define( 'WP_ADMIN', true );
define('WP_DEBUG', false);
/* Load WordPress Bootstrap */
//require_once( dirname(dirname( dirname(dirname( __FILE__ ) ) )) . '/wp-load.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp/wp-load.php' );
/* Require an action parameter  */
if ( empty( $_REQUEST['action'] ) )
	die( '0' );

@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
@header( 'X-Robots-Tag: noindex' );

$action = esc_attr(trim($_POST['action']));
/* A bit of security  */
$allowed_actions = apply_filters('tevolution_ajax_allowed_actions',array(
						'load_populer_post',
						'googlemap_initialize',
						'tevolution_autocomplete_callBack',
						'tevolution_autocomplete_address_callBack',
					));

	if ( is_user_logged_in() ) {	
		do_action( 'wp_ajax_' . $_REQUEST['action'] );
	} else {	
		do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
	}

/* Default status */ 
die( '0' );