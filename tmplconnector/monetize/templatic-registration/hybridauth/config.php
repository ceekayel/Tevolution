<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

$tmpdata = get_option('templatic_settings');
return 
	array(
		"base_url" => plugin_dir_url( __FILE__ ),

		"providers" => array ( 
			/* openid providers*/
			"OpenID" => array (
				"enabled" => true
			),

			"Yahoo" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => "", "secret" => "" ),
			),

			"Google" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => $tmpdata['google_key'], "secret" => $tmpdata['google_secret_key'] ), 
			),

			"Facebook" => array ( 
				"enabled" => true,
				"keys"    => array ( "id" => $tmpdata['facebook_key'], "secret" =>  $tmpdata['facebook_secret_key'] ), 
			),

			"Twitter" => array ( 
				"enabled" => true,
				"keys"    => array ( "key" => $tmpdata['twitter_key'], "secret" =>$tmpdata['twitter_secret_key'] ) 
			),

		),

		/* if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"*/
		"debug_mode" => false,

		"debug_file" => "",
	);
