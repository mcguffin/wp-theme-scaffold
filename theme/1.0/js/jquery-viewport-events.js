/**
 *	Viewport Events
 *	===============
 *	Version 1.0.0
 *
 *	Functions:
 *	
 *	`$el.viewportState( [customOffset] )`
 *		Get current viewport state
 *
 *	`$el.viewportEvents( [customOffset] )`
 *		Let element trigger event when viewport state changes
 *
 *	Event triggered:
 *	'viewport:<viewport-state>'
 *
 *	Viewport states:
 *	`in`			element or part of it is visible in viewport
 *	`in:enter`		enters viewport at the bottom
 *	`in:leave`		starts leaving viewport at the top
 *	`in:contain`	fully visible viewport
 *	`in:cover`		viewport is covered by element
 *	`out`			out of viewport
 *	`out:below`		below viewport
 *	`out:above`		above viewport
 *
 *	Usage:
 *	<script>
 *		// make '.thing' trigger viewport events
 *		$('.thing').viewportEvents();
 *
 *
 *		// triggered when '.thing' enters viewport
 *		$('.thing').on('viewport:in:enter',function(e) {
 *			console.log( e.oldState );
 *		});
 *	</script>
 *
 *
 *
 */

(function($) {
	var all = [],
		$window = $(window),
		viewportHeight,
		viewportTop,
		viewportBottom,
		inited = false;
	
	function setGeom() {
		viewportHeight	= $window.height();
		viewportTop		= $window.scrollTop();
		viewportBottom	= viewportTop + viewportHeight;
	}

	function getViewportState( $el, customOffset ) {
		customOffset = customOffset || 0;
		var elTop = $el.offset().top + customOffset,
			elHeight, elBottom;

		// early return
		if ( elTop > viewportBottom ) {
			return 'out:below';
		}

		elHeight = $el.height();
		elBottom = elTop + elHeight;

		// early return
		if ( elBottom < viewportTop ) {
			return 'out:above';
		}

		if ( elHeight > viewportHeight ) {
			if ( elTop < viewportBottom && elTop > viewportTop ) {
				return 'in:enter';
			}

			if ( elBottom < viewportBottom ) {
				return 'in:leave';
			}
			return 'in:cover';
		}

		if ( elBottom > viewportBottom ) {
			return 'in:enter';
		}
		if ( elTop < viewportTop ) {
			return 'in:leave';
		}
		return 'in:contain';
	}

	function init() {

		if ( inited ) {
			return;
		}

		inited = true;

		$(window).on('scroll', setGeom );
	}

	$.fn.extend({
		viewportEvents: function(customOffset) {
			var self = this;
			customOffset = customOffset || 0;

			init();

			function triggerViewportEvents(){

				self.each( function(i,el) {
					var $el = $(el),
						substate = getViewportState( $el, customOffset ),
						prevSubstate = $el.data('viewport-substate'),
						state = substate.substr( 0, substate.indexOf(':') );
						prevState = $el.data('viewport-state');

					$el.data('viewport-state', state );
					$el.data('viewport-substate', substate );

					if ( state != prevState ) {
						$el.trigger( new $.Event({
							type		: 'viewport:' + state,
							oldState	: prevState,
						}) );
					}
					if ( substate != prevSubstate ) {
						$el.trigger( new $.Event({
							type		: 'viewport:' + substate,
							oldState	: prevSubstate,
						}) );
					}
				});
			}
			$(window).on('scroll', triggerViewportEvents ).trigger('scroll');

			return this;
		},
		viewportState:function(customOffset) {
			customOffset = customOffset || 0;

			return getViewportState( this, customOffset );
		}
	});


})(jQuery);
