/**
 *	Aria Expand
 *	===========
 *	Version 2.0.0
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
 *
 *	Changelog:
 *	----------
 *	v2.0.0	Use reference: http://heydonworks.com/practical_aria_examples/
 *
 *
 *	Usage (1):
 *	==========
 *
 *	<html>
 *	<button aria-controls="the-nav" aria-expanded="false">Toggle Nav</button>
 *	<nav id="the-nav" aria-hidden="true">
 *		...
 *	</nav>
 *	</html>
 *
 *	<style>
 *	#the-nav[aria-hiddden="true"] {
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
 *	// # EVENTS
 *
 *	// ### Button:
 *
 *	// before expanding
 *	$('#the-nav').on('aria-expand', function(e){
 *		// don't show
 *		e.preventDefault();
 *	} );
 *
 *	// after showing
 *	$('#the-nav').on('aria-expanded', callback );
 *
 *	// before hide
 *	$('#the-nav').on('aria-collapse', function(e){
 *		// prevent hiding
 *		e.preventDefault();
 *	} );
 *
 *	// after hide
 *	$('#the-nav').on('aria-collapseed', callback );
 *
 *	// ### Controlled Element:
 *
 *	// before expanding
 *	$('#the-nav').on('aria-show', function(e){
 *		// don't show
 *		e.preventDefault();
 *	} );
 *
 *	// after showing
 *	$('#the-nav').on('aria-showed', callback );
 *
 *	// before hide
 *	$('#the-nav').on('aria-hide', function(e){
 *		// prevent hiding
 *		e.preventDefault();
 *	} );
 *
 *	// after hide
 *	$('#the-nav').on('aria-hidden', callback );
 *	</script>
 */
(function($){

	$.fn.extend({
		ariaSetState: function( newState ) {
			var state, te, ee, be,
				$btn, $el;
			if ( this.is('[aria-controls]') ) {
				$btn = this;
				$el = $('#'+this.attr('aria-controls'));
			} else {
				$el = this;
				$btn = $('[aria-controls="' + this.attr('id') + '"]');
			}

			state = $el.attr( 'aria-hidden' ) !== 'true';

			if ( state != newState ) {
				ee = $.Event( newState ? 'aria-show' : 'aria-hide' );
				ee.bubbles = false;
				be = $.Event( newState ? 'aria-expand' : 'aria-collapse' );
				be.bubbles = false;
				$el.trigger( ee );
				$btn.trigger( be );

				if ( be.isDefaultPrevented() || ee.isDefaultPrevented() ) {
					return;
				}

				$el.attr('aria-hidden', (!newState).toString() );
				$btn.attr('aria-expanded', newState.toString() );

				$el.trigger( newState ? 'aria-showed' : 'aria-hidden' );
				$btn.trigger( newState ? 'aria-expanded' : 'aria-collapsed' );
			}

			return this;
		},

		ariaToggleState: function() {
			var state;
			if ( this.is('[aria-expanded]') ) { // btn
				state = this.attr( 'aria-expanded' ) === 'true';
			} else if ( this.is('[aria-hidden]')) {
				state = this.attr( 'aria-hidden' ) !== 'true';
			} else {
				return;
			}
			this.ariaSetState( ! state );
			return this;
		}
	});


	$(document)
		.on('click','[aria-controls]',function(e){
			// toggle expand on click
			$(this).ariaToggleState();
			// var target_id = $(this).attr('aria-controls');
			// $('#'+target_id).ariaToggleState();
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
