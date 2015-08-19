<?php
/*
 * price packages related function for backend
 */
/* script in style for colour picker in back end */
add_action('admin_enqueue_scripts', 'add_farbtastic_style_script');

/* code to create an admin sub page menu for price packages */
add_action('templ_add_admin_menu_', 'add_subpage_monetization', 13);

add_action('wp_dashboard_setup', 'recent_transactions_dashboard_widgets');

add_action('admin_init', 'post_price_package');

add_action('admin_init', 'transactions_table_create');

/* for featured options of packages */
add_action('admin_init', 'tmpl_change_is_featured_option');

add_filter('set-screen-option', 'package_table_set_option', 10, 3);

/* 	include wordpress farbtastic script and style for choose colour picker	 */

function add_farbtastic_style_script() {
          wp_enqueue_script('farbtastic');
          wp_enqueue_style('farbtastic');
}

/* activating price packages */
if ((isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'monetization') && ($_REQUEST['true'] && $_REQUEST['true'] == 1) || (isset($_REQUEST['activated']) && $_REQUEST['activated'] == 'true')) {
          update_option('monetization', 'Active');
          if (!get_option('currency_symbol'))
                    update_option('currency_symbol', '$');
          if (!get_option('currency_code'))
                    update_option('currency_code', 'USD');
          if (!get_option('currency_pos'))
                    update_option('currency_pos', '1');
          if (!get_option('tmpl_price_decimal_sep'))
                    update_option('tmpl_price_decimal_sep', '.');
          if (!get_option('tmpl_price_num_decimals'))
                    update_option('tmpl_price_num_decimals', 2);
          if (!get_option('tmpl_price_thousand_sep'))
                    update_option('tmpl_price_thousand_sep', ',');

          add_action('admin_init', 'tmpl_dummy_pkg_');

          function tmpl_dummy_pkg_() {
                    require_once(TEMPL_MONETIZATION_PATH . 'add_dummy_packages.php');
          }

} else if ((isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'monetization') && (isset($_REQUEST['true']) && $_REQUEST['true'] == 0 )) {
          delete_option('monetization');
}


/*
  Create the transactions table
 */

function transactions_table_create() {
          global $wpdb, $pagenow;
          /* transaction table BOF */
          if (($pagenow == 'index.php' || $pagenow == 'plugins.php' || (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'templatic_system_menu' || $_REQUEST['page'] == 'transcation' || $_REQUEST['page'] == 'monetization'))) && get_option('tev_transaction_table') != 'updated') {

                    $transection_db_table_name = $wpdb->prefix . "transactions";
                    if ($wpdb->get_var("SHOW TABLES LIKE \"$transection_db_table_name\"") != $transection_db_table_name) {
                              $transaction_table = 'CREATE TABLE IF NOT EXISTS `' . $transection_db_table_name . '` (
			`trans_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`post_id` bigint(20) NOT NULL,
			`post_title` varchar(255) NOT NULL,
			`status` int(2) NOT NULL,
			`payment_method` varchar(255) NOT NULL,
			`payable_amt` float(25,5) NOT NULL,
			`payment_date` datetime NOT NULL,
			`paypal_transection_id` varchar(255) NOT NULL,
			`user_name` varchar(255) NOT NULL,
			`pay_email` varchar(255) NOT NULL,
			`billing_name` varchar(255) NOT NULL,
			`billing_add` text NOT NULL,
			`package_id` int(10) NOT NULL DEFAULT 0,
			`package_type` VARCHAR(255) NULL DEFAULT NULL,
			`payforpackage`  int(2) NOT NULL DEFAULT 0,
			`payforfeatured_h`  int(2) NOT NULL DEFAULT 0,
			`payforfeatured_c`  int(2) NOT NULL DEFAULT 0,
			`payforcategory`  int(2) NOT NULL DEFAULT 0,
			PRIMARY KEY (`trans_id`)
			)DEFAULT CHARSET=utf8';
                              $wpdb->query($transaction_table);
                    }

                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'package_id'");
                    if ('package_id' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD package_id int(10) NOT NULL DEFAULT '0'");
                    }
                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'package_type'");
                    if ('package_type' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD package_type VARCHAR(255) NULL DEFAULT NULL");
                    }

                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'payforpackage'");
                    if ('payforpackage' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD payforpackage int(2) NOT NULL DEFAULT '0'");
                    }
                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'payforfeatured_h'");
                    if ('payforfeatured_h' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD payforfeatured_h int(2) NOT NULL DEFAULT '0'");
                    }
                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'payforfeatured_c'");
                    if ('payforfeatured_c' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD payforfeatured_c int(2) NOT NULL DEFAULT '0'");
                    }
                    $field_check = $wpdb->get_var("SHOW COLUMNS FROM $transection_db_table_name LIKE 'payforcategory'");
                    if ('payforcategory' != $field_check) {
                              $wpdb->query("ALTER TABLE $transection_db_table_name ADD payforcategory int(2) NOT NULL DEFAULT '0'");
                    }
                    /* transaction table EOF */


                    $users_packageperlist_table_name = $wpdb->prefix . "users_packageperlist";
                    if ($wpdb->get_var("SHOW TABLES LIKE \"$users_packageperlist_table_name\"") != $users_packageperlist_table_name) {
                              $users_packageperlist_table = 'CREATE TABLE IF NOT EXISTS `' . $users_packageperlist_table_name . '` (
			`ID` int(20) NOT NULL AUTO_INCREMENT,
			`user_id` int(20) NOT NULL,
			`post_id` int(20) NOT NULL,
			`package_id` int(10) NOT NULL DEFAULT 0,
			`trans_id` int(10) NOT NULL DEFAULT 0,
			`subscriber_id` varchar(255) NOT NULL DEFAULT 0,
			`date` date NOT NULL,
			`status` int(2) NOT NULL,
			PRIMARY KEY (`ID`)
			)DEFAULT CHARSET=utf8';
                              $wpdb->query($users_packageperlist_table);
                    }
                    update_option('tev_transaction_table', 'updated');
          }
}

/* creating a sub page menu to tevolution menu */

function add_subpage_monetization() {
          $page_title = __('Monetization', ADMINDOMAIN); /* define page title and menu title */
          $transcation_title = __('Transactions', ADMINDOMAIN); /* define page title and menu title */

          $hook = add_submenu_page('templatic_system_menu', $page_title, $page_title, 'administrator', 'monetization', 'add_monetization');

          add_action("load-$hook", 'add_screen_options'); /* call a function to add screen options */
          $hook_transaction = add_submenu_page('templatic_system_menu', $transcation_title, $transcation_title, 'administrator', 'transcation', 'add_transcation');
          do_action('templatic_monetizations_menu');
          add_action("load-$hook_transaction", 'add_screen_options_transaction'); /* call a function to add screen options */
}

/* function called on sub page menu hook */

function add_transcation() {
          if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'transcation' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
                    include(TEMPL_MONETIZATION_PATH . "templatic_transaction_detail_report.php");
          elseif (isset($_REQUEST['page']) && $_REQUEST['page'] == 'transcation')
                    include(TEMPL_MONETIZATION_PATH . "templatic_transaction_report.php");
}

/* function called on sub page menu hook */

function add_monetization() {
          include(TEMPL_MONETIZATION_PATH . "templatic_monetization.php");
}

/* 	 Display the screen option in Monetization menu page 
 */

function add_screen_options() {
          $option = 'per_page';
          $args = array('label' => 'Show record per page for monetization',
                    'default' => 10,
                    'option' => 'package_per_page'
          );
          add_screen_option($option, $args); /* ADD SCREEN OPTION */
}

/* 	display the screen option in transaction menu page
 */

function add_screen_options_transaction() {
          $option = 'per_page';
          $args = array('label' => 'Transaction',
                    'default' => 10,
                    'option' => 'transaction_per_page'
          );
          add_screen_option($option, $args); /* ADD SCREEN OPTION */
}

/* this function will filter data according to screen options   */

function package_table_set_option($status, $option, $value) {
          return $value;
}

/* 	Admin dashboard transaction widget setup */

function recent_transactions_dashboard_widgets() {
          global $current_user;
          if (is_super_admin($current_user->ID)) {
                    wp_add_dashboard_widget('recent_transactions_dashboard_widgets', RECENT_TRANSACTION_TEXT, 'recent_transactions_dashboard_widget');

                    global $wp_meta_boxes;

                    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

                    @$example_widget_backup = array('recent_transactions_dashboard_widgets' => $normal_dashboard['recent_transactions_dashboard_widgets']);
                    unset($normal_dashboard['recent_transactions_dashboard_widgets']);

                    $sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);

                    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
          }
}

/*
  Admin dashboard transaction widget display
 */

function recent_transactions_dashboard_widget() {

          global $wpdb, $monetization;
          ?>
          <script type="text/javascript">
                    var chkTransstatus = null;
                    function change_poststatus(str)
                    {
                         if (str == "")
                         {
                              jQuery("#p_status_" + tid).html("");
                              return false;
                         }

                         /* Ajax request for locate change transaction status */
                         chkTransstatus = jQuery.ajax({
                              url: ajaxUrl,
                              type: 'POST',
                              async: true,
                              data: 'action=tmpl_ajax_update_status&post_id=' + str,
                              beforeSend: function () {
                                   if (chkTransstatus != null) {
                                        chkTransstatus.abort();
                                   }
                              },
                              success: function (results) {
                                   jQuery("#p_status_" + str).html(results);
                              }
                         });

                    }
          </script>
          <?php
          $tmpdata = get_option('templatic_settings');
          if (isset($tmpdata['trans_post_type_value']) && count($tmpdata['trans_post_type_value']) > 0) {
                    $post_args = array('post_status' => 'draft,publish', 'post_type' => $tmpdata['trans_post_type_value'], 'order' => 'DESC', 'numberposts' => 7);
                    $recent_posts = get_posts($post_args);
                    $no_alive_days = get_option('no_alive_days');
                    if ($recent_posts) {

                              $transactions = $wpdb->prefix . "transactions";
                              echo '<table class="widefat"  width="100%" >
			<thead>	';
                              $th = '	<tr>
				<th valign="top" align="left" style="width: 50%;">' . __('Transactions', DOMAIN) . '</th>
				<th valign="top" align="left">' . __('With', DOMAIN) . '</th>
				<th valign="top" align="left">' . __('Exp.', DOMAIN) . '</th>
				<th valign="top" align="left">' . __('Status', DOMAIN) . '</th>';
                              $th .= '</tr>';
                              echo $th;
                              foreach ($recent_posts as $posts) {

                                        $color_taxonomy = 'trans_post_type_colour_' . $posts->post_type;

                                        $featured_text = '';
                                        /* Check for featured posts: start */
                                        $featured_type = get_post_meta($posts->ID, 'featured_type', true);
                                        if ('h' == $featured_type) {
                                                  $featured_text = '<div>' . __("Home", DOMAIN) . '</div>';
                                        } elseif ('c' == $featured_type) {
                                                  $featured_text = '<div>' . __("Category", DOMAIN) . '</div>';
                                        } elseif ('both' == $featured_type) {
                                                  $featured_text = '<div>' . __("Home, Category", DOMAIN) . '</div>';
                                        } else {
                                                  $featured_text = '';
                                        }

                                        $price_amount = (get_post_meta($posts->ID, 'total_price', true)) ? fetch_currency_with_position(get_post_meta($posts->ID, 'total_price', true)) : fetch_currency_with_position('0');
                                        $sql = "select * from $transactions where post_id=" . $posts->ID . " AND (package_type is NULL OR package_type=0)";
                                        $tran_info = $wpdb->get_results($sql);

                                        $transaction_price_pkg = $monetization->templ_get_price_info($tran_info[0]->package_id, '');
                                        $publish_date = date_i18n('Y-m-d', strtotime($tran_info[0]->payment_date));
                                        $alive_days = $transaction_price_pkg[0]['alive_days'];
                                        $expired_date = date_i18n(get_option("date_format"), strtotime($publish_date . "+$alive_days day"));
                                        if (isset($tmpdata[$color_taxonomy]) && $tmpdata[$color_taxonomy] != '') {
                                                  $color_taxonomy_value = $tmpdata[$color_taxonomy];
                                        }
                                        echo '<tr>
				<td valign="top" align="left" ><a href="' . admin_url() . 'admin.php?page=transcation&action=edit&trans_id=' . $tran_info[0]->trans_id . '">' . $tran_info[0]->trans_id . '</a>&nbsp; <a href="' . site_url() . '/wp-admin/post.php?post=' . $posts->ID . '&action=edit">' . $posts->post_title . '</a>&nbsp;<div class="transaction_meta">' . __('On', ADMINDOMAIN) . "&nbsp;" . date_i18n(get_option("date_format"), strtotime($tran_info[0]->payment_date)) . '&nbsp;' . __('with', ADMINDOMAIN) . " " . get_the_title($tran_info[0]->package_id) . '&nbsp;' . __('with amt.', ADMINDOMAIN) . '<span style="color:green;">' . $price_amount . '</span></div></td>';
                                        echo '<td valign="top" align="left">' . $tran_info[0]->payment_method . '</td>';
                                        echo '<td valign="top" align="left">' . $expired_date . '</td>';
                                        if ($no_alive_days != '1') {
                                                  echo '<td valign="top" align="left">';
                                                  if (get_post_meta($posts->ID, 'alive_days', true)) {
                                                            echo get_post_meta($posts->ID, 'alive_days', true);
                                                  } else {
                                                            echo '0';
                                                  } echo '</td>';
                                        }
                                        if (get_post_status($posts->ID) == 'draft') {
                                                  echo '<td valign="top" align="left" id="p_status_' . $posts->ID . '"><a href="javascript:void(0);" onclick="change_poststatus(' . $posts->ID . ')"  style="color:#E66F00">' . PENDING . '</a></td>';
                                        } else if (get_post_status($posts->ID) == 'publish') {
                                                  echo '<td valign="top" align="left" style="color:green" id="p_status_' . $posts->ID . '">' . APPROVED_TEXT . '</td>';
                                        }
                                        echo '</tr>';
                              }
                              echo '</thead>	</table>';

                              echo '<p><a href="' . admin_url('admin.php?page=transcation') . '">View More Transactions</a></p>';
                    } else {
                              echo __('No recent transaction available.', ADMINDOMAIN);
                    }
          } else {
                    echo '<p style="margin:0 0 10px">' . sprintf(__('No transaction type selected from  <a href="%s" >transaction settings</a>.', ADMINDOMAIN), admin_url('admin.php?page=transcation')) . '</p>';
          }
}

/* deleting the package on click of delete button of dashboard metabox */

if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && (isset($_REQUEST['package_id']) && $_REQUEST['package_id'] != ''))) {
          global $wpdb, $post;
          $id = $_REQUEST['package_id'];

          delete_post_meta($id, 'package_type');
          delete_post_meta($id, 'package_post_type');
          delete_post_meta($id, 'category');
          delete_post_meta($id, 'show_package');
          delete_post_meta($id, 'package_amount');
          delete_post_meta($id, 'validity');
          delete_post_meta($id, 'validity_per');
          delete_post_meta($id, 'package_status');
          delete_post_meta($id, 'recurring');
          delete_post_meta($id, 'billing_num');
          delete_post_meta($id, 'billing_per');
          delete_post_meta($id, 'billing_cycle');
          delete_post_meta($id, 'is_featured');
          delete_post_meta($id, 'feature_amount');
          delete_post_meta($id, 'feature_cat_amount');
          wp_delete_post($id);
          $url = site_url() . '/wp-admin/admin.php?page=monetization';
          echo '<form action="' . $url . '" method="get" id="frm_package" name="frm_package">
	<input type="hidden" value="monetization" name="page"><input type="hidden" value="delete" name="package_msg">
	<input type="hidden" value="packages" name="tab">
	</form>
	<script>document.frm_package.submit();</script>
	';
          exit;
}

/*
  Fetch order information as a table format.
 */

function get_order_detailinfo_transaction_report($orderId, $isshow_paydetail = 0) {
          global $wpdb, $prd_db_table_name, $transection_db_table_name;
          $ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
          $orderinfo = $wpdb->get_results($ordersql);
          $orderinfo = $orderinfo[0];
          $post_id = $orderinfo->post_id;
          $package_select_id = get_post_meta($post_id, 'package_select', true);
          $package_select_name = get_the_title($package_select_id);
          $coupon_code = get_post_meta($post_id, 'coupon_code', true);
          $alive_days = get_post_meta($post_id, 'alive_days', true);
		  $trans_status = $orderinfo->status;
		  if($trans_status == 0){
				$tstatus = '<span id="p_status_'.$tid.'" style="color:#E66F00; font-weight:normal;"  href="javascript:void(0);">'.__('Pending',DOMAIN).'</span>';
			}else if($trans_status == 1){
				$tstatus = '<span style="color:green; font-weight:normal;">'.__('Approved',DOMAIN).'</span>';
			}
			else if($trans_status == 2){
				$tstatus = '<span style="color:red; font-weight:normal;">'.__('Cancel',DOMAIN).'</span>';
			}else{
				$tstatus = "-";
			}
          $message = '';
          if ($isshow_paydetail) {
                    $message .= '<style>.address_info {width:400px;}</style>';
          }
          $message .='
	<div class="order_info">
	<p> <span class="span"> ' . __('Transaction ID', DOMAIN) . ' </span> : <span class="trans_strong">' . $orderinfo->trans_id . '  </span></p> 
	<p><span class="span"> ' . __('Transaction Date', DOMAIN) . ' </span> : <span class="trans_strong">' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($orderinfo->payment_date)) . '</span> </p>';
          if (!$alive_days) {
                    $publishdate = get_post($post_id);
                    $publish_date = strtotime($publishdate->post_date);
                    $publish_date = date_i18n('Y-m-d', $publish_date);
                    $expired_date = date_i18n(get_option("date_format"), strtotime($publish_date . "+$alive_days day"));
                    $time_formate = get_option('time_format');
                    $end_time = '';
                    if (get_post_meta($post_id, 'st_time', true)) {
                              $end_time = ' ' . date($time_formate, strtotime(get_post_meta($post_id, 'st_time', true)));
                    }
                    $message .='<div class="checkout_address" >
								<div class="address_info address_info2 fr">
									<p> <span class="span"> ' . __('Expiry Date', DOMAIN) . ' </span> :  <span class="trans_strong">' . $expired_date . $end_time . '</span>  </p>
								</div>
							</div>';
          }
          $message .='<p><span class="span">' . __('Transaction Status', DOMAIN) . '</span>  : <span class="trans_strong">' . $tstatus . '</span> </p>
	</div> <!--order_info -->
	<div class="checkout_address" >
	<div class="address_info address_info2 fr">
		<p> <span class="span"> ' . __('Payment Method', DOMAIN) . ' </span> : <span class="trans_strong">' . get_payment_method($orderinfo->payment_method) . '</span>  </p>
	</div>
	</div>
	';
          if ($coupon_code) {
                    $message .='<tr>
	<td align="left" valign="top" colspan="2">
		<div class="checkout_address" >
			<div class="address_info address_info2 fr">
				<h3> ' . __('Coupon Code', DOMAIN) . '  </h3>									
				<div class="address_row"><span class="trans_strong">' . $coupon_code . '</span>  </div>
			</div>
		</div><!-- checkout Address -->
	 </td>
	</tr>';
          }


          return $message;
}

/*
  fetch order information as a table format.
 */

function get_order_detailinfo_price_package($orderId, $isshow_paydetail = 0) {
          global $wpdb, $prd_db_table_name, $transection_db_table_name;
          $ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
          $orderinfo = $wpdb->get_results($ordersql);
          $orderinfo = $orderinfo[0];

          $package_select_id = $orderinfo->package_id;

          $is_category_featured = get_post_meta($package_select_id, 'is_category_featured', true);
          $feature_cat_amount = get_post_meta($package_select_id, 'feature_cat_amount', true);

          $is_home_featured = get_post_meta($package_select_id, 'is_home_featured', true);
          $feature_amount = get_post_meta($package_select_id, 'feature_amount', true);

          $package_type = get_post_meta($package_select_id, 'package_type', true);
          $message = '';
          $recurring = get_post_meta($package_select_id, 'recurring', true);
          $package_select_name = get_the_title($package_select_id);

          $message .='<div class="checkout_address" >
					<div class="address_info address_info2 fr">
						<p> <span class="span"> ' . __('Package', DOMAIN) . ' </span> : <span class="trans_strong">' . $package_select_name . '</span>  </p>
					</div>
				</div>';
          if ($package_type) {
                    if ($package_type == 1) {
                              $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Package Type', DOMAIN) . ' </span> : <span class="trans_strong">' . __('Single Submission', DOMAIN) . '</span> </p>
					</div>';
                    } else {
                              $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Package Type', DOMAIN) . ' </span> : <span class="trans_strong">' . apply_filters('tmpl_package_type',__('Subscription', DOMAIN),$package_type) . '</span> </p>
					</div>';
                    }
          }
          if ($recurring) {
                    $trans_details = $wpdb->get_row("select * from $transection_db_table_name where trans_id=\"$_REQUEST[trans_id]\"");
                    $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Recurring', DOMAIN) . ' </span> : <span class="trans_strong">' . __('Yes', DOMAIN) . '</span> </p>
					</div>
					<div class="order_info">
						<p> <span class="span"> ' . __('Recurring Price', DOMAIN) . ' </span> : <span class="trans_strong">' . fetch_currency_with_position($trans_details->payable_amt, 2) . '  </span></p>
					</div>
					';
          }
          /* package have home page featured or not */
          if ($is_home_featured) {
                    $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Featured for home page', DOMAIN) . ' </span> : <img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
          } elseif ($feature_amount != '') {
                    $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Featured for home page', DOMAIN) . ' </span> : ' . display_amount_with_currency_plugin($feature_amount) . ' </p>
					</div>';
          }

          /* package have category page featured or not */
          if ($is_category_featured) {
                    $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Featured for category page', DOMAIN) . ' </span> : <img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
          } elseif ($feature_cat_amount != '') {
                    $message .='<div class="order_info">
						<p> <span class="span"> ' . __('Featured for category page', DOMAIN) . ' </span> : ' . display_amount_with_currency_plugin($feature_cat_amount) . ' </p>
					</div>';
          }

          if (is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')) {
                    if (get_post_meta($post_id, 'author_moderate', true) == 1) {
                              $message .='<div class="order_info">
						<p> <span class="span"> ' . __('User caon moderate comment', DOMAIN) . ' </span> : <img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" /> </p>
					</div>';
                    }
          }
          return $message;
}

/*
  Fetch order information as a table format.
 */

function get_order_detailinfo_tableformat($orderId, $isshow_paydetail = 0) {
          global $wpdb, $prd_db_table_name, $transection_db_table_name;
          $transection_mng_table = $wpdb->prefix . "users_packageperlist";
          $trans_details = "select subscriber_id from $transection_mng_table where trans_id=\"$orderId\"";


          $subscriber_details = $wpdb->get_var($trans_details);
          $subscriber_id = $subscriber_details;
          if (@$subscriber_id != '') {
                    $ordersql = "select * from $transection_mng_table where subscriber_id=\"$subscriber_id\"";
          } else {
                    $ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
          }
          $orderinfo = $wpdb->get_results($ordersql);
          $post_id = $orderinfo[0]->post_id;
          $post_type = get_post($post_id);
          $package_select_id = get_post_meta($post_id, 'package_select', true);
          $package_select_name = get_the_title($package_select_id);
          $coupon_code = get_post_meta($post_id, 'coupon_code', true);
          $message = '';
          if ($isshow_paydetail) {

                    $message .= '<style>.address_info {width:400px;}</style>';
          }

          $message .='<table width="100%" class="table widefat post" ><thead>
			<tr>
				<th width="5%" align="left" class="title" > ' . __('Image', DOMAIN) . '</th>
				<th width="25%" align="left" class="title" >' . __('Title', DOMAIN) . '</th>
				<th width="20%" align="left" class="title" > ' . __('Submitted by', DOMAIN) . '</th>
				<th width="15%" align="left" class="title" > ' . __('Payment Method', DOMAIN) . '</th>
				<th width="10%" align="left" class="title" > ' . __('For Category', DOMAIN) . '</th>
				<th width="10%" align="left" class="title" > ' . __('Featured On Home Page', DOMAIN) . '</th>
				<th width="10%" align="left" class="title" > ' . __('Featured On Category Page', DOMAIN) . '</th>
				<th width="15%" align="left" class="title" >' . __('Total Price', DOMAIN) . '</th>
			</tr></thead>';

          $c = 0;
          foreach ($orderinfo as $oi) {
                    $c++;
                    if ($oi->post_id != 0) {
                              $product_image_arr = bdw_get_images_plugin($oi->post_id, 'thumb');
                              $product_image = @$product_image_arr[0]['file'];
                              if (!$product_image) {
                                        $product_image = TEVOLUTION_PAGE_TEMPLATES_URL . "tmplconnector/monetize/images/no-image.png";
                              }
                              $post = get_post($oi->post_id);
                              $trans_id = $oi->trans_id;

                              $trans_details = $wpdb->get_row("select * from $transection_db_table_name where trans_id=\"$trans_id\"");
                              if ($trans_details->payforcategory == 1) {
                                        $pfc = '<img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" />';
                              } else {
                                        $pfc = "-";
                              }
                              /* pay for home */
                              if ($trans_details->payforfeatured_h == 1) {
                                        $pffh = '<img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" />';
                              } else {
                                        $pffh = "-";
                              }

                              /* pay for featured on category */
                              if ($trans_details->payforfeatured_c == 1) {
                                        $pffc = '<img src="' . TEVOLUTION_PAGE_TEMPLATES_URL . 'tmplconnector/monetize/images/icon-yes.png" />';
                              } else {
                                        $pffc = "-";
                              }
                              if ($c % 2 == 0 || $c == 0) {
                                        $class = "alternate";
                              } else {
                                        $class = "";
                              }

                              $message .= '<tr class="' . $class . '">
								<td class="row1"><a href="' . get_permalink($post->ID) . '"><img src="' . $product_image . '" width=60 height=60 /></a></td>
								<td class="row1" ><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></td>
								<td class="row1 tprice"  align="left">' . $trans_details->user_name . '</td>
								<td class="row1 tprice"  align="left">' . $trans_details->payment_method . '</td>
								<td class="row1 tprice"  align="left">' . $pfc . '</td>
								<td class="row1 tprice"  align="left">' . $pffh . '</td>
								<td class="row1 tprice"  align="left">' . $pffc . '</td>
								<td class="row1 tprice"  align="left">' . fetch_currency_with_position($trans_details->payable_amt, 2) . '</td>
							</tr>';
                    }
          }
          $message .='</table>';
          if ($post_id != '' || $post_id != 0) {
                    return $message;
          } else {
                    return '';
          }
}

/* return the detailed information of user who completed transaction pass as argument in function */

function get_order_user_info($orderId, $isshow_paydetail = 0) {
          global $wpdb, $prd_db_table_name, $transection_db_table_name;
          $ordersql = "select * from $transection_db_table_name where trans_id=\"$orderId\"";
          $orderinfo = $wpdb->get_results($ordersql);
          $orderinfo = $orderinfo[0];
          $post_id = $orderinfo->post_id;
          $message = '';
          $message .='
				<div class="trans_avatar">
					<div class="order_info">
						<p> <span class="span"> ' . get_avatar($orderinfo->pay_email, 75) . '  </p>
					</div>
				</div>
				<div class="trans_user_info">
					<div class="order_info">
						<p> <span class="span"> ' . __('Username', DOMAIN) . ' </span> : <span class="trans_strong">' . $orderinfo->user_name . '</span> </p>
					</div>
					<div class="order_info">
						<p> <span class="span"> ' . __('User Email', DOMAIN) . ' </span> : <span class="trans_strong">' . $orderinfo->pay_email . '</span> </p>
					</div>
				</div>';
          return $message;
}

/* add package details meta box in backend */

function post_price_package($post) {
          global $post, $post_type, $post_id;
          if (!$post && isset($_GET['post']) && $_GET['post'] != '') {
                    $post = get_post($_GET['post']);
          }

          if ($post) {
                    $post_type = $post->post_type;
                    $post_id = $post->ID;
          }
          $package_select = get_post_meta(@$post_id, 'package_select', true);
          if ($package_select != '' && $package_select != 0 && $post_type != 'page') {
                    add_meta_box("package_details", "Package Details", "price_package_meta_box", $post_type, "side", "high");
          }
}

/* Display package details */

function price_package_meta_box() {
          global $post;
          $package_id = get_post_meta($post->ID, 'package_select', true);
          $alive_days = get_post_meta($post->ID, 'alive_days', true);
          $featured_c = (get_post_meta($post->ID, 'featured_c', true) == 'c') ? '' . __('Yes', DOMAIN) : '' . __('No', DOMAIN);
          $featured_h = (get_post_meta($post->ID, 'featured_h', true) == 'h') ? '' . __('Yes', DOMAIN) : '' . __('No', DOMAIN);
          if (function_exists('fetch_currency_with_position')) {
                    $paid_amount = fetch_currency_with_position(get_post_meta($post->ID, 'paid_amount', true));
          }

          $package_name = get_the_title($package_id);
          ?>
          <p><label><?php echo __('Package Name: ', ADMINDOMAIN); ?></label><strong><?php echo $package_name; ?></strong></p>
          <p><label><?php echo __('Total Amount: ', ADMINDOMAIN); ?></label><strong><?php echo $paid_amount; ?></strong></p>
          <p><label><?php echo __('Alive Days: ', ADMINDOMAIN); ?></label><strong><?php echo $alive_days; ?></strong></p>
          <p><label><?php echo __('Featured for home page? : ', ADMINDOMAIN); ?></label><strong><?php echo $featured_h; ?></strong></p>
          <p><label><?php echo __('Featured for category page? : ', ADMINDOMAIN); ?></label><strong><?php echo $featured_c; ?></strong></p>
          <?php
}

/* 	Sort ordering of price package	 */
add_action('wp_ajax_price_package_order', 'tevolution_price_package_order');

function tevolution_price_package_order() {

          $user_id = get_current_user_id();
          if (isset($_REQUEST['paging_input']) && @$_REQUEST['paging_input'] != 0 && @$_REQUEST['paging_input'] != 1) {
                    $package_per_page = get_user_meta($user_id, 'package_per_page', true);
                    $j = @$_REQUEST['paging_input'] * $package_per_page + 1;
                    $test = '';
                    $i = $package_per_page;
                    for ($j; $j >= count($_REQUEST['price_package_order']); $j--) {
                              if ($_REQUEST['price_package_order'][$i] != '') {
                                        wp_update_post(array('ID' => @$_REQUEST['price_package_order'][$i], 'menu_order' => $j,));
                              }
                              $i--;
                    }
          } else {
                    $j = 1;
                    for ($i = 0; $i < count($_REQUEST['price_package_order']); $i++) {
                              wp_update_post(array('ID' => @$_REQUEST['price_package_order'][$i], 'menu_order' => $j,));
                              $j++;
                    }
          }
          exit;
}

/*
 * function that change the old price package is_featured option to is_home_featured and is_category_featured.
 */

function tmpl_change_is_featured_option() {
          /* check whether price package is update or not */
          if (get_option('update_price_package') != 'updated') {
                    global $post, $wp_query, $monetization, $wpdb;
                    $args = array(
                              'post_type' => 'monetization_package',
                              'posts_per_page' => -1,
                              'post_status' => array('publish'),
                              'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                                  'key' => 'is_featured',
                                                  'value' => '1',
                                                  'compare' => '='
                                        )
                              )
                    );
                    $post_query = null;
                    $post_query = new WP_Query($args);
                    $post_meta_info = $post_query;
                    $monetization = new monetization();
                    if ($post_meta_info) {
                              while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
                                        $listing_price_info = $monetization->templ_get_price_info($post->ID);
                                        update_post_meta($post->ID, 'is_home_page_featured', 1);
                                        update_post_meta($post->ID, 'is_category_page_featured', 1);
                                        update_post_meta($post->ID, 'home_page_alive_days', $listing_price_info[0]['alive_days']);
                                        update_post_meta($post->ID, 'cat_page_alive_days', $listing_price_info[0]['alive_days']);
                              endwhile;
                    }
                    update_option('update_price_package', 'updated'); /* tmp variable set to check whether price package is update or not */
          }
}

/*
 * ajax function to fetch the category for post type while add or editing price package
 */
add_action('wp_ajax_ajax_categories_dropdown', 'ajax_categories_dropdown');

function ajax_categories_dropdown() {

          $my_post_type = explode(",", $_REQUEST['post_type']);
          $result = '';
          $category_li = '';
          $result .= '<ul class="categorychecklist form_cat" data-wp-lists="list:listingcategory" id="category_checklist"><li>
		<input type="checkbox" name="selectall" id="selectall" class="checkbox" onclick="displaychk_frm();" />
		<label for="selectall">&nbsp;' . __('Select All', DOMAIN) . '</label>
	</li>';

          $pkg_id = $_REQUEST['package_id'];
          $scats = $_REQUEST['scats'];
          $pid = explode(',', $scats);

          /* tmpl_remove_terms_clauses filter use for remove wpml language filter in taxonomy terms clauses */
          $remove_terms_clauses = apply_filters('tmpl_remove_terms_clauses', array('monetization'));
          /* Remove stitepress terms claises filer for display all langauge wise category show  */
          if ((isset($_REQUEST['page']) && in_array($_REQUEST['page'], $remove_terms_clauses) ) && is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                    global $sitepress;
                    remove_filter('terms_clauses', array($sitepress, 'terms_clauses'), 10, 4);
          }

          if ($_REQUEST['post_type'] == 'all' || $_REQUEST['post_type'] == 'all,') {
                    $custom_post_types_args = array();

                    $custom_post_types = get_option("templatic_custom_post");
                    $result .= tmpl_get_wp_category_checklist_monetize_plugin($pkg_id, array('taxonomy' => 'category', 'popular_cats' => true, 'selected_cats' => $pid));
                    foreach ($custom_post_types as $content_type => $content_type_label) {
                              $taxonomy = $content_type_label['slugs'][0];

                              $result .= "<li><label style='font-weight:bold;'>" . $content_type_label['taxonomies'][0] . "</label></li>";
                              $result .= tmpl_get_wp_category_checklist_monetize_plugin($pkg_id, array('taxonomy' => $taxonomy, 'popular_cats' => true, 'selected_cats' => $pid));
                    }
          } else {

                    $my_post_type = explode(",", substr($_REQUEST['post_type'], 0, -1));
                    foreach ($my_post_type as $_my_post_type) {
                              if ($_my_post_type != 'all') {
                                        $taxonomy = get_taxonomy($_my_post_type);
                                        $result .= "<li><label style='font-weight:bold;'>" . $taxonomy->labels->name . "</label></li>";
                                        $result .= tmpl_get_wp_category_checklist_monetize_plugin($pkg_id, array('taxonomy' => $_my_post_type, 'popular_cats' => true, 'selected_cats' => $pid));
                              }
                    }
          }
          $result .= '</ul>';
          echo $result;
          exit;
}

/*
  Display the categories check box like wordpress - wp-admin/includes/meta-boxes.php
 */

function tmpl_get_wp_category_checklist_monetize_plugin($post_id = 0, $args = array()) {
          global $cat_array;
          $category_result = '';
          $defaults = array(
                    'descendants_and_self' => 0,
                    'selected_cats' => false,
                    'popular_cats' => false,
                    'walker' => null,
                    'taxonomy' => 'category',
                    'checked_ontop' => true
          );
          if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != "") {
                    $place_cat_arr = $cat_array;
                    $post_id = $post_id;
          }

          $args = apply_filters('wp_terms_checklist_args', $args, $post_id);
          $template_post_type = get_post_meta($post->ID, 'submit_post_type', true);
          extract(wp_parse_args($args, $defaults), EXTR_SKIP);

          if (empty($walker) || !is_a($walker, 'Walker'))
                    $walker = new Tev_Walker_Category_Checklist_Backend;

          $descendants_and_self = (int) $descendants_and_self;

          $args = array('taxonomy' => $taxonomy);

          $tax = get_taxonomy($taxonomy);
          $args['disabled'] = !current_user_can($tax->cap->assign_terms);

          if (is_array($selected_cats))
                    $args['selected_cats'] = $selected_cats;
          elseif ($post_id)
                    $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
          else
                    $args['selected_cats'] = array();

          if (is_array($popular_cats))
                    $args['popular_cats'] = $popular_cats;
          else
                    $args['popular_cats'] = get_terms($taxonomy, array('get' => 'all', 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false));

          if ($descendants_and_self) {
                    $categories = (array) get_terms($taxonomy, array('child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 0));
                    $self = get_term($descendants_and_self, $taxonomy);
                    array_unshift($categories, $self);
          } else {
                    $categories = (array) get_terms($taxonomy, array('get' => 'all'));
          }

          if ($checked_ontop) {
                    /* Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache) */
                    $checked_categories = array();
                    $keys = array_keys($categories);
                    $c = 0;
                    foreach ($keys as $k) {
                              if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                                        $checked_categories[] = $categories[$k];
                                        unset($categories[$k]);
                              }
                    }

                    /* Put checked cats on top */
                    $category_result .= call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
          }
          /* Then the rest of them */

          $category_result .= call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
          if (empty($categories) && empty($checked_categories)) {

                    $category_result .= '<span style="font-size:12px;float:left;color:red;">' . sprintf(__('You have not created any category for %s post type. So, this listing will be submited as uncategorized.', DOMAIN), $template_post_type) . '</span>';
          }
          return $category_result;
}

/* function change transaction status */
if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'transcation' || $pagenow == 'edit.php') {
          add_action('admin_footer', 'tmpl_change_transstatus');
}

function tmpl_change_transstatus() {
          ?>
          <script type="text/javascript">
                    var chkTransstatus = null;
                    function change_transstatus(tid, post_id) {
                         if (tid == "")
                         {
                              jQuery("#p_status_" + tid).html("");
                              return false;
                         }

                         /* Ajax request for locate change transaction status */
                         chkTransstatus = jQuery.ajax({
                              url: ajaxUrl,
                              type: 'POST',
                              async: true,
                              data: 'action=tmpl_ajax_update_status&post_id=' + post_id + '&trans_id=' + tid,
                              beforeSend: function () {
                                   if (chkTransstatus != null) {
                                        chkTransstatus.abort();
                                   }
                              },
                              success: function (results) {
                                   jQuery("#p_status_" + tid).html(results);
                              }
                         });
                    }
          </script>
          <?php
}
?>