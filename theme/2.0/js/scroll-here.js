/**
 *	Smooth Anchor Scrolling
 *	=======================
 *	Version 1.0.0
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
 *
 *	Usage:
 *	======
 *
 *	<html>
 *		<!-- Links with href="#..." will smooth-scroll automatically ... -->
 *		<a href="#element">Goto Element</a>
 *
 *		<!-- ... except Links with role="button" href="#..." -->
 *		<a href="#" role="button">Add to Shopping Cart</a>
 *		...
 *		<div id="element">
 *			...
 *		</div>
 *	</html>
 *	<script>
 *		// FUNCTIONS
 *
 *		// scroll to #element in 333 ms
 *		$('#element').scrollHere( 333 );
 *
 *		// scroll to #element in no time
 *		$('#element').scrollHereNow();
 *
 *		// EVENTS
 *
 *		// before start scrolling
 *		$('#element').on('scroll-here',function(e){
 *			// prevent scrolling
 *			e.preventDefault();
 *		});
 *
 *		// after finished scrolling
 *		$('#element').on('scrolled-here',function(e){
 *			// do something
 *		});
 *	</script>
 */
(function($){
	var $root = $('html, body');

	$.fn.extend({
		scrollHere: function( duration ) {
			var self = this,
				offset = this.offset().top + 1,
				e = $.Event('scroll-here'),
				callback = function() {
					var e = $.Event('scrolled-here');
					self.trigger( e );
				};
			this.trigger( e );

			if ( e.isDefaultPrevented() ) {
				return this;
			}

			duration = duration || 500

			if ( duration > 0 ) {
				$root.stop().animate({
						scrollTop: offset
					},
					duration,
					callback
				);
			} else {
				$root.stop().scrollTop( offset );
				callback();
			}
			return this;
		},
		scrollHereNow: function() {
			return this.scrollHere( 0 );
		}
	});
	//*
	$(document).on('click', "a[href^='#']:not('[role=\"button\"]')", function(e) {
	/*/
	$("a[href^='#']:not('[role=\"button\"]')").on('click', function(e) {
	//*/

		var $this = $(this), hash = this.hash, offs,
			update_hash = ! parseInt($this.data('discrete')),
			callback = function(){
				update_hash && (window.location.hash = hash);
			};

		if ( hash !== '' ) {

			e.preventDefault();

			this.blur();

			$this.closest('[aria-expanded]').ariaSetState(false);

			offstop = $this.data('scroll-position');

			if ( 'undefined' === typeof(offstop) ) {
				$(hash).scrollHere( undefined, callback );
			} else {
				$(document).scrollHere( parseInt(offstop), callback );
			}
			return false;
		}
	});
})(jQuery);
