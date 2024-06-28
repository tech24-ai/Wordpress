(function($) { "use strict";
	
	/* Add categories */

	if (jQuery(".add_categories_left_menu").length) {
		jQuery(".add_categories_left_menu").on("click",function () {
			var add_item = jQuery(this);
			var item_id = jQuery(this).data("id");
			var item_name = jQuery(this).data("name");
			var select_val = add_item.parent().find("select").val();
			var select_text = add_item.parent().find("select option:selected").text();
			if (jQuery("#"+item_id+'_'+select_val).length) {
				jQuery("#"+item_id+'_'+select_val).addClass("removered").slideUp(function() {
					jQuery(this).slideDown().removeClass("removered");
				});
			}else {
				jQuery("#"+item_id).append('<li class="categories ui-sortable-handle" id="'+item_id+'_'+select_val+'"><label>'+select_text+'</label><input name="'+item_name+'[cat-'+select_val+'][value]" value="'+select_val+'" type="hidden"><div><div class="del-item-li remove-answer"><i class="icon-cancel"></i></div><div class="move-poll-li ui-icon darg-icon"><i class="icon-menu"></i></div></div></li>');
			}
			return false;
		});
	}

	/* Remove readonly */
	
	jQuery(window).on("load",function() {
		if (jQuery(".wpqa-readonly").length) {
			setTimeout(function() {
				jQuery(".wpqa-readonly input:not(.age-datepicker)").attr("readonly",false);
			},600);
		}
	});

	/* Cancel edit email */

	if (jQuery(".cancel-edit-email").length) {
		jQuery(document).on("click",".cancel-edit-email",function () {
			var edit_email = jQuery(this);
			var id = edit_email.data("id");
			var nonce = edit_email.data("nonce");
			jQuery.ajax({
				url: wpqa_edit.admin_url,
				type: "POST",
				data: { action : 'wpqa_cancel_edit_email',id : id,nonce : nonce },
				success:function(results) {
					jQuery(".alert-confirm-email").hide().remove();
				},
				error: function(errorThrown) {
					// Error
				}
			});
			return false;
		});
	}

	/* Change password */
	
	if (jQuery(".change-password-ajax").length) {
		jQuery(".change-password-ajax").on("submit",function() {
			var thisform = jQuery(this);
			var data = thisform.serialize();
			jQuery(".wpqa_error,.wpqa_success",thisform).remove();
			jQuery("input[type='submit']",thisform).hide();
			jQuery(".load_span",thisform).show().css({"display":"block"});
			var result = data.split("&");
			var data = "";
			var i;
			for (i = 0; i < result.length; i++) {
				if("action" == result[i].split("=")[0]) {
					data += "action" + "=wpqa_edit_profile_password&";
				}else {
					data += result[i] + "&";
				}
			}
			jQuery.ajax({
				url: wpqa_edit.admin_url,
				type: "POST",
				cache: false,
				dataType: "JSON",
				data: data,
				success:function(results) {
					if (results.error == 0) {
						jQuery("input[type='password']",thisform).val("");
					}
					var print_result = (results.success == 0?results.error:results.success);
					thisform.prepend(print_result).find(".wpqa_error,.wpqa_success").hide().css({"display":"none"}).animate({opacity: "show" , height: "show"}, 400).delay(5000).animate({opacity: "hide" , height: "hide"}, 400);
					jQuery(".load_span",thisform).hide().css({"display":"none"});
					jQuery("input[type='submit']",thisform).show();
				},
				error: function(errorThrown) {
					// Error
				}
			});
			return false;
		});
	}

	/* Financial payments */
	
	if (jQuery(".financial_payments_field").length) {
		jQuery("input[name='financial_payments']").on("change",function () {
			var financial_payments_c = jQuery(this);
			var financial_payments_c_val = financial_payments_c.val();
			jQuery(".financial_payments_forms").slideUp(10);
			jQuery("."+financial_payments_c_val+"_form").slideDown(300);
		});
	}

	/* Withdrawals */
	
	if (jQuery(".points_radio").length) {
		jQuery(".points_chooseCustom").on("click",function () {
			jQuery("input[name='choose_points'][value='custom']").prop("checked",true);
			jQuery("input[name='choose_points'][value='all']").removeAttr("checked");
		});
		jQuery(".points_chooseAll").on("click",function () {
			jQuery("input[name='choose_points'][value='all']").prop("checked",true);
			jQuery("input[name='choose_points'][value='custom']").removeAttr("checked");
		});
		jQuery("input[name='custom_points']").on("keyup",function() {
			var custom_points = jQuery(this);
			var custom_points_value = custom_points.val();
			var typingTimer;
			if (custom_points_value != "") {
				clearTimeout(typingTimer);
				typingTimer = setTimeout(function () {
					jQuery.ajax({
						url: wpqa_edit.admin_url,
						type: "POST",
						cache: false,
						dataType: "json",
						data: { action : 'wpqa_request_money',custom_points_value : custom_points_value },
						success:function(result) {
							if (result.success == 0) {
								jQuery(".points_chooseCustom,.points_radio_first").addClass("points_choose_error").removeClass("points_choose_activated");
								jQuery(".custom_points").css({"color":"#F00"});
								var last_error = wpqa_edit.not_min_points;
								if (result.error == "not_enough_points") {
									var last_error = wpqa_edit.not_enough_points;
								}else if (result.error == "not_enough_money") {
									var last_error = wpqa_edit.not_enough_money;
								}
								custom_points.closest(".withdrew_content").find(".wpqa_error").text(last_error).slideDown(200);
							}else {
								custom_points.closest(".withdrew_content").find(".wpqa_error").slideUp(200);
								jQuery(".points_chooseCustom,.points_radio_first").addClass("points_choose_activated").removeClass("points_choose_error");
								jQuery(".custom_points").css({"color":"#677075"});
								jQuery(".current_balance strong span").text(result.success);
							}
						}
					});
				},1000);
			}
		});
	}
	
})(jQuery);