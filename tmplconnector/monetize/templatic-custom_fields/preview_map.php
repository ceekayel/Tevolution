<?php
/*
 * show map on success page.
 */
if(!function_exists('preview_address_google_map'))
{
    function preview_address_google_map_plugin($latitute,$longitute,$address,$map_type='Road Map')
    {		    
	 if($map_type=='Street map') { 
	 	$street_map = $map_type; 
	 }
	if($map_type=='Satellite Map') { $map_type = SATELLITE; } elseif($map_type=='Terrain Map') { $map_type = TERRAIN; }  else { $map_type = ROADMAP; }
	 
	 
	$term_icon = apply_filters('tmpl_default_map_icon',get_bloginfo('template_directory').'/library/map/icons/pin.png');
	
	wp_print_scripts( 'google-maps-apiscript' );
	wp_print_scripts( 'google-clusterig' );
	
	$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
    ?>   
    <script type="text/javascript">
	<?php if(!is_single())
		{?>
		var script = '<script type="text/javascript" src="http://google-maps-' +
          'utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble';
      if (document.location.search.indexOf('compiled') !== -1) {
        script += '-compiled';
      }
      script += '.js"><' + '/script>';
      document.write(script);
	  <?php } ?>
	/* <![CDATA[ */
	var infoBubble;
	var map ;
	function initialize() {	
		
		var geocoder = null;
		
		var lat = <?php echo $latitute;?>;
		var lng = <?php echo $longitute;?>;
		var latLng = new google.maps.LatLng(<?php echo $latitute;?>, <?php echo $longitute;?>);
		//var isDraggable = jQuery(document).width() > 480 ? true : false;
		var myOptions = {
			zoom: 8,
			center: latLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		var myLatLng = new google.maps.LatLng(<?php echo $latitute;?>, <?php echo $longitute;?>);
		var marker = new google.maps.Marker({
			map: map,
			position: myLatLng,
			icon: '<?php echo apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>',
			draggable: true
		});

		 var contentString = '<?php echo $address;?>';
		
		infoBubble = new InfoBubble({
		  content: contentString,
		  maxWidth:210,minWidth:210,minHeight:"auto",padding:0,borderRadius:0,borderWidth:0,borderColor:"none",overflow:"visible",backgroundColor:"#fff",
        });
		
		google.maps.event.addListener(marker, 'click', function() {
			infoBubble.open(map,marker);
		});
		<?php if(!is_single())
		{?>
			infoBubble.open(map, marker);	
	<?php } ?>
	}
	google.maps.event.addDomListener(window, 'load', initialize);
			/*End*/
		<?php if($street_map=='Street map'):?>
			 panorama = new google.maps.StreetViewPanorama(document.getElementById('map_canvas'));	
			var sv = new google.maps.StreetViewService();	
			sv.getPanoramaByLocation(latLng, 50, processSVData);
		<?php endif;?>	
			
	
	
	
	function processSVData(data, status) {		
		if (status == google.maps.StreetViewStatus.OK) {
			var marker = new google.maps.Marker({
				position: data.location.latLng,
				map: map,
				title: data.location.description
			});
			
			panorama.setPano(data.location.pano);
				panorama.setPov({
				heading: 270,
				pitch: 0
			});
			panorama.setVisible(true);
			
			google.maps.event.addListener(marker, 'click', function() {
				var markerPanoID = data.location.pano;
				/* Set the Pano to use the passed panoID*/
				panorama.setPano(markerPanoID);
					panorama.setPov({
					heading: 270,
					pitch: 0
				});
				panorama.setVisible(true);
			});
		} else {
			alert('Street View data not found for this location.');
		}
	}
	
	/* ]]> */
    </script>
    <div class="map" id="map_canvas" style="width:100%; height:500px;" ></div>
    <?php
    }
}
?>