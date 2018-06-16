/**
 *	Viewport Events
 *	===============
 *	Version 1.1.0
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
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
 *		$('.thing').viewportEvents( 60, '');
 *
 *
 *		// triggered when '.thing' enters viewport
 *		$('.thing').on('viewport:in:enter',function(e) {
 *			console.log( e.oldState );
 *			console.log( $(this).data('viewport-state') );
 *		});
 *	</script>
 *
 *	Changelog:
 *	----------
 *	1.1.0 - Add Prefix param
 *	      - Trigger global 'viewport'
 *	1.0.0 initial
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
		try {
			elHeight = $el[0].getBoundingClientRect().height;
		} catch(err) {
			elHeight = $el.height(); // always INT
		}
		elBottom = Math.floor( elTop + elHeight );

		elTop = Math.floor( elTop );
		elHeight = Math.floor( elHeight );
		elBottom = Math.floor( elBottom );

		// early return
		if ( elBottom <= viewportTop ) {
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
		viewportEvents: function( customOffset, prefix ) {
			var self = this;
			var customOffset = customOffset || 0;
			var prefix = prefix || 'viewport';

			init();

			function triggerViewportEvents(){

				self.each( function(i,el) {
					var $el = $(el),
						substate = getViewportState( $el, customOffset ),
						prevSubstate = $el.data( prefix + '-substate'),
						state = substate.substr( 0, substate.indexOf(':') );
						prevState = $el.data( prefix + '-state'),
						stateChange = state != prevState,
						substateChange = substate != prevSubstate;

					$el.data( prefix + '-state', state );
					$el.data( prefix + '-substate', substate );

					if ( ! stateChange && ! substateChange ) {
						return;
					}

					$el.trigger( new $.Event({
						type		: prefix,
						oldState	: prevState,
					}) );
					if ( stateChange ) {
						$el.trigger( new $.Event({
							type		: prefix + ':' + state,
							oldState	: prevState,
						}) );
					}
					if ( substateChange ) {
						$el.trigger( new $.Event({
							type		: prefix + ':' + substate,
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
