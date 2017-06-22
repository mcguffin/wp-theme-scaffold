/**
 *	Lazyload
 *	========
 *	Version 1.0.0
 * 
 *
 *	Usage:
 *	<html>
 *	<img id="the-image"
 *		src="blank.gif"
 *		data-src="thing.png"
 *		data-srcset="thing-1920.png 1920w, thing-1024.png 1024w"
 *	/>
 *	</html>
 *	<script>
 *		// load image immediately
 *		$('img').lazyload( -1 );
 *
 *		// add image to loading queue with high priority
 *		$('img').lazyload( 0 );
 *
 *		// add image to loading queue with custom priority
 *		$('img').lazyload( function( $image ) { return $image.offset().top } );
 *	</script>
 */

(function($) {

	var queue = [];
	
	function priosort( getPrioCB ) {
		queue.sort( function(a,b){
			return getPrioCB( a ) - getPrioCB( b );
		});
	}

	function load( $image ) {
		if ( ! $image ) {
			return;
		}
		var e = $.Event( 'lazyload' );
		
		$image.trigger( e );

		if ( e.isDefaultPrevented() ) {
			return;
		}

		if ( Modernizr.srcset && $image.is('[data-srcset]') ) {
			$image.attr('srcset', $image.attr('data-srcset') );
		} else if ( $image.is('[data-src]') ) {
			$image.attr('src', $image.attr('data-src') );
		}
	}

	$.fn.extend({
		lazyload: function( priority ) {

			var prio = priority;

			if ( 'undefined' === typeof priority ) {
				priority = function() {
					return 0;
				}
			};

			if ( 'function' !== typeof priority ) {
				priority = function( $image ) {
					return prio;
				}
			}

			if ( ! this.is('img') || this.data( 'loaded' ) ) {
				return this;
			}

			this.each( function() {
				var $self = $(this),
					imgPrio = priority( $self );

				// load
				$self.one('load', function() {

					$self.data( 'loaded', true );

					var e = $.Event( 'lazyloaded' );
					$self.trigger( e );
					if ( e.isDefaultPrevented() ) {
						return;
					}

					$self.hide().fadeIn( 666, function() {
						$(this).removeAttr('style');
					});
					priosort( priority );
					
					!!queue.length && load( queue.shift() );
				});

				if ( imgPrio === -1 ) {
					load( $self );
				} else {
					queue.push( $self );
				}
			});

			priosort( priority );
			!!queue.length && load( queue.shift() );
			return this;
		}
		
	});

})(jQuery);