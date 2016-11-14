/* globals wpem_vars */
window.WPEM = window.WPEM || {};

jQuery( document ).ready( function( $ ) {

	var start = new Date().getTime() / 1000;

	window.WPEM.Form = {};

	window.WPEM.Form.beforeSubmit = function( $form ) {

		var now = new Date().getTime() / 1000;

		$( '#wpem_step_took' ).val( parseFloat( now - start ).toFixed( 3 ) );

		$form.find( 'input, select' ).blur();

		$form.find( 'input[type="submit"]' ).prop( 'disabled', true ).addClass( 'disabled' );

		$( '#wpbody-content' ).block( {
			css: {
				position: 'fixed',
				width: '100%',
			},
			message: wpem_vars.i18n.loading_message,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: '0.9'
			}
		} );

		var $links = $( '.wpem-steps-list-item a');

		$links.css( 'cursor', 'default' );

		// Disable links to go back while page is loading
		$links.on( 'click', function( e ) {

			e.preventDefault();

		} );

	};

	$( '.wpem-screen form' ).on( 'click', '#wpem_no_thanks', function( e ) {

		e.preventDefault();

		if ( window.confirm( wpem_vars.i18n.exit_confirm ) ) {

			$( '#wpem_continue' ).val( 'no' );

			$( '.wpem-screen form' ).submit();

		}

	} );

	var validated = false;

	$( '.wpem-screen' ).on( 'submit', 'form', function( e ) {

		// Submit now if validated
		if ( validated ) {

			return true;

		}

		var $form = $( this );

		if ( ! $form[0].checkValidity() ) {

			return false;

		}

		e.preventDefault();

		window.WPEM.Form.beforeSubmit( $form );

		validated = true;

		if ( -1 === navigator.userAgent.indexOf( 'Safari' ) ) {

			$form.submit();

			return false;

		}

		// Workaround for Safari not repainting the DOM on submit
		setTimeout( function() {

			$form.submit();

		}, 250 );

	} );

} );
