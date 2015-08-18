<?php
require("../../../../../../wp-load.php");
$id = $_REQUEST['package_id'];
$is_featured = get_post_meta($id,'is_featured',true);
$can_author_mederate = "";
$feature_amount_home = get_post_meta($id,'feature_amount',true);
$feature_cat_amount = get_post_meta($id,'feature_cat_amount',true);
$package_amount = get_post_meta($id,'package_amount',true);
$currency = get_option('currency_symbol');
$position = get_option('currency_pos');
if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
	$can_author_mederate = get_post_meta($id,'can_author_mederate',true);
}
if( $is_featured == 1 || $can_author_mederate == 1)
{ ?>
<label><?php echo FEATURED_TITLE; ?>?</label>
<div class="feature_label">
	<label>
		<input type="checkbox" name="featured_h" id="featured_h" value="<?php echo $feature_amount_home; ?>" onclick="featured_list(this.id)" <?php if($_SESSION['custom_fields']['featured_h'] != ""){ echo "checked=checked"; } ?> /><?php _e(FEATURED_HOME,DOMAIN); ?>
			<span id="ftrhome">
			<?php echo fetch_currency_with_position($feature_amount_home); ?>
			</span>
	</label>
	<label>
		<input type="checkbox" name="featured_c" id="featured_c" value="<?php echo $feature_cat_amount; ?>" onclick="featured_list(this.id)" <?php if($_SESSION['custom_fields']['featured_c'] != ""){ echo "checked=checked"; } ?> /><?php _e(FEATURED_CAT,DOMAIN); ?>
			<span id="ftrcat">
			<?php echo fetch_currency_with_position($feature_cat_amount); ?>
			</span>
	</label>
	<?php
		if(is_plugin_active('thoughtful-comments/fv-thoughtful-comments.php')){
			$author_can_moderate_comment = get_post_meta($id,'can_author_mederate',true);
		?>
			<label>
				<input type="checkbox" name="author_can_moderate_comment" id="author_can_moderate_comment" value="<?php echo $author_can_moderate_comment; ?>" onclick="featured_list(this.id)" <?php if($_SESSION['custom_fields']['author_can_moderate_comment'] != ""){ echo "checked=checked"; } ?> /><?php _e(MODERATE_COMMENT,DOMAIN); ?>
					<span id="ftrcomnt">
					<?php echo fetch_currency_with_position($author_can_moderate_comment); ?>
					</span>
			</label>
			<input type="hidden" name="author_moderate" id="author_moderate" value="0"/>
		<?php	
		}
	?>
	<input type="hidden" name="featured_type" id="featured_type" value="none"/>
	<span id='process' style='display:none;'><img src="<?php echo get_template_directory_uri()."/images/process.gif"; ?>" alt='Processing..' /></span> 
	<span class="message_note"><?php echo FEATURED_MSG_DESC;?>.</span>
	<span id="category_span" class="message_error2"></span>
</div>
<?php } ?>
<div class="form_row clearfix totalprice_asp">
<label><?php echo TOTAL_CHARGES_TEXT; ?></label>
<?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.' '; } ?>
<span id="pkg_price"><?php if(isset($package_amount) && $package_amount !=""){ echo $package_amount; } else{ echo "0";}?></span>
<?php if($position == '3'){ echo $currency; }else if($position != '1' && $position != '2' && $position !='3'){ echo ' '.$currency; } ?>
	+ 
<?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.' '; } ?>
<span id="feture_price"><?php if(@$fprice !=""){ echo fetch_currency_with_position($fprice) ; } else { echo "0"; }?></span>
<?php if($position == '3'){ echo $currency; }else if($position != '1' && $position != '2' && $position !='3'){ echo ' '.$currency; } ?>
	= 
<?php if($position == '1'){ echo $currency; }else if($position == '2'){ echo $currency.' '; } ?>
<span id="result_price"><?php if(@$total_price != ""){ echo fetch_currency_with_position($total_price); }  else { echo $package_amount; } ?></span>
<?php if($position == '3'){ echo $currency; }else if($position != '1' && $position != '2' && $position !='3'){ echo ' '.$currency; } ?>
<input type="hidden" name="feture_price" id="feture_price" value="<?php if(@$fprice 
== ""){ echo $fprice = $_SESSION['custom_fields']['featured_h'] + $_SESSION['custom_fields']['featured_c']; } else{ echo "0";} ?>"/>
<input type="hidden" name="total_price" id="total_price" value="<?php if(@$total_price != ""){ echo $total_price; } else{ echo $package_amount;} ?>"/>
</div>
