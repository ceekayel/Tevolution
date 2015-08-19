<?php
/* * ************************* LOAD THE BASE CLASS *******************************
 * The Tmpl_WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
global $pagenow;

if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}
/* * ************************ CREATE A PACKAGE CLASS *****************************
 * Create a new list table package that extends the core Tmpl_WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we need to define and override some methods so that our data can be display exactly the way we need it to be.

 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 */

class templatic_List_Table extends Tmpl_WP_List_Table {
          /*           * *** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
           * Call a function that will return all the data in an array and we will assign that result to a variable $package_data. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $package_data */

          function fetch_package_meta_data($post_id = '', $post_title = '') {
                    $pkg_type = get_post_meta($post_id, 'package_type', true);
                    $amount = get_post_meta($post_id, 'package_amount', true);
                    $recurring = get_post_meta($post_id, 'recurring', true);

                    if ($recurring == 1) {
                              $validity = get_post_meta($post_id, 'billing_num', true);
                              $validity_per = get_post_meta($post_id, 'billing_per', true);
                    } else {
                              $validity = get_post_meta($post_id, 'validity', true);
                              $validity_per = get_post_meta($post_id, 'validity_per', true);
                    }
                    $status = get_post_meta($post_id, 'package_status', true);
                    if ($validity_per == 'D') {

                              if ($validity == 1) {
                                        $validity_d = __('Day', ADMINDOMAIN);
                              } else {
                                        $validity_d = __('Days', ADMINDOMAIN);
                              }
                              $validity_per = $validity . "&nbsp;" . $validity_d;
                    } elseif ($validity_per == 'M') {
                              if ($validity == 1) {
                                        $validity_m = __('Month', ADMINDOMAIN);
                              } else {
                                        $validity_m = __('Months', ADMINDOMAIN);
                              }

                              $validity_per = $validity . "&nbsp;" . $validity_m;
                    } else {
                              if ($validity == 1) {
                                        $validity_y = __('Year', ADMINDOMAIN);
                              } else {
                                        $validity_y = __('Years', ADMINDOMAIN);
                              }

                              $validity_per = $validity . "&nbsp;" . $validity_y;
                    }
                    if ($status == '1') {
                              $package_status = "<font color='green'>" . __('Active', ADMINDOMAIN) . "</font>";
                    } else {
                              $package_status = "<font color='red'>" . __('Inactive', ADMINDOMAIN) . "</font>";
                    }
                    $package_type = get_post_meta($post_id, 'package_type', true);
                    if ($package_type == 2) {
                              $package_type = __('Subscription', ADMINDOMAIN);
                    } else {
                              $package_type = __('Single Submission', ADMINDOMAIN);
                    }
                    $submit_form_package_url = '';
                    $tevolution_post_type = tevolution_get_post_type();
                    foreach ($tevolution_post_type as $post_type) {
                              if ($post_type != 'admanager' && (in_array($post_type, explode(",", get_post_meta($post_id, 'package_post_type', true))) || in_array('all', explode(",", get_post_meta($post_id, 'package_post_type', true))) )) {
                                        global $post, $wp_query;
                                        $args = array(
                                                            'post_type' => 'page',
                                                            'posts_per_page' => -1,
                                                            'post_status' => array('publish'),
                                                            'meta_query' => array(
                                                                      'relation' => 'AND',
                                                                      array(
                                                                                'key' => 'submit_post_type',
                                                                                'value' => $post_type,
                                                                                'compare' => '='
                                                                      ),
                                                                      array(
                                                                                'key' => 'is_tevolution_submit_form',
                                                                                'value' => 1,
                                                                                'compare' => '='
                                                                      )
                                                            )
                                        );

                                        $post_query = null;
                                        $post_query = new WP_Query($args);
                                        $post_meta_info = $post_query;
                                        if ($post_meta_info->have_posts()) {
                                                  while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
                                                            /* $post_link = "'".'#package_link_'.$post_type.'_'.$post->ID.'_'.$post_id."'"; */
                                                            $post_link = '#package_link_' . $post_type . '_' . $post->ID . '_' . $post_id;
                                                            $submit_form_package_url .= "<div><span>" . __('Submit', ADMINDOMAIN) . ' ' . ucfirst($post_type) . "</span> <input type='hidden' value='" . get_permalink($post->ID) . "?pkg_id=" . $post_id . "' id='package_link_" . $post_type . "_" . $post->ID . "_" . $post_id . "'><a onclick='prompt(\"URL:\", jQuery(\"$post_link\").val()); return false;' class='link' href='#'>" . __('Short link', ADMINDOMAIN) . "</a></div>";
                                                  endwhile;
                                                  wp_reset_query();
                                                  wp_reset_postData();
                                        }
                              }
                    }
                    $meta_data = array(
                              'ID' => $post_id,
                              'title' => '<strong><a href="' . site_url() . '/wp-admin/admin.php?page=monetization&action=edit&tab=packages&package_id=' . $post_id . '">' . $post_title . '</a></strong><input type="hidden" value="' . $post_id . '" name="price_package_order[]">',
                              'package_type' => $package_type,
                              'package_amount' => fetch_currency_with_position($amount),
                              'validity' => $validity_per,
                              'package_status' => $package_status,
                              'package_link' => $submit_form_package_url
                    );
                    return apply_filters('tevolution_pricepackage_meta_data', $meta_data, $post_id);
          }

          function package_data() {
                    global $post, $paged, $query_args;
                    $package_data = array();
                    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                    if (isset($_POST['s']) && $_POST['s'] != '') {
                              $search_key = $_POST['s'];
                              $args = array(
                                        'post_type' => 'monetization_package',
                                        'posts_per_page' => -1,
                                        'post_status' => array('publish'),
                                        'paged' => $paged,
                                        's' => $search_key,
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC'
                              );
                    } else {
                              $args = array(
                                        'post_type' => 'monetization_package',
                                        'posts_per_page' => -1,
                                        'paged' => $paged,
                                        'post_status' => array('publish'),
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC'
                              );
                    }
                    $post_query = null;
                    $post_query = new WP_Query($args);
                    while ($post_query->have_posts()) : $post_query->the_post();
                              $package_data[] = $this->fetch_package_meta_data($post->ID, $post->post_title);
                    endwhile;
                    return $package_data;
          }

          /* EOF - FETCH PACKAGE DATA */

          /* DEFINE THE COLUMNS FOR THE TABLE */

          function get_columns() {
                    $columns = array(
                              'cb' => '<input type="checkbox" />',
                              'title' => __('Title', ADMINDOMAIN),
                              'package_type' => __('Type', ADMINDOMAIN),
                              'package_amount' => __('Amount', ADMINDOMAIN),
                              'validity' => __('Package Duration', ADMINDOMAIN),
                    );
                    $columns = apply_filters('tevolution_pricepackage_list_table_column', $columns);
                    $columns['package_link'] = __('Package Link', ADMINDOMAIN);
                    $columns['package_status'] = __('Status', ADMINDOMAIN);


                    return $columns;
          }

          function process_bulk_action() {
                    /* Detect when a bulk action is being triggered... */
                    if ('delete' === $this->current_action()) {
                              /* DELETE THE PACKAGE DATA */
                              global $wpdb, $post;
                              $ids = $_REQUEST['package_ids'];
                              foreach ($ids as $id) {
                                        /* DELETING THE PACKAGES ON CLICK OF DELETE BUTTON OF DASHBOARD METABOX */
                                        wp_delete_post($id);
                              }
                              ?>
                              <div class="updated fade below-h2" id="message">
                              <?php
                              if (count($ids) > 0) {
                                        echo count($ids) . "&nbsp;";
                                        echo __('Packages permanently deleted.', ADMINDOMAIN);
                              } else {
                                        echo __('Package permanently deleted.', ADMINDOMAIN);
                              }
                              ?>
                              </div>
                    <?php
                    }
          }

          function prepare_items() {
                    $per_page = $this->get_items_per_page('package_per_page', 10);
                    $columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
                    $hidden = array();
                    $sortable = array();
                    $sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
                    $this->_column_headers = array($columns, $hidden, $sortable);
                    $this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
                    $data = $this->package_data(); /* RETIRIVE THE PACKAGE DATA */

                    $current_page = $this->get_pagenum(); /* GET THE PAGINATION */
                    $total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
                    $this->found_data = array_slice($data, (($current_page - 1) * $per_page), $per_page); /* TRIM DATA FOR PAGINATION */
                    $this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
                    /* REGISTER PAGINATION OPTIONS */

                    $this->set_pagination_args(array(
                              'total_items' => $total_items, /* WE have to calculate the total number of items */
                              'per_page' => $per_page /* WE have to determine how many items to show on a page */
                    ));
          }

          /* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */

          function column_default($item, $column_name) {
                    switch ($column_name) {
                              case 'title':
                              case 'package_amount':
                              case 'package_type':
                              case 'validity':
                              case 'recurring':
                              case 'package_status':
                              case 'package_link':
                                        if ($column_name == 'package_type') {
                                                  if (@$item['recurring'] == 'Yes') {
                                                            $text = '- Recurring';
                                                  } else {
                                                            $text = '';
                                                  }
                                                  $item[$column_name] = $item[$column_name] . '<br/>' . $text;
                                        }
                                        return $item[$column_name];
                              default:
                                        return $item[$column_name]; /* Show the whole array for troubleshooting purposes */
                    }
          }

          /* DEFINE THE COLUMNS TO BE SORTED */

          function get_sortable_columns() {
                    $sortable_columns = array(
                              'title' => array('title', true)
                    );
                    return $sortable_columns;
          }

          function column_title($item) {
                    $actions = array(
                              'edit' => sprintf('<a href="?page=%s&action=%s&package_id=%s&tab=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID'], 'packages'),
                              'delete' => sprintf('<a href="?page=%s&action=%s&package_id=%s&tab=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['ID'], 'packages')
                    );

                    return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions, $always_visible = false));
          }

          function get_bulk_actions() {
                    $actions = array(
                              'delete' => 'Delete'
                    );
                    return $actions;
          }

          function column_cb($item) {
                    return sprintf(
                              '<input type="checkbox" name="package_ids[]" value="%s" />', $item['ID']
                    );
          }

}
?>