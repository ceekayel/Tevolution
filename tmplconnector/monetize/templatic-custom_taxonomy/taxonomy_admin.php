<?php
/*************************** LOAD THE BASE CLASS *******************************

 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
global $pagenow;
if(!class_exists('Tmpl_WP_List_Table')){
    include_once( WP_PLUGIN_DIR . '/Tevolution/templatic.php');
}

class taxonmy_list_table extends Tmpl_WP_List_Table
{
	/***** FETCH ALL THE DATA AND STORE THEM IN AN ARRAY *****
	* Call a function that will return all the data in an array and we will assign that result to a variable $_posttaxonomy. FIRST OF ALL WE WILL FETCH DATA FROM POST META TABLE STORE THEM IN AN ARRAY $_posttaxonomy */
	function fetch_taxonomy_data( $_posttaxonomy)
	{ 
		$tax_label  = $_posttaxonomy['labels']['name'];
		$tax_desc = (isset($_posttaxonomy['description']))?$_posttaxonomy['description'] :'';
		$tax_category = $_posttaxonomy['taxonomies'][0];
		$tax_tags = $_posttaxonomy['taxonomies'][1];
		$tax_slug = $_posttaxonomy['query_var'];
		
		$edit_url = admin_url("admin.php?page=custom_setup&ctab=custom_setup&action=edit-type&amp;post-type=$tax_slug");
		$meta_data = array(
			'title'	=> '<strong><a href="'.$edit_url.'">'.$tax_label.'</a></strong>',
			'tax_desc' 	=> $tax_desc,
			'tax_category' => $tax_category,
			'tax_tags' 	=> $tax_tags,
			'tax_slug' 	=> $tax_slug
			);
		return $meta_data;
	}
	/* fetch taxonomy data */
	function taxonomy_data()
	{
		global $post;
		$taxonomy_data =array();
		$posttaxonomy = apply_filters('tevolution_custom_post_type_list',get_option("templatic_custom_post"));
		if($posttaxonomy):
			foreach($posttaxonomy as $key=>$_posttaxonomy):
						$taxonomy_data[] = $this->fetch_taxonomy_data($_posttaxonomy);
			endforeach;
		endif;
		return $taxonomy_data;
	}
	/* eof - fetch taxonomy data */
	
	/* define the columns for the table */
	function get_columns()
	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Post Type Name',ADMINDOMAIN),
			'tax_desc' => __('Description',ADMINDOMAIN),
			'tax_category' => __('Taxonomy Name',ADMINDOMAIN),
			'tax_tags' => __('Tags',ADMINDOMAIN)
			);
		return $columns;
	}
	
	function process_bulk_action()
	{ 
		/*Detect when a bulk action is being triggered...*/
		if('delete' === $this->current_action() )
		{
			 $_SESSION['custom_msg_type'] = 'delete';
			 $post_type = get_option("templatic_custom_post");
			 $taxonomy = get_option("templatic_custom_taxonomy");
			 $tag = get_option("templatic_custom_tags");
			 foreach($_REQUEST['checkbox'] as $tax_post_type)
			  {
				 $taxonomy_slug = $post_type[$tax_post_type]['slugs'][0];
				 $tag_slug = $post_type[$tax_post_type]['slugs'][1];
				 
				 unset($post_type[$tax_post_type]);
				 unset($taxonomy[$taxonomy_slug]);
				 unset($tag[$tag_slug]);
				 update_option("templatic_custom_post",$post_type);
				 update_option("templatic_custom_taxonomy",$taxonomy);
				 update_option("templatic_custom_tags",$tag);
				 if(file_exists(get_template_directory()."/taxonomy-".$taxonomy_slug.".php"))
					unlink(get_template_directory()."/taxonomy-".$taxonomy_slug.".php");
				 if(file_exists(get_template_directory()."/taxonomy-".$tag_slug.".php"))
					unlink(get_template_directory()."/taxonomy-".$tag_slug.".php");
				 if(file_exists(get_template_directory()."/single-".$post_type.".php"))
					unlink(get_template_directory()."/single-".$post_type.".php");
			 }	 
			 wp_redirect(admin_url("admin.php?page=custom_setup&ctab=custom_setup"));
			 $_SESSION['custom_msg_type'] = 'delete';
			 exit;
		}
	}
    
	function prepare_items()
	{
		$per_page = $this->get_items_per_page('taxonomy_per_page', 10);
		$columns = $this->get_columns(); /* CALL FUNCTION TO GET THE COLUMNS */
        $hidden = array();
		$sortable = array();
        $sortable = $this->get_sortable_columns(); /* GET THE SORTABLE COLUMNS */
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action(); /* FUNCTION TO PROCESS THE BULK ACTIONS */
		$data = $this->taxonomy_data(); /* RETIRIVE THE PACKAGE DATA */
		
		/* FUNCTION THAT SORTS THE COLUMNS */
		function usort_reorder($a,$b)
		{
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; /*If no sort, default to title*/
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; /*If no order, default to asc*/
            $result = strcmp($a[$orderby], $b[$orderby]); /*Determine sort order*/
            return ($order==='asc') ? $result : -$result; /*Send final sort direction to usort*/
        }
		if(is_array($data))
	        usort( $data, 'usort_reorder');
		
		$current_page = $this->get_pagenum(); /* GET THE PAGINATION */
		$total_items = count($data); /* CALCULATE THE TOTAL ITEMS */
		if(is_array($data))
			$this->found_data = array_slice($data,(($current_page-1)*$per_page),$per_page); /* TRIM DATA FOR PAGINATION*/
		$this->items = $this->found_data; /* ASSIGN SORTED DATA TO ITEMS TO BE USED ELSEWHERE IN CLASS */
		/* REGISTER PAGINATION OPTIONS */
		
		$this->set_pagination_args( array(
            'total_items' => $total_items,      /*WE have to calculate the total number of items*/
            'per_page'    => $per_page         /*WE have to determine how many items to show on a page*/
        ) );
	}
	
	/* To avoid the need to create a method for each column there is column_default that will process any column for which no special method is defined */
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'cb':
			case 'title':
			case 'tax_desc':
			case 'tax_category':
			case 'tax_tags':
			case 'tax_slug':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; /*Show the whole array for troubleshooting purposes*/
		}
	}
	
	/* define the columns to be sorted */
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'title' => array('title',true)
			);
		return $sortable_columns;
	}
	
	/* define the links dispplaying below the title */
	function column_title($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&ctab=%s&action=%s&post-type=%s">Edit</a>',$_REQUEST['page'],'custom_setup','edit-type',$item['tax_slug']),
			'delete' => sprintf('<a href="?page=%s&post-type=%s">Delete Permanently</a>','delete-type',$item['tax_slug'])
			);
		
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions , $always_visible = false) );
	}
	
	/* define the bulk actions */
	function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete permanently'
			);
		return $actions;
	}
	
	/* checkbox to select all the taxonomies */
	function column_cb($item)
	{ 
		return sprintf(
			'<input type="checkbox" name="checkbox[]" id="checkbox[]" value="%s" />', $item['tax_slug']
			);
	}
}


/* this function will fetch all the post types */

function fetch_post_types_labels()
{
	$types = get_post_types('','objects');
	return $types;
}

/* filters to add a column on all usres page */

add_filter('manage_users_columns', 'add_post_type_users_column');
add_filter('manage_users_custom_column', 'view_post_type_user_custom_column', 10, 3);

/* function to display number of articles */
function view_post_type_user_custom_column($out, $column_name, $user_id)
{
	global $wpdb,$articles;
	switch ( $column_name )
	{		
		case $column_name :
			$post_type=str_replace(' num','',$column_name);
			$result = $wpdb->get_row("SELECT count(ID) as count FROM $wpdb->posts WHERE post_type = '".strtolower($post_type)."' AND post_author = ".$user_id." AND post_status = 'publish'");
			if( $result->count > 0 )
			{
				$articles = "<a href='edit.php?post_type=".strtolower($post_type)."&author=".$user_id."' class='edit' title='".__('View posts by this author',DOMAIN)."'>".$result->count."</a>";
			}
			else
			{
				$articles = $result->count;
			}
		break;
	}
	return $articles; 
}

/* function to add a column */
function add_post_type_users_column($columns)
{
	$types = fetch_post_types_labels();
	foreach($types as $key => $values )
	{
		if(in_array($key,tevolution_get_post_type()))
		{
			foreach( $values as $label => $val)
			{ 
				if($val->name != '')
				{
					$columns[$key.' num'] = $val->name;
				}
			}
		}
	}	
	return $columns;
}
/*
	Display the category wise display price package from backend
 */
add_action('admin_footer', 'tevolution_taxonomy_price_package');
function tevolution_taxonomy_price_package(){
	global $pagenow,$post;			
		/* Tevolution Custom Post Type custom field meta box */
	if($pagenow=='post.php' || $pagenow=='post-new.php'){			
		if(isset($_REQUEST['post_type']) && $_REQUEST['post_type']!=''){
			$posttype=$_REQUEST['post_type'];
		}else{
			$posttype=(get_post_type(@$_REQUEST['post']))? get_post_type(@$_REQUEST['post']) :'post';
		}
		$post_type_post['post']= (array)get_post_type_object( 'post' );			
		$custom_post_types=get_option('templatic_custom_post');
		$custom_post_types=array_merge($custom_post_types,$post_type_post);
		foreach($custom_post_types as $post_type => $value){
				if($posttype==$post_type){
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
		?>
          	<script type="text/javascript">
			jQuery(document).ready(function(){	
			   jQuery('input:checkbox[name^="tax_input"]').click(function(){								
				/*var value=jQuery('input:checkbox[name^="tax_input"]').val();*/
				var val='';
				jQuery('input:checkbox[name^="tax_input"]:checkbox:checked').each(function(i){
						val+= jQuery(this).val()+',';
				});				
				
				 var value=val.substr(0,val.length-1);			
				 var url;
				 var post_type='<?php echo $post_type?>';
				 var taxonomy='<?php echo $taxonomies[0];?>';
				 url="<?php echo TEMPL_PLUGIN_URL;?>/tmplconnector/monetize/templatic-monetization/ajax_price.php?pckid="+value+"&post_type="+post_type+"&taxonomy="+taxonomy+'&is_backend=1'				 
				 jQuery.ajax({
					   url: url, 
					   type: "GET",
					   cache: false,
					   success: function (html) {
							if(html==''){								
								jQuery('table#tvolution_price_package_fields #ajax_packages_checkbox').remove();
							}else{
								jQuery('table#tvolution_price_package_fields td div.backedn_package').add(html);
							}
					   }      
				    });
				 
			});
		});
			</script>
          <?php
			}
		}
	}
}

/*
	Set the permalink when new taxonomy created
*/

if((isset($_GET['custom_msg_type']) && $_GET['custom_msg_type']=='add') && (isset($_GET['page']) && $_GET['page']=='custom_setup') ){
	add_action('admin_init','tmpl_default_permalink_set');
}
function tmpl_default_permalink_set(){
	global $pagenow;
	if ( 'plugins.php' == $pagenow || 'themes.php' == $pagenow){ /* Test if theme is activate*/
		/*Set default permalink to postname start*/
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules();
		if(function_exists('flush_rewrite_rules')){
			flush_rewrite_rules(true);  
		}
	/*Set default permalink to postname end*/
	}
} ?>