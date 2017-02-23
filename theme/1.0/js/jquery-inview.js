(function($) {
	var all = [],
		$window = $(window);
	
	$(window).on('scroll',function(){
		$.each( all, function(i,$el) {
			if ( $el.isScrolledIntoView() ) {
				$el.trigger( 'inview' );
			}
		});
	});

	function isScrolledIntoView( $el ) {
	}

	$.fn.extend({
		inView: function(  ) {
			this.isScrolledIntoView = function( fac ) {
				if ( 'undefined' === typeof fac) {
					fac = 0;
				}
				var docViewBottom = $window.scrollTop() + $window.height() * ( 1 - fac ),
					elTop = this.offset().top;
				return elTop <= docViewBottom;
			}
			all.push( this );
			return this;
		}
	});

})(jQuery);
