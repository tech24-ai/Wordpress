(function($) { "use strict";

	/* Register user */

	function wpqa_unlogged_payment() {
		return new Promise(resolve => {
			const $form = jQuery(".register-payment-unlogged form");
			jQuery.ajax({
				type: "POST",
				url: wpqa_payment.admin_url,
				data: $form.serialize(),
				cache: false,
				dataType: "json",
				success: function (data) {
					if (data.error) {
						jQuery(".wpqa_error",$form).html('<span class="required-error required-error-c">'+data.error+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery("html,body").animate({scrollTop: jQuery(".wpqa_error").offset().top-35},"slow");
						resolve("error");
					}
					jQuery('.checkout-unlogged .load_span').hide();
					jQuery('.checkout-unlogged .process_area .button,.checkout-unlogged .button-default.pay-button').show();
					if (data.success == 1) {
						if (jQuery(".wpqa-stripe-payment").length) {
							const $paymentForm = jQuery('.activate-payment-tab form:first');
							$paymentForm.find(".name").prop("value",data.name);
							$paymentForm.find(".email").prop("value",data.email);
						}
						jQuery(".register-payment-unlogged").slideUp();
						resolve("resolve");
					}
				},error: function (jqXHR, textStatus, errorThrown) {
					// Error
				},complete: function () {
					// Done
				}
			});
		});
	}

	if (jQuery(".checkout-unlogged").length) {
		jQuery(".register-payment-unlogged #first_name_payment,.register-payment-unlogged #last_name_payment,.register-payment-unlogged #email_payment").keypress(function(event){
			const keycode = (event.keyCode?event.keyCode:event.which);
			if (keycode == "13") {
				jQuery(".checkout-unlogged .activate-payment-tab .process_area .button,.checkout-unlogged .activate-payment-tab .button-default.pay-button").click();
			}
		});
		
		jQuery(document).on("click",".checkout-unlogged .process_area .button,.checkout-unlogged .button-default.pay-button",async (e) => {
			const checkout_unlogged = jQuery(e.currentTarget);
			e.preventDefault();
			checkout_unlogged.find('.load_span').show();
			checkout_unlogged.find('.process_area .button,.button-default.pay-button').hide();
			if (!jQuery(".payment-stripe.payment-style-activate").length) {
				const wpqa_result = await wpqa_unlogged_payment();
				if (wpqa_result == "error") {
					return;
				}
			}
			const $paymentForm = jQuery('.activate-payment-tab form:first');
			$paymentForm.submit();
			return false;
		});
	}
	
	/* Payments */

	if (jQuery(".payment-methods a").length) {
		jQuery(".payment-tabs").on("click","a",function () {
			const payment = jQuery(this);
			const payment_hide = payment.attr("href");
			const payment_button = payment.parent().parent().find("a");
			const payment_wrap = payment.closest(".payment-wrap");
			if (payment_wrap.hasClass("payment-wrap-2")) {
				payment_button.removeClass("payment-style-activate");
				payment.addClass("payment-style-activate");
			}else {
				payment_button.addClass("button-default-2").addClass("btn__primary").removeClass("button-default-3").removeClass("btn__info");
				payment.addClass("button-default-3").addClass("btn__info").removeClass("button-default-2").removeClass("btn__primary");
			}
			payment_wrap.find(".payment-method").removeClass("activate-payment-tab").hide(10);
			payment_wrap.find(".payment-method[data-hide="+payment_hide+"]").addClass("activate-payment-tab").slideDown(300);
			setTimeout(function () {
				jQuery(window).trigger('sticky_recalc');
			},1000);
			return false;
		});
	}

	/* Stripe */

	if (jQuery(".wpqa-stripe-payment").length && wpqa_payment.publishable_key != "") {
		const stripe = Stripe(wpqa_payment.publishable_key);

		function isInViewport(the_element) {
			const $window = jQuery(window);
			const viewPortTop = $window.scrollTop();
			const viewPortBottom = viewPortTop + $window.height();
			const elementTop = the_element.offset().top;
			const elementBottom = elementTop + the_element.outerHeight();
			return ((elementBottom <= viewPortBottom) && (elementTop >= viewPortTop));
		}

		let wpqa_stripe_paypemt = () => {
			const $cards = jQuery('.wpqa-stripe-payment');

			if ($cards.length === 0) {
				return;
			}

			$cards.each(function () {
				const $form = jQuery(this).parents('form:first');
				const formId = $form.data('id');
				const elements = stripe.elements();
				const cardStyle = {
					hidePostalCode: true,
					classes: {
						base: 'wpqa-stripe-payment',
						empty: 'wpqa-stripe-payment-empty',
						focus: 'wpqa-stripe-payment-focus',
						complete: 'wpqa-stripe-payment-complete',
						invalid: 'wpqa-stripe-payment-error'
					},
					style: {
						base: {
							color: '#7c7f85',
							fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Oxygen-Sans", Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
							fontSmoothing: 'antialiased',
							fontSize: '15px',
							'::placeholder': {
								color: '#7F8393'
							}
						},
						invalid: {
							color: '#7c7f85',
							iconColor: '#CC3434'
						}
					}
				};
				if (wpqa_payment.stripe_style_input == "3_inputs") {
					var cardElement = elements.create('cardNumber', cardStyle);
					const cardExpiry = elements.create('cardExpiry', cardStyle);
					const cardCvc = elements.create('cardCvc', cardStyle);
					cardCvc.mount('div.wpqa-stripe-payment-cvc[data-id="'+formId+'"]');
					cardExpiry.mount('div.wpqa-stripe-payment-expiry[data-id="'+formId+'"]');
				}else {
					var cardElement = elements.create('card', cardStyle);
				}
				cardElement.mount('div.wpqa-stripe-payment[data-id="'+formId+'"]');
				cardElement.addEventListener('change', (event) => {
					const $form = jQuery(this).parents('form:first');
					if (event.error) {
						jQuery('.wpqa_error', $form).text(event.error.message).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
					}
				});

				$form.on("submit",async (e) => {
					e.preventDefault();
					jQuery('.load_span',$form).show();
					jQuery('button[type="submit"]',$form).prop('disabled', true).hide();
					if (jQuery(".register-payment-unlogged").length) {
						const wpqa_result = await wpqa_unlogged_payment();
						if (wpqa_result == "error") {
							return;
						}
					}
					jQuery('input[name="payment-method-id"]', $form).remove();
					jQuery('input[name="payment-intent-id"]', $form).remove();
					jQuery('input[name="payment-invoice-id"]', $form).remove();
					jQuery('input[name="payment-subscription-id"]', $form).remove();
					jQuery('input[name="payment-old-subscr-id"]', $form).remove();
					const payment_data = {};
					const card_name_input = jQuery('input[name="name"]', $form);
					if (card_name_input.length > 0) {
						const card_name = card_name_input.val();
						if (card_name != null && card_name != '') {
							payment_data.billing_details = {
								name: card_name
							};
						}
					}
					stripe.createPaymentMethod('card',cardElement,payment_data).then(function (payment_result) {
						if (payment_result.error) {
							jQuery('.load_span',$form).hide();
							jQuery('button[type="submit"]',$form).prop('disabled', false).show();
							jQuery('.wpqa_error', $form).text(payment_result.error.message).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
							const the_element = jQuery('.wpqa-stripe-payment', $form);
							if (the_element && the_element.offset() && the_element.offset().top) {
								if (!isInViewport(the_element)) {
									jQuery('html, body').animate({scrollTop: the_element.offset().top - 100},1000);
								}
							}
							if (the_element) {
								the_element.fadeIn(500).fadeOut(500).fadeIn(500);
							}
						}else {
							if (typeof(payment_result) !== 'undefined' && payment_result.hasOwnProperty('paymentMethod') && payment_result.paymentMethod.hasOwnProperty('id')) {
								jQuery('<input>').attr({type: 'hidden',name: 'payment-method-id',value: payment_result.paymentMethod.id}).appendTo($form);
							}
							submit_ajax($form, cardElement);
						}
					});
					return false;
				});
			});
		}

		function submit_ajax($form, card) {
			jQuery.ajax({
				type: "POST",
				url: wpqa_payment.admin_url,
				data: $form.serialize(),
				cache: false,
				dataType: "json",
				success: function (data) {
					if (data.error) {
						jQuery('.wpqa_error', $form).text(data.error).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery('.load_span',$form).hide();
						jQuery('button[type="submit"]',$form).prop('disabled', false).show();
					}else if (data.success) {
						const formId = $form.data('id');
						if (card != null) {
							card.clear();
						}
						jQuery('input[name="payment-method-id"]', $form).remove();
						jQuery('input[name="payment-intent-id"]', $form).remove();
						jQuery('input[name="payment-invoice-id"]', $form).remove();
						jQuery('input[name="payment-subscription-id"]', $form).remove();
						jQuery('input[name="payment-old-subscr-id"]', $form).remove();
						if (data.redirect) {
							setTimeout(function () {
								window.location = data.redirect;
							}, 1500);
						}
					}else if (typeof(data) !== 'undefined' && data.hasOwnProperty('confirm_card') && data.confirm_card == 1) {
						confirm_card_payment($form, card, data);
					}else if (typeof(data) !== 'undefined' && data.hasOwnProperty('resubmit_again') && data.resubmit_again == 1) {
						jQuery('.load_span',$form).show();
						jQuery('button[type="submit"]',$form).prop('disabled', true).hide();
						submit_ajax($form, card);
					}
				},error: function (jqXHR, textStatus, errorThrown) {
					// Error
				},complete: function () {
					// Done
				}
			});
		}

		function confirm_card_payment($form, card, data) {
			stripe.confirmCardPayment(data.client_secret).then(function (result) {
				if (data.hasOwnProperty('trial_end') && data.trial_end == true && result.hasOwnProperty('error') && result.error.hasOwnProperty('code') && result.error.code == "payment_intent_authentication_failure") {
					jQuery.ajax({
						type: "POST",
						url: wpqa_payment.admin_url,
						data: {action : 'wpqa_delete_trial_subscription_ajax',subscription : data.subscription},
						cache: false,
						dataType: "json",
						success: function (successData) {
							// Success
						},error: function (jqXHR, textStatus, errorThrown) {
							// Error
						},complete: function () {
							// Done
						}
					});
				}
				if (result.error) {
					jQuery('.wpqa_error', $form).text((result.hasOwnProperty('error') && result.error.hasOwnProperty('message')?result.error.message:result.error)).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
					jQuery('.load_span',$form).hide();
					jQuery('button[type="submit"]',$form).prop('disabled', false).show();
				}else {
					jQuery('input[name="payment-intent-id"]', $form).remove();
					jQuery('input[name="payment-invoice-id"]', $form).remove();
					jQuery('input[name="payment-subscription-id"]', $form).remove();
					jQuery('input[name="payment-old-subscr-id"]', $form).remove();
					if (typeof(result) !== 'undefined' && result.hasOwnProperty('paymentIntent') && result.paymentIntent.hasOwnProperty('id')) {
						jQuery('<input>').attr({type: 'hidden',name: 'payment-intent-id',value: result.paymentIntent.id}).appendTo($form);
					}
					if (typeof(data) !== 'undefined' && data.hasOwnProperty('invoice')) {
						jQuery('<input>').attr({type: 'hidden',name: 'payment-invoice-id',value: data.invoice}).appendTo($form);
					}
					if (typeof(data) !== 'undefined' && data.hasOwnProperty('subscription')) {
						jQuery('<input>').attr({type: 'hidden',name: 'payment-subscription-id',value: data.subscription}).appendTo($form);
					}
					if (typeof(data) !== 'undefined' && data.hasOwnProperty('old_subscr_id')) {
						jQuery('<input>').attr({type: 'hidden',name: 'payment-old-subscr-id',value: data.old_subscr_id}).appendTo($form);
					}
					jQuery('.load_span',$form).show();
					jQuery('button[type="submit"]',$form).prop('disabled', true).hide();
					submit_ajax($form, card);
				}
			});
		}

		wpqa_stripe_paypemt();
	}
	
})(jQuery);