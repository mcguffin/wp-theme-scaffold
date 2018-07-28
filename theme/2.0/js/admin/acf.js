(function($){

	if ( typeof acf !== 'undefined' ) {
		// BEGIN Sticky mce toolbar
		var current = false,
			offset = null,
			windowHeight = $(window).height();

		$(window).on('resize',function(){
			windowHeight = $(window).height()
		});

		function setFixed( $el, state ) {
			var $tb, tbh, twb;
			if ( state ) {
				$tb = $el.find('.mce-top-part');
				tbh = $tb.height();
				tbw = $tb.width();
				$tb.css({
					height:tbh,
				}).children().first().css({
					position:'fixed',
					top:$('#wpadminbar').height(),
					width:tbw,
				});
			} else {
				$el
					.find('.mce-top-part').children().first()
					.removeAttr('style');
			}
		}

		$(document)
			.on('viewport:in:cover viewport:in:leave','.mce-tinymce',function(e){
				var h = $(this).closest('.acf-input').height();
				if ( h > windowHeight && ! $(this).data( 'out' ) ) {
					setFixed( $(this), true );
				}
			})
			.on('viewport:in:contain','.mce-tinymce',function(e){
				setFixed( $(this), false );
			})
			.on('viewport:in:enter','.mce-tinymce',function(e){
				var h = $(this).closest('.acf-input').height();
				if ( h > windowHeight ) {
					setFixed( $(this), false );
				}
			})
			.on('viewport-out:in','.mce-tinymce',function(e){
				$(this).data( 'out', false );
				var h = $(this).closest('.acf-input').height();
				if ( h > windowHeight) {
					setFixed( $(this), true );
				}
			})
			.on('viewport-out:out:above','.mce-tinymce',function(e){
				$(this).data( 'out', true );
				var h = $(this).closest('.acf-input').height();

				if ( h > windowHeight) {
					setFixed( $(this), false );
				}
			});

		// enable autoresize
		acf.add_filter('wysiwyg_tinymce_settings',function(init, id, $field ) {
//			if ( $field.is('.-collapsed-target') ) {
				init.wp_autoresize_on = true;
				init.resize = false;
//			}
			return init;
		});

		acf.add_action( 'wysiwyg_tinymce_init', function( ed, id, mceInit, $field ) {
			if ( /* $field.is('.-collapsed-target') &&*/ $( ed.container ).parent().parent().is('.acf-editor-wrap') ) {
				$( ed.container ).viewportEvents( -$('#wpadminbar').height() );
				$( ed.container ).viewportEvents( -250, 'viewport-out' );
			}
		} );
		// END Sticky mce toolbar

		// BEGIN Collapsible editors
		if ( 'undefined' !== typeof acf.fields.repeater ) {

			acf.fields.repeater = acf.fields.repeater.extend({
				events:{
					'click .-collapsed .-collapsed-target .acf-editor-wrap': '_collapse',
				}
			});
			
		}
		// END Collapsible editors


	}
})(jQuery)
