<?php
 add_action( 'pre_get_posts', 'templatic_search_query',1000);

 /*
 Name: templatic_search_query
 Desc: to set the search variable and overwrite wordpress query
 */
 function templatic_search_query($query){
	if($query->is_search()){ 

		if(wp_verify_nonce(@$_REQUEST['t'], 'tmpl_search') )	{	
			/* get the all fields from query string */
			$post_type1= array();
			if(is_array(@$_REQUEST['post_type'])){
				$post_type = array_merge($post_type1,@$_REQUEST['post_type']); // if there is multiple posttypes in URL
			}else{
				$post_type = array('post'); // singe post type
			}
			
			if(isset($_REQUEST['relation']) && $_REQUEST['relation'] !=''){
				$relation = $_REQUEST['relation'];
			}else{
				$relation = 'OR';
			}
	
			$keyword = $_REQUEST['search_key'];

		
			$meta_query = $get_meta ;    
			/* set the query for search from post meta ( custom fields )*/
			$query->set( 'post_type',$post_type );
			$query->set( 'post_status',array('publish') );
			$query->set( 'relation','OR' );
			add_filter('posts_where','templatic_search_query_where');
			//$query->set( 'meta_query', $meta_query );
		}		
		return $query;
	}
 }
/*
Name: tmpl_get_search_term_query
Desc: Search sub queries( if search with space )
*/
function tmpl_get_search_term_query($keyword,$field){
	if(preg_match('/\s/',$keyword))
		$query_search = explode(' ',$keyword);
	if(preg_match('/\s/',$keyword)){
				
				for($q=0; $q < count($query_search); $q++ ){
					$key = $query_search[$q];
					if($key !=''){
						if(($q+1) < (count($query_search))){
							$rel = "OR";
						}else{
							$rel='';
						}
						$tquery .= " $field like \"%$key%\" ".$rel; 
					}
				}
				
	}else{
				$tquery = "$field like \"%$keyword%\"";
	}
	return $tquery;
 }
 /*
 Name: templatic_search_query_where
 Desc: Return search query
 */
 function templatic_search_query_where($where){
	global $wpdb;
	$keyword = esc_html(get_search_query() );
	if(preg_match('/\s/',$keyword))
		$query_search = explode(' ',$keyword);

	if(is_array(@$_REQUEST['post_type'])){
		$post_type = implode("','",@$_REQUEST['post_type']); // if there is multiple posttypes in URL
	}else{
		$post_type = @$_REQUEST['post_type']; // singe post type
	}
	if(@$_GET['search_in_city'] ==1){
		$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_SESSION['post_city_id'].", pm.meta_value ))";	
	}

	if(isset($_REQUEST['relation']) && $_REQUEST['relation'] !=''){
		$relation = $_REQUEST['relation'];
	}else{
		$relation = 'OR';
	}
	
	$fields = array();
	/* Search from custom fields */
	if(!empty($_GET['mkey'])){
		foreach($_GET['mkey'] as  $v){
			if($v != 'cats' && $v != 'reviews' && $v != 'tags' && $v != 'post_city_id'){
			
				$fquery = tmpl_get_search_term_query($keyword,'pm.meta_value');
				
				$fields1 = $wpdb->get_col("select pm.post_id from $wpdb->postmeta pm ,$wpdb->posts p where p.ID = pm.post_id and  p.post_status = 'publish' and p.post_type IN ('".$post_type."') and pm.meta_key like \"%$v%\" and ( {$fquery} ) ");
				$srch_fields[] = $fields1;
				$fquery='';
			}
		}
	} 


	if(!empty($srch_fields)){
		$fvalueds =  implode(',', array_map('implode', array_fill(0, count($srch_fields), ','), $srch_fields));
		$srch_fields = array_filter(explode(',',$fvalueds));
	}else{
		$fvalueds = array();
		$srch_fields = array();
	}

	if(empty($srch_fields)){ $srch_fields =array(); }
	/* if there is a city name in search */
	if(!empty($_GET['mkey'])){
		if(in_array('post_city_id',@$_GET['mkey'])){
			$multicity_table = $wpdb->prefix . "multicity";	
			
			$cityquery = tmpl_get_search_term_query($keyword,'cityname');

			$cities = $wpdb->get_col("SELECT city_id FROM $multicity_table where {$cityquery} ");
			$cities = rtrim(implode(',',$cities));
			if(!empty($cities)){
				$incities = $wpdb->get_col("select pm.post_id from $wpdb->postmeta pm ,$wpdb->posts p where p.ID = pm.post_id and p.post_status = 'publish' and p.post_type IN ('".$post_type."') and pm.meta_key ='post_city_id' and FIND_IN_SET( ".$cities.", pm.meta_value )");
			}
			if(empty($incities)){ $incities = array(); }
		}
	}
	
	/* Search from Categories  */
	if(!empty($_GET['mkey'])){ 
		if(in_array('cats',@$_GET['mkey']) || in_array('tags',@$_GET['mkey'])){ 
			$tquery = tmpl_get_search_term_query($keyword,'c.name');
			if(@$_GET['search_in_city'] ==1){
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p, $wpdb->postmeta pm where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".$post_type."') and p.ID=pm.post_id and pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_SESSION['post_city_id'].", pm.meta_value ) group by  p.ID");
			}else{
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".$post_type."') group by  p.ID");
			}
			$srch_arr = $cats;
			//$where .= " ".$relation."  ($wpdb->posts.ID in ($srch_arr))";
		}
		
	}
	/* Search from comments content */
	if(!empty($_GET['mkey']) && in_array('reviews',@$_GET['mkey'])){
	
			$cquery = tmpl_get_search_term_query($keyword,'comment_content');
			
			$comments = $wpdb->get_col("SELECT $wpdb->comments.comment_post_ID FROM $wpdb->comments,$wpdb->posts p WHERE comment_approved = '1' and ( {$cquery} ) and p.ID =$wpdb->comments.comment_post_ID and  p.post_type IN ('".$post_type."')");
			if(!empty($srch_arr)){
				$srch_arr = array_merge($srch_arr,$comments);
			}else{
				$srch_arr = $comments;
			}
			
	}

	/* merge all category ids */
	
	if(!empty($srch_fields) ){  
		
		if($relation =='AND'): 
			if(!empty($incities)):
				$all_pids_arr =  array_filter(array_unique(array_intersect($cats,$comments,$srch_fields,$incities)));
				else:
				$all_pids_arr =  array_filter(array_unique(array_intersect($cats,$comments,$srch_fields)));
				endif;
		else: 
			if($cats ==''){ $cats = array(''); }
			if($comments==''){ $comments = array(); }
			
			
			$all_pids_arr =  array_filter(array_unique(array_merge($cats,$comments,$srch_fields))); 
			if(!empty($incities))
				$all_pids_arr =  array_intersect($all_pids_arr,$incities);
		endif;
		
		if(count($all_pids_arr) >=1):
			$all_pids_arr1 = implode(',',$all_pids_arr);
		else:
			$all_pids_arr1 = $all_pids_arr[0];
		endif;
		if(!empty($all_pids_arr1))
			$where .= " ".$relation." ($wpdb->posts.ID in ($all_pids_arr1))";
		
	}else{
		if(!empty($srch_arr)){
			if($cats ==''){ $cats = array(''); }
			if($comments==''){ $comments = array(); }
			
			if($relation =='AND'):
				if(!empty($incities)):
					if(!empty($srch_fields) && !empty($srch_arr)){
					$srch_fields = array_filter(array_unique(array_intersect($srch_fields,$srch_arr,$incities))); 
					}elseif(!empty($srch_fields) && empty($srch_arr)){
					$srch_fields = array_filter(array_unique(array_intersect($srch_fields,$incities))); 
					}elseif(empty($srch_fields) && !empty($srch_arr)){
					$srch_fields = array_filter(array_unique(array_intersect($srch_arr,$incities))); 
					}else{
						$srch_fields = $incities;
					}
				else:
					if(!empty($srch_fields) && !empty($srch_arr)){
						$srch_fields = array_filter(array_unique(array_intersect($srch_fields,$srch_arr))); 
					}elseif(!empty($srch_fields) && empty($srch_arr)){
						$srch_fields = $srch_fields;
					}else{
						$srch_fields = $srch_arr;
					}
				endif;
			else:
				if(!empty($srch_fields) && !empty($srch_arr)){
					$srch_fields = array_filter(array_unique(array_merge($srch_fields,$srch_arr))); /* $srch_arr is uniq array which fetch the results from reviews and categories in if else condition */
				}else{
					if(!empty($incitie)){
						$srch_fields = array_intersect($srch_fields,$incities);
					}else{
						$srch_fields = $srch_fields;
					}
				}
			endif;
			
			if(!empty($srch_fields)){
				
				$srch_arr = implode(',',$srch_fields);
			}
			
			if(is_array($srch_arr)){ $srch_arr = implode(',',$srch_arr);}
			if(!empty($srch_arr) || $srch_arr !='')
				$where .= " ".$relation." ($wpdb->posts.ID in ($srch_arr))";
		}
	}	
	

    return $where;
 }
?>