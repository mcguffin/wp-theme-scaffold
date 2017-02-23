(function($){

	$.fn.extend({
		ariaSetState: function( newState ) {
			var state;
			if ( this.is('[aria-expanded]') ) {
				state = this.attr( 'aria-expanded' ) == 'true';
				if ( state != newState ) {
					this.attr( 'aria-expanded', newState.toString() );
					this.trigger( newState ? 'aria-expand' : 'aria-collapse' );
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
				$('[aria-expanded="true"]').ariaSetState( false );
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