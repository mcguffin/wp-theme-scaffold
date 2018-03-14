/**
 *	Scroll-Snapping
 *	===============
 *	Version 1.0.0
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
 *
 *
 *	Keep objects scrolled to top of viewport
 *
 *	Usage:
 *	======
 *
 *	<script>
 *		// will snap #element to the top of the screen when scrolled
 *		// Snap radius is 1 / 8 screen height
 *		$('#element').snap();
 *
 *		// snap with radius 60 px.
 *		$('#element').snap( 60 );
 *
 *
 *		// revert snapping
 *		$('#element').unsnap();
 *	</script>
 */
(function($){
	var $win = $(window),
		$snap = null,
		listen = true,
		snapTimeout = false;

	$.fn.extend({
		snap: function( dist ) {
			$('[data-snap]').removeAttr( 'data-snap' );
			$snap = this.first().attr( 'data-snap', Math.abs( dist || $win.height() * 0.125 ) );
			return this;
		},
		unsnap: function(  ) {
			$snap.removeAttr('data-snap');
			$snap = null;
			return this;
		},
	});

	$win.on('scroll',function(){
		var winScroll,
			snapDist,
			snapOffs, snapRadius;

		if ( ! $snap || ! listen ) {
			return;
		}

		snapDist = parseInt( $snap.attr('data-snap') );
		snapOffs = $snap.offset().top;
		winScroll = $win.scrollTop();

		snapTimeout && clearTimeout( snapTimeout );

		if ( Math.abs( winScroll - snapOffs ) < snapDist ) {
			snapTimeout = setTimeout(function(){
				listen = false;
				$snap.scrollHere( snapOffs, function() {
					listen = true;
					snapTimeout = false;
				}, 250 );
			}, 250 );
		}
	});
})(jQuery);
