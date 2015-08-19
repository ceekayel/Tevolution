/*
 * grid list view option
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
jQuery(function() {
	var cc = jQuery.cookie('display_view');	
	if (cc == 'grid') {
		jQuery('#loop_listing').addClass('grid');
		jQuery('#loop_listing').removeClass('list');
		jQuery('#loop_taxonomy').addClass('grid');
		jQuery('#loop_taxonomy').removeClass('list');
		jQuery('#loop_archive').addClass('grid');
		jQuery("#gridview").addClass("active");	
		jQuery("#listview").removeClass("active");	
	} else {
		jQuery('#loop_listing').removeClass('grid');	
		jQuery('#loop_taxonomy').removeClass('grid');
		jQuery('#loop_archive').removeClass('grid');
		jQuery('#loop_listing').addClass('list');	
		jQuery('#loop_taxonomy').addClass('list');
		jQuery('#loop_archive').addClass('list');
		jQuery("#listview").addClass("active");	
		jQuery("#gridview").removeClass("active");	
	}
});
jQuery(document).ready(function() {
	jQuery("blockquote").before('<span class="before_quote"></span>').after('<span class="after_quote"></span>');
	jQuery('.viewsbox a.listview').click(function(e){	
		e.preventDefault();	
		jQuery('#loop_listing').addClass('list');
		jQuery('#loop_taxonomy').addClass('list');				
		jQuery('#loop_archive').addClass('list');
		jQuery('#loop_listing').removeClass('grid');
		jQuery('#loop_taxonomy').removeClass('grid');				
		jQuery('#loop_archive').removeClass('grid');				
		jQuery('.viewsbox a').attr('class','');
		jQuery(this).attr('class','active');
		jQuery('.viewsbox a.gridview').attr('class','');
		jQuery.cookie("display_view", "list");
	});
	jQuery('.viewsbox a.gridview').click(function(e){	
		e.preventDefault();
		jQuery('#loop_listing').addClass('grid');
		jQuery('#loop_listing').removeClass('list');
		jQuery('#loop_taxonomy').addClass('grid');
		jQuery('#loop_taxonomy').removeClass('list');
		jQuery('#loop_archive').addClass('grid');		
		jQuery('#loop_archive').removeClass('list');
		jQuery('.viewsbox a').attr('class','');
		jQuery(this).attr('class','active');
		jQuery('.viewsbox .listview a').attr('class','');
		jQuery.cookie("display_view", "grid");
	});
});
