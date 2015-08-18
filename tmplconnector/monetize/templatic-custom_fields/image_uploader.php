<?php
$tmpdata = get_option('templatic_settings');
$templatic_image_size =  @$tmpdata['templatic_image_size'];
if(!$templatic_image_size){ $templatic_image_size = '50'; }
wp_enqueue_script( 'jquery-ui-sortable' );
?>
<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ); ?>js/ajaxupload.3.5.js" ></script>
<script type="text/javascript">
var temp = 1;
var html_var = '<?php echo $val['htmlvar_name']; ?>';
var $uc = jQuery.noConflict();
$uc(function()
{
	var btnUpload=$uc('#uploadimage');
	var status=$uc('#status');
	new AjaxUpload(btnUpload, {
	name: 'uploadfile[]',
	action: '<?php echo plugin_dir_url( __FILE__ ); ?>uploadfile.php',
	onSubmit: function(file, ext)
	{
		var file_extension = file.search('.php');
		var file_extension_js = file.search('.js');
		if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext)) || file_extension != -1 || file_extension_js != -1){
		 // extension is not allowed 
		status.text('Only JPG, PNG or GIF files are allowed');
		return false;
		}status.text('<?php _e('Uploading...',DOMAIN); ?>');
		},
	
	onComplete: function(file, response)
	{
		/*Image size validation*/
		 if(response == 'LIMIT'){
			status.text('<?php _e('Your image size must be less then',DOMAIN); echo " ".$templatic_image_size." "; _e('kilobytes',DOMAIN); ?>');
			return false;
		 }
		
		// Start Limit Code
		if(response > 10 )
		  {
			status.text('<?php _e('You can upload maximum 10 images',DOMAIN); ?>');
			return false;
		  }
		 
		 var counter = 0;
		 $uc('#imagelist div').each(function(){
				counter = counter + 1;
		 });
		limit = (response.split(",").length + counter) - 1;
		if(parseFloat(limit) > 10)
	   	  {
			status.text('<?php _e('You can upload maximum 10 images',DOMAIN); ?>');
			return false;
	   	  }
		// End Limit Code
		
		var spl = response.split(",");
		//On completion clear the status
		status.text('');
		//Add uploaded file to list
		var counter = 0;
		$uc('#files .success').each(function(){
				counter = parseFloat(this.id) + 1;
		});
		for(var i =0;i<spl.length;i++)
		  {
			if((spl.length-1) == i)
			  {
			  }
			else
			  {
				  	
				var img_name = '<?php echo bloginfo('template_url')."/images/tmp/"; ?>'+spl[i];
				var id_name=spl[i].split("."); 
				
				$uc('<div id=i_'+spl[i]+' class='+id_name[0]+'>').appendTo('#imagelist').html('<p id="'+id_name[0]+'"><img src="'+img_name+'" name="'+spl[i]+'" height="120" width="120"/><span><img align="right" alt="delete" src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/cross.png" class="redcross" onClick="delete_image(\''+id_name[0]+'\',\''+spl[i]+'\');" /></span></p>');
			  }
		  }
		
		var counter = 0;
		$uc('#files .success').each(function(){
				counter = counter + 1;
			});
		var cnt = 0;
		$uc('#files .success').each(function(){
			cnt = cnt + 1;
			if(this.id)
			 {
				if(cnt == 1)
				 {
					if(counter > 1)
					  {
						$uc('#left'+this.id).hide();
						$uc('#right'+this.id).show();
					  }
					else
					  {
						$uc('#left'+this.id).hide();
						$uc('#right'+this.id).hide();
					  }
				 }
				 
				 if(counter == cnt)
				   {
					   $uc('#right'+this.id).hide();
				   }
				 else
				   {
					   $uc('#right'+this.id).show();
				   }
			 }
		 });
		
		var imgArr = new Array();
		var i = 0;
		$uc('#files #imagelist p img').each(function(){
			imgArr[i] = this.name;
			i++;
		});
		document.getElementById('imgarr').value = imgArr;
	
	}});
});
jQuery.noConflict();
jQuery(document).ready(function($){
	jQuery("#imagelist").sortable({
		 connectWith: '#deleteArea',
		 'start': function (event, ui) {
			   //jQuery Ui-Sortable Overlay Offset Fix
			   if ($.browser.webkit) {
				  wscrolltop = $(window).scrollTop();
			   }
		 },
		 'sort': function (event, ui) {
			   //jQuery Ui-Sortable Overlay Offset Fix
			   if ($.browser.webkit) {
				  ui.helper.css({ 'top': ui.position.top + wscrolltop + 'px' });
			   }
		 },
		 update: function(event, ui){
			 //Run this code whenever an item is dragged and dropped out of this list
			 var order = $(this).sortable('serialize');						
			 jQuery.ajax({
				 url: '<?php echo plugin_dir_url( __FILE__ ); ?>processImage.php',
				 type: 'POST',
				 data: order,				 
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
<div id="uploadimage" class="upload button secondary_btn" ><span><?php _e("Upload Image", DOMAIN); ?></span></div><span id="status" class="message_error2 clearfix"></span>
<table width="70%" align="center" border="0" class="img_table">
<tr>
    <td>
       <?php if(isset($_REQUEST['pid'])){			
			 $thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');		
			  if($thumb_img_arr):
                foreach ($thumb_img_arr as $val) :
					 $tmpimg = explode("/",$val['file']);
					 $name = end($tmpimg);
					 if($name!="")
						 $image_name.=$name.",";
				endforeach;	   
			  endif;
	   }
	   
	   if(isset($_SESSION["file_info"][0]) && $_SESSION["file_info"][0] != '' &&  $_SESSION["file_info"] != '' && $_REQUEST['backandedit'] != "" && !$_REQUEST['pid'] )
        {
            foreach($_SESSION["file_info"] as $image_id=>$val)
            {
				if($val !='')
				$tmp =explode("/",$val);
				$name = end($tmp);
				if($val!="")
					$image_name.=$name.",";				
			}
		}
	   ?>
        <input name="imgarr" id="imgarr" value="<?php echo @$image_name;?>" type="hidden"/>
        <table>
        	<tr>
            <td id="files">
            	<div id="imagelist">
                
               
        <?php
        $i = 0;
		if(isset($_SESSION["file_info"][0]) && $_SESSION["file_info"][0] != '' &&  $_SESSION["file_info"] != '' && $_REQUEST['backandedit'] != "" && !$_REQUEST['pid'] )
        {
            foreach($_SESSION["file_info"] as $image_id=>$val)
            {
				
                $thumb_src = get_template_directory_uri().'/images/tmp/'.$val;		
				if($val !='')
					$tmpd = explode("/",$val);
					$name = end($tmpd);
					$img_name= explode('.',$name);
				if(!file_exists($thumb_src) && $val!=""):
           ?>
                    <div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $thumb_src; ?>" height = "120px" width = "120px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<img align="right" id="cross" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','');" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/cross.png" alt="delete" class="redcross" />
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
            $thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');				
            $i = 0;
            if($thumb_img_arr):
                foreach ($thumb_img_arr as $val) :
				$tmpimg = explode("/",$val['file']);
                $name = end($tmpimg );
				$img_name= explode('.',$name);
               //$thumb_src = get_template_directory_uri().'/thumb.php?src='.$val['file'];			  
			   if($name!=""):
           ?>                    
                    <div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                    	<p id='<?php echo $img_name[0]?>'>
                        <img src="<?php echo $val['file']; ?>" height = "120px" width = "120px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<img align="right" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','<?php echo $val['id']; ?>');" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/cross.png" alt="delete" class="redcross" />
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
                        <img src="<?php echo $src; ?>" height = "120px" width = "120px" name="<?php echo $name; ?>" alt="" />                       
                        <span>
                        	<img align="right" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','');" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/cross.png" alt="delete" class="redcross" />
                        </span>   
                         </p>               
                    </div>
                      
                   <?php else: ?>
                   <?php
                  		$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');
                        foreach($thumb_img_arr as $value):
                            $name = end(explode("/",$value['file']));
							$img_name= explode('.',$name);							
                            if($name == $val):	
							
                   ?>
                   		<div id="i_<?php  echo $name; ?>" class="<?php echo $img_name[0]?>">
                            <p id='<?php echo $img_name[0]?>'>
                            <img src="<?php echo $value['file']; ?>" height = "120px" width = "120px" name="<?php echo $name; ?>" alt="" />                       
                            <span>
                                <img align="right" id="cross<?php echo $i; ?>" onclick="delete_image('<?php echo $img_name[0]; ?>','<?php echo $name; ?>','<?php echo $value['id']; ?>');" src="<?php echo plugin_dir_url( __FILE__ ); ?>images/cross.png" alt="delete" class="redcross" />
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