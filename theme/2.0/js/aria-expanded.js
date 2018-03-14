/**
 *	Aria Expand
 *	===========
 *	Version 1.0.0
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
 *
 *	Usage (1):
 *	==========
 *
 *	<html>
 *	<button aria-controls="the-nav">Toggle Nav</button>
 *	<nav id="the-nav" aria-expanded="false">
 *		...
 *	</nav>
 *	</html>
 *
 *	<style>
 *	#the-nav[aria-expanded="false"] {
 *		height:0;
 *	}
 *	</style>
 *
 *
 *	Usage (2):
 *	==========
 *
 *	<script>
 *
 *	// FUNCTIONS
 *
 *	// expand
 *	$('#the-nav').ariaSetState(true);
 *
 *	// collapse
 *	$('#the-nav').ariaSetState(false);
 *
 *	// toggle
 *	$('#the-nav').ariaToggleState(false);
 *
 *	// EVENTS
 *
 *	// before expanding
 *	$('#the-nav').on('aria-expand', function(e){
 *		// prevent expansion
 *		e.preventDefault();
 *	} );
 *
 *	// after expanded
 *	$('#the-nav').on('aria-expanded', callback );
 *
 *	// before collapsing
 *	$('#the-nav').on('aria-collapse', function(e){
 *		// prevent collapsing
 *		e.preventDefault();
 *	} );
 *
 *	// after collapse
 *	$('#the-nav').on('aria-collapsed', callback );
 *	</script>
 */
(function($){

	$.fn.extend({
		ariaSetState: function( newState ) {
			var state, e;
			if ( this.is('[aria-expanded]') ) {
				state = this.attr( 'aria-expanded' ) == 'true';
				if ( state != newState ) {
					e = $.Event(newState ? 'aria-expand' : 'aria-collapse');
					e.bubbles = false;
					this.trigger( e );
					if ( ! e.isDefaultPrevented() ) {
						this.attr( 'aria-expanded', newState.toString() );
						this.trigger( newState ? 'aria-expanded' : 'aria-collapsed' );
					}
				}
			}
			return this;
		},

		ariaToggleState: function() {
			if ( this.is('[aria-expanded]') ) {
				var state = this.attr( 'aria-expanded' ) == 'true';
				this.ariaSetState( ! state );
			}
			return this;
		}
	});


	$(document)
		.on('click','[aria-controls]',function(e){
			// toggle expand on click
			var target_id = $(this).attr('aria-controls');
			$('#'+target_id).ariaToggleState();
			e.stopPropagation();
			e.preventDefault();
		})
		.on( 'click', function(e) {
			if ( ! $(e.target).closest('[aria-expanded="true"]').length ) {
				$('[aria-expanded="true"]').not('[data-aria-keep-open="true"]').ariaSetState( false );
			}
		} )
		.on('keyup', function( event ) {
			// close expanded elements on escape
			switch ( event.keyCode ) {
				case 27: // ESCAPE Key
					$('[aria-expanded="true"]').ariaSetState( false );
					break;
			}
		});

})(jQuery);
