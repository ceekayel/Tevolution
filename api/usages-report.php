<?php
add_action( 'init', 'tmpl_get_site_usages',98 );
/* main function which collect and return data */
function tmpl_get_site_usages() {
    global $wp_version;
    /* site general details */
    
    $result['site_detail']['site_url'] = site_url();
    $result['site_detail']['server_ip'] = $_SERVER['SERVER_ADDR'];
    $result['site_detail']['site_title'] = get_bloginfo( 'title' );
    $result['site_detail']['admin_email'] = get_option('admin_email');
    $result['site_detail']['current_theme'] = get_current_theme();
    $result['site_detail']['template'] = get_template();
    $result['site_detail']['stylesheet'] = get_stylesheet();
    
    /* get users count and avail_roles */
    $get_users = count_users();
    $result['site_detail']['total_users'] = $get_users['total_users'];
    
    /* site meta details */
    $theme_data = wp_get_theme();			
    $result['site_options']['theme_version'] = $theme_data['Version'];
    $result['site_options']['description'] = get_bloginfo( 'description' );
    $result['site_options']['wp_version'] = get_bloginfo('version');
    $result['site_options']['locale'] = get_locale();
    $result['site_options']['debug_mode'] = WP_DEBUG;
    (!defined('WP_MEMORY_LIMIT')) ? $result['site_options']['memory_limit'] = WP_MEMORY_LIMIT : '';
    $result['site_options']['php_version'] = $_SERVER['SERVER_SOFTWARE'];
    $result['site_options']['mysql_version'] = mysql_get_server_info();
    $result['site_options']['post_size'] = ini_get('post_max_size');
    $result['site_options']['upload_size'] = ini_get('upload_max_filesize');
    $result['site_options']['is_curl'] = 1;
    
    /* templatic setting start */
    $templatic_setting = get_option('templatic_settings');
    isset($templatic_setting['php_mail']) ? $result['site_options']['php_mail'] = $templatic_setting['php_mail'] : '';
    $result['site_options']['send_to_frnd'] = isset($templatic_setting['send_to_frnd']) ? 1 : 0;
    $result['site_options']['send_inquiry'] = isset($templatic_setting['send_inquiry']) ? 1 : 0;
    $result['site_options']['templatic_settings'] = $templatic_setting;
    $event_manager_setting = get_option('event_manager_setting');
    if(!empty($event_manager_setting)) {
        $result['site_options']['event_manager_setting'] = $event_manager_setting;
    }
    
    /* reset location manager query */
    remove_action('parse_query','tmpl_location_parse_query');
    /* get price package for tevolution */
    $pargs = array('post_type' => 'monetization_package',
                    'posts_per_page' => -1,
                    'meta_query' => array('relation' => 'AND',
                                                          array('key' => 'package_status',
                                                                'value' =>  '1',
                                                                'compare' => '=')
                                        ),
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                    );
          remove_all_actions('posts_where');
       
        $package_query = null;			
        $package_query = new WP_Query($pargs);
            
        if($package_query->have_posts()){
            $result['site_options']['total_price_package'] = $package_query->post_count;
            /* result for site price package */
            global $post;
            while($package_query->have_posts()){ 
                    $package_query->the_post();	
                    $result['price_package'][$post->ID]['title'] =  $post->post_title;
                    $result['price_package'][$post->ID]['status'] =  $post->post_status;
                    $result['price_package'][$post->ID]['package_type'] =  get_post_meta($post->ID,'package_type',true);;
                    $result['price_package'][$post->ID]['package_amount'] =  get_post_meta($post->ID,'package_amount',true);;
                    $result['price_package'][$post->ID]['package_post_type'] = get_post_meta($post->ID,'package_post_type',true);
                    $result['price_package'][$post->ID]['category'] = get_post_meta($post->ID,'category',true);
                    $result['price_package'][$post->ID]['show_package'] = get_post_meta($post->ID,'show_package',true);
                    $result['price_package'][$post->ID]['recurring'] = get_post_meta($post->ID,'recurring',true);
                    $result['price_package'][$post->ID]['billing_num'] = get_post_meta($post->ID,'billing_num',true);
                    $result['price_package'][$post->ID]['billing_per'] =get_post_meta($post->ID,'billing_per',true);
                    $result['price_package'][$post->ID]['validity'] = get_post_meta($post->ID,'validity',true);
                    $result['price_package'][$post->ID]['validity_per'] = get_post_meta($post->ID,'validity_per',true);
                    $result['price_package'][$post->ID]['package_status'] = get_post_meta($post->ID,'package_status',true);
                    $result['price_package'][$post->ID]['billing_cycle'] = get_post_meta($post->ID,'billing_cycle',true);
                    $result['price_package'][$post->ID]['is_featured'] = get_post_meta($post->ID,'is_featured',true);
                    $result['price_package'][$post->ID]['feature_amount'] = get_post_meta($post->ID,'feature_amount',true);
                    $result['price_package'][$post->ID]['feature_cat_amount'] = get_post_meta($post->ID,'feature_cat_amount',true);  
                    $result['price_package'][$post->ID]['home_featured_type'] = get_post_meta($post->ID,'home_featured_type',true); 
                    $result['price_package'][$post->ID]['featured_type'] = get_post_meta($post->ID,'featured_type',true);

            } wp_reset_query();
        }          
        
        /* get currency settings */
        $result['site_options']['currency_symbol'] = get_option('currency_symbol');
        $result['site_options']['tmpl_price_num_decimals'] = get_option('tmpl_price_num_decimals');
        $result['site_options']['tmpl_price_decimal_sep'] = get_option('tmpl_price_decimal_sep');
        $result['site_options']['tmpl_price_thousand_sep'] = get_option('tmpl_price_thousand_sep');
        $result['site_options']['currency_code'] = get_option('currency_code');
        $result['site_options']['currency_pos'] = get_option('currency_pos');
        
        
        /* Location settings */
        $directory_citylocation_view = get_option('directory_citylocation_view');
        !empty($directory_citylocation_view) ? $result['site_options']['directory_citylocation_view'] = $directory_citylocation_view : '';
        
        $location_options = get_option('location_options');
        !empty($location_options) ? $result['site_options']['location_options'] = $location_options : '';
        
        $default_city_set = get_option('default_city_set');
        !empty($default_city_set) ? $result['site_options']['default_city_set'] = $default_city_set : '';
        
        $geoip_location_tracking = get_option('geoip_location_tracking');
        !empty($geoip_location_tracking) ? $result['site_options']['geoip_location_tracking'] = $geoip_location_tracking : '';
        
        $disable_city_log = get_option('disable_city_log');
        !empty($disable_city_log) ? $result['site_options']['disable_city_log'] = $disable_city_log : '';
        
        $location_post_type = get_option('location_post_type');
        !empty($location_post_type) ? $result['site_options']['location_post_type'] = $location_post_type : '';
                
        if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
            global $wpdb,$country_table,$multicity_table,$zones_table;
			$country_table = $wpdb->prefix . "countries";
			$zones_table = $wpdb->prefix . "zones";
			$multicity_table = $wpdb->prefix . "multicity";
            $sql = "select count(*) as total_country from $country_table";
            $countryinfo = $wpdb->get_results($sql);
            $result['site_options']['total_country'] = $countryinfo[0]->total_country;
                    
            $sql = "select count(*) as total_city from $multicity_table";
            $cityinfo = $wpdb->get_results($sql);
            $result['site_options']['total_state'] = $cityinfo[0]->total_city;
            
            $sql = "select count(*) as total_state from $zones_table";
            $stateinfo = $wpdb->get_results($sql);	
            $result['site_options']['total_city'] = $stateinfo[0]->total_state;
            
        }
        
        /* get theme settings */
        
        if(function_exists('supreme_prefix')){
                $pref = supreme_prefix();
        }else{
                $pref = sanitize_key( apply_filters( 'hybrid_prefix', get_template() ) );
        }
        $theme_options = get_option($pref.'_theme_settings');
        isset($theme_options['rtlcss']) ? $result['site_options']['rtlcss'] = $theme_options['rtlcss'] : '';
        isset($theme_options['enable_sticky_header_menu']) ? $result['site_options']['enable_sticky_header_menu'] = $theme_options['enable_sticky_header_menu'] : '';
        isset($theme_options['supreme_show_breadcrumb']) ? $result['site_options']['supreme_show_breadcrumb'] = $theme_options['supreme_show_breadcrumb'] : '';
        isset($theme_options['tmpl_mobile_view']) ? $result['site_options']['tmpl_mobile_view'] = $theme_options['tmpl_mobile_view'] : '';
        
        $directory_custom_css = get_option('directory_custom_css');
        !empty($directory_custom_css) ? $result['site_options']['is_directory_custom_css'] = 1 : '';
        
        /* get active plugin list */
        $the_plugs = get_option('active_plugins'); 
        $loop_count = 0;
        $plugin_folder_path = WP_CONTENT_DIR . "/plugins/";
        foreach($the_plugs as $key => $value) {
            $plugin_data = '';
            
            $plugin_data = get_plugin_data( $plugin_folder_path.$value ); 
            
            $result['site_plugin'][$loop_count]['plugin_title'] = $plugin_data['Name'];
            $result['site_plugin'][$loop_count]['path'] = $value;
            $result['site_plugin'][$loop_count]['version'] = $plugin_data['Version'];
            $result['site_plugin'][$loop_count]['is_third_party'] = strpos($plugin_data['Author'],'Templatic') ? 0 : 1;
            $loop_count++;
        }
        
        /* get tevolution post type and total count */
        global $wpdb;
        $custom_post_types = get_option("templatic_custom_post");
        $post_table = $wpdb->prefix.'posts';
        foreach($custom_post_types as $key => $value) {
            $result['site_posts'][$key]['name'] = $value['label'];
            $sql = "select count(*) as total_post from $post_table where post_type='".$key."'";
            $postinfo = $wpdb->get_results($sql);	
            $result['site_posts'][$key]['total_post'] = $postinfo[0]->total_post;
            
        }
        
        /* get payment gatways  info */
        $option_table = $wpdb->prefix.'options';
        $sql = "SELECT * FROM `$option_table` WHERE `option_name` LIKE '%payment_method%'";
        $paymentinfo = $wpdb->get_results($sql);
        if($paymentinfo)	{
            foreach($paymentinfo as $key => $payment)
            {
                
                $payment = unserialize($payment->option_value);
                $result['payment_gateway'][$key]['payment_method'] = $payment['name'];
                $result['payment_gateway'][$key]['status'] = $payment['isactive'];
                $result['payment_gateway'][$key]['payment_key'] = $payment['key'];
                
                $transection_table = $wpdb->prefix.'transactions';
                $transaction = "select count(*) as total_transation from $transection_table where payment_method='".$payment['key']."'" ;
                $transction= $wpdb->get_results($transaction);
	
                $result['payment_gateway'][$key]['total_transaction'] = $transction[0]->total_transation;

            }
        }
        
    /* make curl to templatic with all the data */    
    $HTTP_HOST=$_SERVER['HTTP_HOST'];
    $SERVER_ADDR=$_SERVER['SERVER_ADDR'];
    $arg=array('method' => 'POST',
                'timeout' => 30,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array( 'is_usages_curl' => TRUE,'result'=> $result,'HTTP_HOST'=>$HTTP_HOST,'SERVER_ADDR'=>$SERVER_ADDR),
                'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
                'cookies' => array()
       );
    
    //$response = wp_remote_get('http://localhost/tmp/usages_responce.php',$arg);
    $response = wp_remote_get('http://templatic.net/settingstatistics/usages_responce.php',$arg);
    
}

