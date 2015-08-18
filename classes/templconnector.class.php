<?php
class Templatic{
	var $file;
	var $version;
}
class Templatic_connector { 
	/*
	Name : templ_dashboard_bundles
	Description : Function contains bundles of file which creates the bunch of options in backend BOF 
	*/
	public function templ_dashboard_bundles(){
		
		$modules_array = array();
		$modules_array = array('templatic-custom_taxonomy','templatic-custom_fields','templatic-registration','templatic-monetization','templatic-manage_ip','templatic-claim_ownership','templatic-bulk_upload');
		$no_include = array('templatic-generalization','templ_header_section.php','general_settings.php','general_functions.php','templ_footer_section.php','images','.svn');
		$tab = @$_REQUEST['tab'];
		switch($tab){
			case 'overview':
				$oclass = "nav-tab-active";
				$title =__("Overview",ADMINDOMAIN);
				$oaclass ="active";
				$sclass ='';
				break;
			case 'setup-steps':
			   $sclass = "nav-tab-active";
			   $title = __("Setup steps",ADMINDOMAIN);
			   $oclass="";
				break;
			case 'extend':
			   $oclass = "nav-tab-active";
			   $title = __("Extend",ADMINDOMAIN);
			   $eaclass ="active";
			   $sclass="";
				break; 
			case '':
			   $oclass = "nav-tab-active";
			   $title = __("Overview",ADMINDOMAIN);
			   $sclass="";
				break;
		}
		echo '
		<div id="tevolution_bundled" class="metabox-holder wrapper widgets-holder-wrap">
		<table cellspacing="0" class="wp-list-tev-table postbox fixed pages ">
			<thead>
			<tr style="height:40px">
				<th scope="col" id="cb" class="revisions-meta manage-column column-cb check-column" style="">'.ucfirst($title).'</th>
			</tr>
			</thead>
			<tbody style="background:white; padding:40px;">
			<tr><td>
			<div >';
		
		
		/* This is the correct way to loop over the directory. */
			for($f =0; $f <count($modules_array); $f++)
			{					
				$file = $modules_array[$f];
				if(file_exists(TEMPL_MONETIZE_FOLDER_PATH.$file."/bundle_box.php")){
						require_once(TEMPL_MONETIZE_FOLDER_PATH.$file."/bundle_box.php" ); 
				}
			}
		
		/* to get t plugins */	
		
		echo '</div>
		</td></tr>
		</tbody></table>
		</div>';
	
	
	}	
	
	public function templ_dashboard_extends(){
		
		$modules_array = array();
		$modules_array = array('templatic-custom_taxonomy','templatic-custom_fields','templatic-registration','templatic-monetization','templatic-manage_ip','templatic-claim_ownership','templatic-bulk_upload');
		$no_include = array('templatic-generalization','templ_header_section.php','general_settings.php','general_functions.php','templ_footer_section.php','images','.svn');
		
		
		/* This is the correct way to loop over the directory. */
			
		do_action('templconnector_bundle_box');
			
		
		/* to get t plugins */	
	
	}
	/* -- Function contains bundles of file which creates the bunch of options in backend EOF - */
	public function templ_setup_steps(){
		
		$modules_array = array();
		$modules_array = array('templatic-custom_taxonomy','templatic-custom_fields','templatic-registration','templatic-monetization','templatic-manage_ip','templatic-claim_ownership','templatic-bulk_upload');
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_header_section.php' );
	
		echo '<div><div>
		<div id="tevolution_bundled" class="metabox-holder wrapper widgets-holder-wrap"><table cellspacing="0" class="wp-list-tev-table postbox fixed pages ">
			<thead>
			<tr style="height:40px">
				<th scope="col" id="cb" class="revisions-meta manage-column column-cb check-column" style="">'.ucfirst($title).'</th>
			</tr>
			</thead>
			<tbody style="background:white; padding:40px;">
			<tr><td>
			';
		
		
		/* This is the correct way to loop over the directory. */
			
				do_action('tevolution_setup_steps_box');
			
		
		/* to get t plugins */	
		
		echo '</td></tr>
		</tbody></table>
		</div>
		';
	
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_footer_section.php' );
	
	}
	
	/* -- Function contains bundles of file which creates the bunch of templatic other plugins list EOF - */
	function templ_extend(){
		$modules_array = array();
		$modules_array = array('templatic-custom_taxonomy','templatic-custom_fields','templatic-registration','templatic-monetization','templatic-manage_ip','templatic-claim_ownership','templatic-bulk_upload');
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_header_section.php' );
		?>
        <p class="tevolution_desc"><?php echo __('The plugins listed below will help you completely transform your website. For instance, installing the Booking plugin will allow you to manage a smaller hotel or a rental home. ',ADMINDOMAIN);?></p>
          <?php
		echo '<div><div>
		<div id="tevolution_bundled" class="metabox-holder wrapper widgets-holder-wrap"><table cellspacing="0" class="wp-list-tev-table postbox fixed pages ">
			<thead>
			<tr style="height:40px">
				<th scope="col" id="cb" class="revisions-meta manage-column column-cb check-column" style="">'.ucfirst($title).'</th>
			</tr>
			</thead>
			<tbody style="background:white; padding:40px;">
			<tr><td>
			';
		/* This is the correct way to loop over the directory. */			
		do_action('tevolution_extend_box');
		/* to get t plugins */			
		echo '</td></tr>
		</tbody></table>
		</div>
		';
	
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_footer_section.php' );
	}
	
	
	/* -- Function contains bundles of file which creates the bunch of paymentgateway plugin lists backend EOF - */
	function templ_payment_gateway(){
		$modules_array = array();
		$modules_array = array('templatic-custom_taxonomy','templatic-custom_fields','templatic-registration','templatic-monetization','templatic-manage_ip','templatic-claim_ownership','templatic-bulk_upload');
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_header_section.php' );
		?>
          <p class="tevolution_desc"><?php echo __('Offer new ways to pay using the plugins available below. You will also need a merchant account (with the payment processor) in order to use the downloaded plugin.',ADMINDOMAIN);?></p>
          <?php
		
		echo '<div>
		<div id="tevolution_bundled" class="metabox-holder wrapper widgets-holder-wrap"><table cellspacing="0" class="wp-list-tev-table postbox fixed pages ">
			<thead>
			<tr style="height:40px">
				<th scope="col" id="cb" class="revisions-meta manage-column column-cb check-column" style="">'.ucfirst($title).'</th>
			</tr>
			</thead>
			<tbody style="background:white; padding:40px;">
			<tr><td>
			';
		/* This is the correct way to loop over the directory. */
		do_action('tevolution_payment_gateway');
		/* to get t plugins */			
		echo '</td></tr>
		</tbody></table>		
		';
	
		require_once(TEMPL_MONETIZE_FOLDER_PATH.'templ_footer_section.php' );
	}
	/*
	Name : bdw_get_images_with_info
	Description :Return the images of post with attachment information
	*/
	function bdw_get_images_with_info($iPostID,$img_size='thumb') 
	{
    $arrImages =& get_children('order=ASC&orderby=menu_order ID&post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );
	$return_arr = array();
	if($arrImages) 
	{		
       foreach($arrImages as $key=>$val)
	   {
	   		$id = $val->ID;
			if($img_size == 'large')
			{
				$img_arr = wp_get_attachment_image_src($id,'full');	// THE FULL SIZE IMAGE INSTEAD
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}
			elseif($img_size == 'medium')
			{
				$img_arr = wp_get_attachment_image_src($id, 'medium'); //THE medium SIZE IMAGE INSTEAD
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
			}
			elseif($img_size == 'thumb')
			{
				$img_arr = wp_get_attachment_image_src($id, 'thumbnail'); // Get the thumbnail url for the attachment
				$imgarr['id'] = $id;
				$imgarr['file'] = $img_arr[0];
				$return_arr[] = $imgarr;
				
			}
	   }
	  return $return_arr;
	}
	}
}
?>