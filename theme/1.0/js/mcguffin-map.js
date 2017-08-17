/**
 * @version 1.0.0
 */
(function($){
	var maps = {},
		flyouts = {},
		markerImage = {
			url: mcguffin_map.markerImage.inactive,
			size: new google.maps.Size(101, 87),
			origin: new google.maps.Point(0, 0),
			anchor: new google.maps.Point(43, 87)					
		},
		clickMarker = function() {
			var i = 0,
				mapId = $( this.map.getDiv() ).attr('id'),
				markers = maps[mapId].markers,
				len = markers.length,
				active, newImage;

			for ( i; i<len; i++ ) {

				newImage = $.extend({},markerImage);

				active = this === maps[mapId].markers[i].marker;

				newImage.url = active ? mcguffin_map.markerImage.active : mcguffin_map.markerImage.inactive;

				markers[i].$flyout.toggleClass( 'hidden', ! active );

				markers[i].marker.setIcon( newImage );
			}
		};

	$(document).ready(function(){
		$('[data-map="true"]').each(function( i, el ) {
			var map,
				lng = parseFloat( $( el ).attr('data-map-lng') ),
				lat = parseFloat( $( el ).attr('data-map-lat') ),
				zoom = parseInt( $( el ).attr('data-map-zoom') );

			map = new google.maps.Map( el, {
				center: {
					lat: lat,
					lng: lng,
				},
				zoom: zoom,
				scrollwheel: false
			});
			map.setOptions( { styles: mcguffin_map.styles } );

			maps[ $(el).attr('id') ] = {
				map:map,
				markers:[]
			};

		});
		
		
		$('[data-map-marker="true"]').each(function( i, el ) {
			var marker,
				mapId = $( el ).attr('data-map-id'),
				lng = parseFloat( $( el ).attr('data-map-lng') ),
				lat = parseFloat( $( el ).attr('data-map-lat') );
		
			if ( 'undefined' === typeof maps[mapId] ) {
				console.log( 'map '+mapId+' not found!' );
				return;
			}

			marker = new google.maps.Marker({
				clickable: true,
				position: {
					lat: lat, 
					lng: lng
				},
				map: maps[mapId].map,
				icon: markerImage
			});
			marker.addListener('click', clickMarker );

			maps[mapId].markers.push( {
				marker:marker,
				$flyout: $( el )
			} );
			
		});

		// click first marker
		$.each( maps, function(mapId,mapObj) {
			if ( mapObj.markers.length ) {
				clickMarker.apply( mapObj.markers[0].marker );
			}
		});

	});

})(jQuery);
