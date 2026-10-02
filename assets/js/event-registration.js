/* global gapsEventFrontend, GAPSStripeLoader */
( function () {
	'use strict';
	document.addEventListener( 'DOMContentLoaded', function () {
		var form = document.getElementById( 'gaps-event-registration-form' );
		if ( ! form || ! window.gapsEventFrontend ) return;
		var continueBtn = document.getElementById( 'gaps-event-continue' );
		var payBtn = document.getElementById( 'gaps-event-pay' );
		var paymentWrap = document.getElementById( 'gaps-event-payment-wrap' );
		var paymentElement = document.getElementById( 'gaps-event-payment-element' );
		var loading = document.getElementById( 'gaps-event-payment-loading' );
		var total = document.getElementById( 'gaps-event-total' );
		var secondWrap = document.getElementById( 'gaps-event-second-participant-wrap' );
		var secondInput = secondWrap ? secondWrap.querySelector( 'input' ) : null;
		var message = document.getElementById( 'gaps-event-form-message' );
		var stripe = null, stripePromise = null, elements = null, mounted = false, requestPromise = null, requestKey = '';

		function selectedType() { var el = form.querySelector( 'input[name="ticket_type"]:checked' ); return el ? el.value : 'single'; }
		function price() { return 'couple' === selectedType() ? gapsEventFrontend.couplePriceCents : gapsEventFrontend.singlePriceCents; }
		function formatEuro( cents ) { return ( cents / 100 ).toLocaleString( 'it-IT', { style: 'currency', currency: 'EUR' } ); }
		function update() {
			var couple = 'couple' === selectedType();
			if ( secondWrap ) secondWrap.hidden = ! couple;
			if ( secondInput ) secondInput.required = couple;
			if ( total ) total.textContent = formatEuro( price() );
			mounted = false; requestPromise = null; requestKey = '';
			if ( paymentElement ) paymentElement.innerHTML = '';
			if ( paymentWrap ) paymentWrap.hidden = true;
		}
		form.querySelectorAll( 'input[name="ticket_type"]' ).forEach( function ( el ) { el.addEventListener( 'change', update ); } );
		update();

		function setMessage( text, error ) { if ( message ) { message.textContent = text || ''; message.classList.toggle( 'is-error', Boolean( error ) ); } }
		function ensureStripe() {
			if ( stripe ) return Promise.resolve( stripe );
			if ( ! stripePromise ) {
				stripePromise = window.GAPSStripeLoader.getStripeInstance( gapsEventFrontend.stripePublishableKey ).then( function ( instance ) { stripe = instance; return stripe; } ).catch( function ( err ) { stripePromise = null; throw err; } );
			}
			return stripePromise;
		}
		function validate() {
			if ( ! form.reportValidity() ) return false;
			if ( 'couple' === selectedType() && secondInput && ! secondInput.value.trim() ) return false;
			return true;
		}
		function key() {
			return [ form.nome.value, form.cognome.value, form.email.value, form.telefono.value, selectedType(), secondInput ? secondInput.value : '' ].join( '|' );
		}
		function requestIntent() {
			var currentKey = key();
			if ( requestPromise && requestKey === currentKey ) return requestPromise;
			requestKey = currentKey;
			var fd = new FormData( form );
			fd.append( 'action', gapsEventFrontend.action );
			fd.append( 'nonce', gapsEventFrontend.nonce );
			requestPromise = fetch( gapsEventFrontend.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) { if ( data && data.success && data.data.client_secret ) return data.data.client_secret; throw new Error( data && data.data && data.data.message ? data.data.message : gapsEventFrontend.i18n.genericError ); } )
				.catch( function ( err ) { requestPromise = null; requestKey = ''; throw err; } );
			return requestPromise;
		}

		continueBtn.addEventListener( 'click', function () {
			setMessage( '', false );
			if ( ! validate() ) { setMessage( gapsEventFrontend.i18n.validationError, true ); return; }
			continueBtn.disabled = true;
			paymentWrap.hidden = false;
			loading.hidden = false;
			Promise.all( [ ensureStripe(), requestIntent() ] ).then( function ( results ) {
				if ( mounted ) return;
				var clientSecret = results[1];
				elements = results[0].elements( { clientSecret: clientSecret } );
				var pe = elements.create( 'payment' );
				pe.mount( paymentElement );
				mounted = true; loading.hidden = true; payBtn.disabled = false;
			} ).catch( function ( err ) {
				loading.hidden = true; continueBtn.disabled = false; setMessage( err.message || gapsEventFrontend.i18n.genericError, true );
			} );
		} );

		payBtn.addEventListener( 'click', function () {
			if ( ! stripe || ! elements || payBtn.disabled ) return;
			payBtn.disabled = true; setMessage( gapsEventFrontend.i18n.paying, false );
			stripe.confirmPayment( { elements: elements, confirmParams: { return_url: gapsEventFrontend.thankYouUrl } } ).then( function ( result ) {
				if ( result.error ) { setMessage( result.error.message, true ); payBtn.disabled = false; }
			} );
		} );
	} );
}() );
