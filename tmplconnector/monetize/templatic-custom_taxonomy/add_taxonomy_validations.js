/* this function is created to validate the required fields of Add new taxonomy form */
function check_taxonomy_form()
{
	jQuery.noConflict();
	var post_name = jQuery('#post_name').val();
	var post_slug = jQuery('#post_slug').val();
	var taxonomy_name = jQuery('#taxonomy_name').val();
	var taxonomy_slug = jQuery('#taxonomy_slug').val();
	var tag_name = jQuery('#tag_name').val();
	var tag_slug = jQuery('#tag_slug').val();
	if( post_name == "" || post_slug == "" || taxonomy_name == "" || taxonomy_slug == "" || tag_name == "" || tag_slug == "" )
	{
		if(post_name == "")
			jQuery('#post_title').addClass('form-invalid');
		jQuery('#post_title').change(on_change_post_title);
		if(post_slug == "")
			jQuery('#post_title_slug').addClass('form-invalid');
		jQuery('#post_title_slug').change(on_change_title_slug);
		if(taxonomy_name == "")
			jQuery('#tax_name').addClass('form-invalid');
		jQuery('#tax_name').change(on_change_tax_name);
		if(taxonomy_slug == "")
			jQuery('#tax_slug').addClass('form-invalid');
		jQuery('#tax_slug').change(on_change_tax_slug);
		if(tag_name == "")
			jQuery('#tag_title').addClass('form-invalid');
		jQuery('#tag_title').change(on_change_tag_title);
		if(tag_slug == "")
			jQuery('#tag_slug_name').addClass('form-invalid');
		jQuery('#tag_slug_name').change(on_change_tag_slug_name);
		return false;
	}
	function on_change_post_title()
	{
		var post_name = jQuery('#post_name').val();
		if(post_name=="")
		{
			jQuery('#post_title').addClass('form-invalid');
			return false;
		}
		else if(post_name!="")
		{
			jQuery('#post_title').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_title_slug()
	{
		var post_slug = jQuery('#post_slug').val();
		if(post_slug=="")
		{
			jQuery('#post_title_slug').addClass('form-invalid');
			return false;
		}
		else if(post_slug!="")
		{
			jQuery('#post_title_slug').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_tax_name()
	{
		var taxonomy_name = jQuery('#taxonomy_name').val();
		if(taxonomy_name=="")
		{
			jQuery('#tax_name').addClass('form-invalid');
			return false;
		}
		else if(taxonomy_name!="")
		{
			jQuery('#tax_name').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_tax_slug()
	{
		var taxonomy_slug = jQuery('#taxonomy_slug').val();
		if(taxonomy_slug=="")
		{
			jQuery('#tax_slug').addClass('form-invalid');
			return false;
		}
		else if(taxonomy_slug!="")
		{
			jQuery('#tax_slug').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_tag_title()
	{
		var tag_name = jQuery('#tag_name').val();
		if(tag_name=="")
		{
			jQuery('#tag_title').addClass('form-invalid');
			return false;
		}
		else if(tag_name!="")
		{
			jQuery('#tag_title').removeClass('form-invalid');
			return true;
		}
	}
	function on_change_tag_slug_name()
	{
		var tag_slug = jQuery('#tag_slug').val();
		if(tag_slug=="")
		{
			jQuery('#tag_slug_name').addClass('form-invalid');
			return false;
		}
		else if(tag_slug!="")
		{
			jQuery('#tag_slug_name').removeClass('form-invalid');
			return true;
		}
	}
}
function character_validation(e)
{
	var keyCode = (e.keyCode ? e.keyCode : e.which);
	if ((keyCode > 47 && keyCode < 58)  || keyCode == 32) {
        e.preventDefault();
    }
}
