<?php
/*
 * mulitple image upload script for submit form.
 */

$tmpdata = get_option('templatic_settings');
$templatic_image_size =  @$tmpdata['templatic_image_size'];
if(!$templatic_image_size){ $templatic_image_size = '50'; }

?>
<script type="text/javascript" >
var temp = 1;
var html_var = '<?php echo $val['htmlvar_name']; ?>';
var $uc = jQuery.noConflict();
var imgArr = new Array();

jQuery.noConflict();
jQuery(document).ready(function($){
	jQuery("#imagelist").sortable({
		 connectWith: '#deleteArea',
		 'start': function (event, ui) {
			   /*jQuery Ui-Sortable Overlay Offset Fix */
			   if ($.browser.webkit) {
				  wscrolltop = $(window).scrollTop();
			   }
		 },
		 'sort': function (event, ui) {
			   /* jQuery Ui-Sortable Overlay Offset Fix */
			   if ($.browser.webkit) {
				  ui.helper.css({ 'top': ui.position.top + wscrolltop + 'px' });
			   }
		 },
		 update: function(event, ui){
			/*Run this code whenever an item is dragged and dropped out of this list*/
			/*var order = $(this).sortable('serialize');*/
			var image_names = '';
			jQuery('#imagelist p img').css('cursor','default').each(function() {
				var imagename = jQuery(this).attr( 'name' );
				image_names = image_names + imagename + ',';
			});
			 
			 jQuery.ajax({
				 url: '<?php echo plugin_dir_url( __FILE__ ); ?>processImage.php',
				 type: 'POST',
				 /*data: order+'&i='+image_names,*/
				 data: 'i='+image_names,
				 success:function(result){					 	
						document.getElementById('imgarr').value = result;
					}
			 });			 
	 	}
	 });		 
	
});
function delete_image(name,img_name,pid)
{
	var li_id=name;	
	var image_arr=document.getElementById('imgarr').value;
	jQuery.ajax({
		 url: '<?php echo plugin_dir_url( __FILE__ ); ?>processImage.php',
		 type: 'POST',
		 data: 'name='+img_name+'&image_arr='+image_arr+'&pid='+pid,				 
		 success:function(result){			 
				document.getElementById('imgarr').value = result;
				jQuery('#'+li_id).remove();	
				jQuery('.'+li_id).remove();				
		}				 
	 });	
}
</script>
<style type="text/css">
img_table {
	margin-top: 20px;
	}
li >img {
	cursor: move;
	}
#imagelist { width:700px; }
#imagelist div { float:left; }
#imagelist p >img {
	cursor: move;
	}
#imagelist div p { position:relative; padding: 0; }
#imagelist div p span {
	position:absolute;
	top:-6px;
	right:-6px;
	}
.uploadfilebutton{ position:absolute;font-size:30px; cursor:pointer; z-index:2147483583; top:-10px; left:0; opacity:0; }
</style>

<?php $button_text = apply_filters('tmpl_image_uploader_text',__("Upload Image", 'templatic')); ?>
	 <div class="clearfix image_gallery_description">
     <p class="add_tevolution_images hide-if-no-js">
		<!-- Multi image uploader button -->
		<div class="<?php echo $name; ?>-sm-preview"></div>
			<div id="fancy-contact-form">
			<div class="dz-default dz-message" ><span  id="fancy-<?php echo $name; ?>"><span><i class="fa fa-folder"></i> <?php _e("Upload Image", 'templatic'); ?></span></div><span id="status" class="message_error2 clearfix"></span></span></div>
			<input type="hidden" name="submitted" value="1">
       		<p class="max-upload-size">
			<?php
				_e( 'Maximum upload file size: ','templatic');
				echo esc_html($templatic_image_size).'KB';
			?>
            </p>
		</div>
		<script type="text/javascript" async>
			var thumb_src = '<?php echo get_template_directory_uri();?>/images/tmp/';
			jQuery(document).ready(function(){
				var settings = {
					url: '<?php echo plugin_dir_url( __FILE__ ); ?>drag-uploadfile.php',
					dragDrop:true,
					fileName: "uploadfile[]",
					allowedTypes:"jpeg,jpg,png,gif,doc,zip",	
					returnType:"json",
					multiple:true,
					showAbort:false,
					showProgress:true,
					onSuccess:function(files,data,xhr)
					{
						jQuery('#post_images_error').html('');
						var status=$uc('#status');
						status.text("");
						$uc('#files .success').each(function(){
								counter = parseFloat(this.id) + 1;
						});
						jQuery('.ajax-file-upload-statusbar').css('display','none');
						data = data+'';
						if(data == 'error'){
							jQuery('#post_images_error').html("<?php _e('Image can&rsquo;t be uploaded due to some error.','templatic'); ?>");
							jQuery('.ajax-file-upload-statusbar').css('display','none');
							return false;
						}
						/*Image size validation*/
						 if(data == 'LIMIT'){
							status.text("<?php _e('Your image size must be less then','templatic'); echo " ".$templatic_image_size." "; _e('kilobytes','templatic'); ?>");
							jQuery('.ajax-file-upload-statusbar').hide();
							return false;
						 }
						
						/* Start Limit Code  */
						if(data > 10 )
						  {
							status.text("<?php _e('You can upload maximum 10 images','templatic'); ?>");
							return false;
						  }
						 
						 var counter = 0;
						 $uc('#imagelist div').each(function(){
								counter = counter + 1;
						 });
						limit = (data.split(",").length + counter) - 1;
						if(parseFloat(limit) >= 10)
						  {
							status.text("<?php _e('You can upload maximum 10 images','templatic'); ?>");
							return false;
						  }
						/* End Limit Code */
						var id_name = data.split('.'); 
						var img_name = '<?php echo bloginfo('template_url')."/images/tmp/"; ?>'+id_name[0]+"."+id_name[1];
						
						
						$uc('<div id=i_'+data+' class='+id_name[0]+'>').appendTo('#imagelist').html('<p id="'+id_name[0]+'"><img width="60px" height="60px" src="'+img_name+'" name="'+data+'" /><span><i class="fa fa-times-circle redcross" onClick="delete_image(\''+id_name[0]+'\',\''+data+'\');"></i></span></p>');
						
						
						var imgArr_i = 0;
						$uc('#files #imagelist p img').each(function(){
							imgArr[imgArr_i] = this.name;
							imgArr_i++;
						});
						document.getElementById('imgarr').value = imgArr;
					},
					showDelete:true,
					deleteCallback: function(data,pd)
					{
					for(var i=0;i<data.length;i++)
					{
						jQuery.post("delete.php",{op:"delete",name:data[i]},
						function(resp, textStatus, jqXHR)
						{
							/* Show Message  */
							jQuery("#status").append("<div>File Deleted</div>");      
						});
					 }      
					pd.statusbar.hide(); /* You choice to hide/not. */

				}
				}
				var uploadObj = jQuery("#fancy-"+'<?php echo $name; ?>').uploadFile(settings);
			});
		</script>
	 </p>
  
<table width="70%" align="center" border="0" class="img_table">
<tr>
    <td>
       <?php if(isset($_REQUEST['pid'])){			
			 $thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'full');		
			  if($thumb_img_arr):
                foreach ($thumb_img_arr as $val) :
					 $tmpimg = explode("/",$val['file']);
					 $name = end($tmpimg);
					 if($name!="")
						 $image_name.=$name.",";
				endforeach;	   
			  endif;
	   }
	   if(isset($_SESSION["custom_fields"]['imgarr']) && $_SESSION["custom_fields"]['imgarr'] != '' &&  !$_REQUEST['pid'] )
        {
			$image_upload = explode(",",$_SESSION["custom_fields"]['imgarr']);
            foreach($image_upload as $image_id=>$val)
            {
				if($val !='')
				$tmp =explode("/",$val);
				$name = end($tmp);
				if($val!="")
					$image_name.=$name.",";				
			}
		}
		if(isset($_REQUEST['imgarr']) && $_REQUEST['imgarr']!='')
		{
			$image_name = $_REQUEST['imgarr'];
		}
	   ?>
        <input name="imgarr" id="imgarr" value="<?php echo @$image_name;?>" type="hidden"/>
        <table>
        	<tr>
            <td id="files">
            	<div id="imagelist">
                
               
        <?php
        $i = 0;
		if(isset($_SESSION["custom_fields"]['imgarr']) && $_SESSION["custom_fields"]['imgarr'] != '' &&  !$_REQUEST['pid'] )
        {
			$image_upload = explode(",",$_SESSION["custom_fields"]['imgarr']);
            foreach($image_upload as $image_id=>$val)
            {
				
                $thumb_src = get_template_directory_uri().'/images/tmp/'.$val;		
				if($val !='')
					$tmpd = explode("/",$val);
					$name = end($tmpd);
					$img_name= explode('.',$name);
				if(file_exists($thumb_src) && $val!=""):
           ?>
                    <div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $thumb_src; ?>" height = "60px" width = "60px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<i class="fa fa-times-circle redcross" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','');"></i>
                        </span>   
                         </p>               
                    </div>
           <?php
		   		endif;
            }
        }
       ?>
       <?php
       if(isset($_REQUEST['pid']) && !$_REQUEST['backandedit']) :
            $thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'full');				
            $i = 0;
            if($thumb_img_arr):
                foreach ($thumb_img_arr as $val) :
				$tmpimg = explode("/",$val['file']);
                $name = end($tmpimg );
				$img_name= explode('.',$name);
               /*$thumb_src = get_template_directory_uri().'/thumb.php?src='.$val['file'];			  */
			   if($name!=""):
           ?>                    
                    <div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $val['file']; ?>" height = "60px" width = "60px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<i class="fa fa-times-circle redcross" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','<?php echo $val['id']; ?>');" ></i>
                        </span>   
                         </p>               
                    </div>
            <?php
					endif;
                 endforeach;
            endif;
        endif;
       ?>
        <?php
        if(isset($_REQUEST['imgarr']) && $_REQUEST['imgarr']!= '' && !isset($_REQUEST['pid'])) :
			$img_arr = explode(",",$_REQUEST['imgarr']);
            foreach($img_arr as $image_id=>$val)
            {
                $thumb_src = get_template_directory_uri().'/images/tmp/'.$val;	
				$src = TEMPLATEPATH.'/images/tmp/'.$val;	
				if($val !='')
					$tmpd = explode("/",$val);
					$name = end($tmpd);
					$img_name= explode('.',$name);
					if(file_exists($src) && $val!=""):
	    	       ?>
                    <div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $thumb_src; ?>" height = "60px" width = "60px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<img align="right" id="cross" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','');" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/cross.png" alt="delete" class="redcross" />
                        </span>   
                         </p>               
                    </div>
           <?php
		   		endif;
            }
        endif;
       ?>
        <?php 
            if(!empty($_SESSION["file_info"]) && isset($_REQUEST['backandedit']) && isset($_REQUEST['pid']) ):
                global $upload_folder_path;
                $i = 0;				
                foreach($_SESSION["file_info"] as $image_id=>$val):
                    $final_src = TEMPLATEPATH.'/images/tmp/'.$val;
					$src = get_bloginfo('template_directory').'/images/tmp/'.$val;
					$name = end(explode("/",$val));
					$img_name= explode('.',$name);					
                    if($val):
                    if(file_exists($final_src)):
					
        ?>
        			<div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $src; ?>" height = "60px" width = "60px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<i class="fa fa-times-circle redcross" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','');"  ></i>
                        </span>   
                         </p>               
                    </div>
                      
                   <?php else: ?>
                   <?php
                  		$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'full');
                        foreach($thumb_img_arr as $value):
                            $name = end(explode("/",$value['file']));
							$img_name= explode('.',$name);							
                            if($name == $val):	
							
                   ?>
                   		<div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                            <p id='<?php echo $img_name[0]?>'>
                            <img src="<?php echo $value['file']; ?>" height = "60px" width = "60px" name="<?php echo $name; ?>" alt="" />                       
                            <span>
                            	<i class="fa fa-times-circle redcross" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','<?php echo $value['id']; ?>');" ></i>
                            </span>   
                             </p>               
                        </div>
                        
                    <?php								
                            endif;
                       endforeach;
                    ?>
             
             <?php 
                    endif;
                    endif;
                    $i++;
                endforeach; 
            endif;
             ?>
              </div>
	</td>
   </tr>
  </table>
</td>
</tr>
</table>
