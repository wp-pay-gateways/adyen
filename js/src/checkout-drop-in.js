/* global AdyenCheckout, pronamicPayAdyenCheckout */
( function () {
	'use strict';

	const checkout = new AdyenCheckout( pronamicPayAdyenCheckout.configuration );

	const validate_http_status = response => {
		if ( response.status >= 200 && response.status < 300 ) {
			return Promise.resolve( response );
		}

		return Promise.reject( new Error( response.statusText ) );
	};

	const get_json = response => response.json();

	if ( pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay ) {
		pronamicPayAdyenCheckout.paymentMethodsConfiguration.applepay.onValidateMerchant = ( resolve, reject, validationUrl ) => {
			send_request( pronamicPayAdyenCheckout.applePayMerchantValidationUrl, { validation_url: validationUrl } )
				.then( validate_http_status )
				.then( get_json )
				.then( response => {
					// Handle Pronamic Pay error.
					if ( response.error ) {
						return Promise.reject( new Error( response.error ) );
					}

					// Handle Adyen error.
					if ( response.statusMessage ) {
						return Promise.reject( new Error( response.statusMessage ) );
					}

					return resolve( response );
				} )
				.catch( error => {
					dropin.setStatus( 'error', { message: error.message } );

					setTimeout( () => {dropin.setStatus( 'ready' );}, 5000 );

					return reject();
				} );
		};
	}

	let pronamicPayAdyenProcessing = false;

	const dropin = checkout.create( 'dropin', {
		paymentMethodsConfiguration: pronamicPayAdyenCheckout.paymentMethodsConfiguration,
		onSubmit: ( state, dropin ) => {
			if ( pronamicPayAdyenProcessing ) {
				return false;
			}

			pronamicPayAdyenProcessing = true;

			send_request( pronamicPayAdyenCheckout.paymentsUrl, state.data )
			.then( validate_http_status )
			.then( get_json )
			.then( response => {
				pronamicPayAdyenProcessing = false;

				// Handle error.
				if ( response.error ) {
					return Promise.reject( new Error( response.error ) );
				}

				// Handle action object.
				if ( response.action ) {
					dropin.handleAction( response.action );
				}

				// Handle result code.
				if ( response.resultCode ) {
					paymentResult( response );
				}
			} )
			.catch( error => {
				dropin.setStatus( 'error', { message: error.message } );

				setTimeout( () => { dropin.setStatus( 'ready' ); }, 5000 );
			} );
		},
		onAdditionalDetails: ( state, dropin ) => {
			send_request( pronamicPayAdyenCheckout.paymentsDetailsUrl, state.data )
			.then( validate_http_status )
			.then( get_json )
			.then( response => {
				// Handle action object.
				if ( response.action ) {
					dropin.handleAction( response.action );
				}

				// Handle result code.
				if ( response.resultCode ) {
					paymentResult( response );
				}
			} )
			.catch( error => {
				throw Error( error );
			} );
		}
	} )
	.mount( '#pronamic-pay-checkout' );

	const send_request = ( url, data ) => {
		return fetch(
			url,
			{
				method: 'POST',
				cache: 'no-cache',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( data )
			}
		);
	};

	const paymentResult = ( response ) => {
		/*
		 * Handle payment result
		 *
		 * @link https://docs.adyen.com/checkout/drop-in-web#step-6-present-payment-result
		 */
		switch ( response.resultCode ) {
			case 'Authorised':
				// The payment was successful.
				dropin.setStatus( 'success', { message: pronamicPayAdyenCheckout.paymentAuthorised } );

				/*
				 * Inform the shopper that the payment was successful.
				 */
				window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;

				break;
			case 'Error':
				// Inform the shopper that there was an error processing their payment.

				/*
				 * You'll receive a `refusalReason` in the same response, indicating the cause of the error.
				 */
				if ( response.refusalReason ) {
					dropin.setStatus( 'error', { message: response.refusalReason } );
				}

				break;
			case 'Pending':
				// The shopper has completed the payment but the final result is not yet known.

				/*
				 * Inform the shopper that you've received their order, and are waiting for the payment to be completed.
				 */
				window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;

				break;
			case 'PresentToShopper':
				// Present the voucher or the QR code to the shopper.

				/*
				 * For a voucher payment method, inform the shopper that you are waiting for their payment. You will receive the final result of the payment in an AUTHORISATION notification.
				 *
				 * For a qrCode payment method, wait for the AUTHORISATION notification before presenting the payment result to the shopper.
				 *
				 * @todo
				 */
				break;
			case 'Refused':
				// The payment was refused.

				/*
				 * Inform the shopper that the payment was refused. Ask the shopper to try the payment again using a different payment method or card.
				 */
				dropin.setStatus( 'error', { message: pronamicPayAdyenCheckout.paymentRefused + ' (' + response.refusalReason + ')' } );

				setTimeout(
					() => {
						dropin.setStatus( 'ready' );
					},
					8000
				);

				break;
			case 'Received':
				// For some payment methods, it can take some time before the final status of the payment is known.
				dropin.setStatus( 'success', { message: pronamicPayAdyenCheckout.paymentReceived } );

				/*
				 * Inform the shopper that you've received their order, and are waiting for the payment to clear.
				 */
				setTimeout(
					() => {
						window.location.href = pronamicPayAdyenCheckout.paymentReturnUrl;
					},
					3000
				);

				break;
		}
	};
} )();
