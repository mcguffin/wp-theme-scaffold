/**
 *	Objectfit fallback
 *
 *	(c) 2018 Jörn Lund
 *	https://github.com/mcguffin
 */
(function($){
	var blankSrc = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

	if ( ! Modernizr.objectfit ) {
		$('.background-image-container > .background-wrap > img').each(function () {
			var $img	= $(this),
				imgUrl	= $img.prop('src'),
				fit		= false, attachment = false,
				css		= {
					'background-position'	: 'center center',
					'background-repeat'		: 'no-repeat',
				};

			if ( $img.closest( '.background-size-cover' ).length ) {
				fit = 'cover';
			} else if ( $img.closest( '.background-size-contain' ).length ) {
				fit = 'contain';
			}
			if ( $img.closest( '.background-attachment-fixed' ).length ) {
				attachment	= 'fixed';
			} else if ( $img.closest( '.background-attachment-fixed' ).length ) {
				attachment	= 'scroll';
			}

			if (!!fit && imgUrl) {
				$img.prop('src',blankSrc).removeAttr('srcset');
				$img.closest('.container-wrap').addClass( 'objectfit-polyfill' );

				css['background-image']		= 'url(' + imgUrl + ')';
				css['background-size']		= fit;
				if ( !!attachment ) {
					css['background-attachment']	= attachment;
				}

				$img.css( css );
			}
		});
	}

})(jQuery);
