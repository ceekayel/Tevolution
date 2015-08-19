/*
*	TypeWatch 2.2
*
*	Examples/Docs: github.com/dennyferra/TypeWatch
*	
*  Copyright(c) 2013 
*	Denny Ferrassoli - dennyferra.com
*   Charles Christolini
*  
*  Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
(function(jQuery) {
	jQuery.fn.typeWatch = function(o) {
		// The default input types that are supported
		var _supportedInputTypes =
			['TEXT', 'TEXTAREA', 'PASSWORD', 'TEL', 'SEARCH', 'URL', 'EMAIL', 'DATETIME', 'DATE', 'MONTH', 'WEEK', 'TIME', 'DATETIME-LOCAL', 'NUMBER', 'RANGE'];
		// Options
		var options = jQuery.extend({
			wait: 750,
			callback: function() { },
			highlight: true,
			captureLength: 2,
			inputTypes: _supportedInputTypes
		}, o);
		function checkElement(timer, override) {
			var value = jQuery(timer.el).val();
			// Fire if text >= options.captureLength AND text != saved text OR if override AND text >= options.captureLength
			if ((value.length >= options.captureLength && value.toUpperCase() != timer.text)
				|| (override && value.length >= options.captureLength))
			{
				timer.text = value.toUpperCase();
				timer.cb.call(timer.el, value);
			}
		};
		function watchElement(elem) {
			var elementType = elem.type.toUpperCase();
			if (jQuery.inArray(elementType, options.inputTypes) >= 0) {
				// Allocate timer element
				var timer = {
					timer: null,
					text: jQuery(elem).val().toUpperCase(),
					cb: options.callback,
					el: elem,
					wait: options.wait
				};
				// Set focus action (highlight)
				if (options.highlight) {
					jQuery(elem).focus(
						function() {
							this.select();
						});
				}
				// Key watcher / clear and reset the timer
				var startWatch = function(evt) {
					var timerWait = timer.wait;
					var overrideBool = false;
					var evtElementType = this.type.toUpperCase();
					// If enter key is pressed and not a TEXTAREA and matched inputTypes
					if (typeof evt.keyCode != 'undefined' && evt.keyCode == 13 && evtElementType != 'TEXTAREA' && jQuery.inArray(evtElementType, options.inputTypes) >= 0) {
						timerWait = 1;
						overrideBool = true;
					}
					var timerCallbackFx = function() {
						checkElement(timer, overrideBool)
					}
					// Clear timer					
					clearTimeout(timer.timer);
					timer.timer = setTimeout(timerCallbackFx, timerWait);
				};
				jQuery(elem).on('keydown paste cut input', startWatch);
			}
		};
		// Watch Each Element
		return this.each(function() {
			watchElement(this);
		});
	};
})(jQuery);
// JavaScript Document
jQuery(document).ready(function() {		
	searchInput = jQuery('#search_string');
	searchInput.typeWatch({
		callback: function() {
			jQuery("#ajaxform").submit();
		},
		wait: 500,
		highlight: false,
		captureLength: 0
	});	
});
/*Search Google Map */
function new_googlemap_ajaxSearch(){
	var search_string = document.getElementById('search_string').value;
	var post_type='';
	var categoryname='';
	ClustererMarkers=[];	
	m_counter=0;
	all_googlemap_deleteMarkers();
	jQuery("input[name='posttype[]']").each(function() {		
		if (jQuery(this).attr('checked'))
		{		
			post_type+=jQuery(this).val()+',';
			
		}
	});
	
	jQuery("input[name='categoryname[]']").each(function() {
		if (jQuery(this).attr('checked'))
		{		
			categoryname+=jQuery(this).val()+',';
		}
	});	
	document.getElementById('map_loading_div').style.display = 'block';
		jQuery.ajax({
			url:ajaxUrl,
			type:'POST',			
			data:'action=taxonomies_googlemap_initialize&posttype='+post_type+'&categoryname='+categoryname+'&search_string=' + search_string,
			success:function(results) {				
				//jQuery('#adv_city').html(results);
				document.getElementById('map_loading_div').style.display = 'none';
				all_googlemap(results);
				
			}
		});	
}

var taxo_googlemap = null;

/*Google map widget initialize */
function taxo_googlemap_initialize(map){
	var post_type='';
	var categoryname='';
	var classname;	
	ClustererMarkers=[];	
	m_counter=0;
	var checkbox_id=jQuery(map).attr('id');
	document.getElementById('map_loading_div').style.display = 'block';		
	all_googlemap_deleteMarkers();	
	
	jQuery("input[name='posttype[]']").each(function() {// post type loop
            classname=jQuery(this).attr('id');
            //categoryname='';
            id_name=jQuery(this).attr('data-category');
            if(checkbox_id==classname){
                    jQuery('.'+classname).find(':checkbox').attr('checked', jQuery('#'+classname).is(":checked"));
            }

            post_type+=jQuery(this).val()+',';
            
            jQuery("div#"+id_name+" input[name='categoryname[]']").each(function() {// post type category loop
                    if (jQuery(this).attr('checked'))
                    {
                            categoryname+=jQuery(this).val()+',';				// finish the ajax
                            jQuery(this).parent().parent().parent().find('.mw_cat_title .'+classname).attr('checked','true'); /* for post type checkbox */

                    }

            });// finish post type category loop

        });// finish post type loop	
        
        /* list of post type and category send */        
        
        taxo_googlemap =jQuery.ajax({
            url:ajaxUrl,
            type:'POST',
            data:'action=taxonomies_googlemap_initialize&posttype='+post_type+'&categoryname='+categoryname,
            beforeSend : function(){
                    if(taxo_googlemap != null){
                                    taxo_googlemap.abort();
                    }
            },
            success:function(results){
                    document.getElementById('map_loading_div').style.display = 'none';
                    all_googlemap(results);
            }
        });
	
	if(categoryname==''){
		document.getElementById('map_loading_div').style.display = 'none';
	}
}

// read the data, create markers
function all_googlemap(data) {	
	// get the bounds of the map
	bounds = new google.maps.LatLngBounds();  
	// clear old markers  	
     var search_string = document.getElementById('search_string');
	jsonData = jQuery.parseJSON(data);
  	// create the info window
	infowindow = new google.maps.InfoWindow();
  	// if no markers found, display map_marker_nofound div with no search criteria met message	
  	 if (jsonData[0].totalcount <= 0  && search_string!='') {
		 
		var mapcenter = new google.maps.LatLng(map_latitude,map_longitude);
		if(navigator.appName =='Microsoft Internet Explorer'){
			all_googlemap_listMapMarkers1(jsonData);  
		}else{
			all_googlemap_listMapMarkers1(jsonData); 
		}		
  	}else{

		var mapcenter = new google.maps.LatLng(map_latitude,map_longitude);		
		all_googlemap_listMapMarkers1(jsonData);
		if(search_string.value!="" ){
			map.fitBounds(bounds);
			var center = bounds.getCenter();
			map.setCenter(center);
		}
	}
	
	if(zoom_option==1){
		map.fitBounds(bounds);
		var center = bounds.getCenter();
		map.setCenter(center);
	}else{
		map.setCenter(mapcenter);
		map.setZoom(map_zomming_fact);
	}
}
/*Delete the existing google map markers */
function all_googlemap_deleteMarkers() {	
	 if (markerArray && markerArray.length > 0) {		
		for (i in markerArray) {
			if (!isNaN(i)){				
				//alert(i);				
				markerArray[i].setMap(null);
				infoBubble.close();
				//markers[i].setMap(null); 				
				
			}
		}
		markerArray.length = 0;
	  }	
	if(mClusterer != null)
	{
		mClusterer.clearMarkers();
		infoBubble.close();
	}
}
/*listMapMarkers function for display the markers on google map */
function all_googlemap_listMapMarkers1(input) {
	markers=input;
	var search_string = document.getElementById('search_string');
	var totalcount = input[0].totalcount;	
	//New infobundle			
	 infoBubble = new InfoBubble({
		maxWidth:210,minWidth:210,minHeight:"auto",padding:0,borderRadius:0,borderWidth:0,borderColor:"none",overflow:"visible",backgroundColor:"#fff"
	  });			
	//finish new infobundle
	if(totalcount > 0){
		for (var i = 0; i < input.length; i++) {							 
			var details = input[i];				
			var image = new google.maps.MarkerImage(details.icons,new google.maps.Size(PIN_POINT_ICON_WIDTH, PIN_POINT_ICON_HEIGHT));
			var coord = new google.maps.LatLng(details.location[0], details.location[1]);			
			markers[i]  = new google.maps.Marker({
							position: coord,
							title: details.name,
							content: details.message,
							visible: true,
							clickable: true,
							map: map,
							icon: details.icons
						});
			
			bounds.extend(coord);
			markerArray[m_counter] = markers[i];
		     markers[i]['infowindow'] = new google.maps.InfoWindow({
				content: details.message,
				maxWidth: 210,
				minWidth: 210,
			});	
			
			//Start
			mgr.addMarkers( markers[i], 0 );			
			
			if(search_string.value!="" && input.length==1){
				 infoBubble.setContent( details.message );
				 infoBubble.open(map, markers[i]);
				 all_marker_attachMessage(markers[i], details.message);
			}else{
				all_marker_attachMessage(markers[i], details.message);
			}
			m_counter++;
		}	
		ClustererMarkers = markers.concat(ClustererMarkers);
		if(clustering != 1){
			mClusterer = new MarkerClusterer(map, ClustererMarkers,{maxZoom: 0,gridSize: 10,batchSizeIE: 100,styles: null,infoOnClick: 1,infoOnClickZoom: 18,});
						
			google.maps.event.addListener(mClusterer, 'clusterclick', function(cluster) {
					/* Convert lat/long from cluster object to a usable MVCObject */
					var info = new google.maps.MVCObject;
					info.set('position', cluster.center_);
					/*Get markers*/
					var markers = cluster.getMarkers();
					var content = "";
					/*Get all the titles*/
					for(var i = 0; i < markers.length; i++) {
						content += markers[i].content + "\n";
					}
					//google.maps.event.addListener(map, 'zoom_changed', function() {
					if(map.getMapTypeId() == 'terrain'){
						var map_zoom = 15;
					}else if(map.getMapTypeId() == 'satellite'){
						var map_zoom = 19;
					}else{
						var map_zoom = 21;
					}	
						if(map.getZoom()==map_zoom){
							infowindow.close();
							infowindow.setContent( content );
							infowindow.open(map, info);
						}
					//});
			});
		}
	}  
}
// but that message is not within the marker's instance data 
function all_marker_attachMessage(marker, msg) {
	var myEventListener = google.maps.event.addListener(marker, 'click', function() {
		infoBubble.setContent( msg );
		infoBubble.open(map, marker);		
	});
}
/* Custom post type taxonomy open hide*/
function custom_post_type_taxonomy(id,str){
	
	var div1 = document.getElementById(id);
	var toggal = document.getElementById(str.id);
	
	if (div1.style.display == 'none') {
		div1.style.display = 'block';
		jQuery('#'+str.id).removeClass('toggleoff');
		toggal.setAttribute('class','toggleon toggle_post_type');
	} else {
		div1.style.display = 'none';
		jQuery('#'+str.id).removeClass('toggleon');
		toggal.setAttribute('class','toggleoff toggle_post_type');
	}
}
