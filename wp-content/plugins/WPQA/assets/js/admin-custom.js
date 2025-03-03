﻿(function($) {
	"use strict";
	
	/* Delete post - question - comment - answer */
	jQuery(".delete-question-post,.delete-comment-answer").on("click",function() {
		var answer = confirm(option_js.confirm_delete);
		if (answer) {
			var this_event = jQuery(this);
			var data_id = this_event.attr("data-id");
			var data_action = this_event.attr("data-action");
			var data_location = this_event.attr("data-location");
			var data_div = this_event.attr("data-div-id");
			var data_nonce = this_event.attr("data-nonce");
			jQuery.post(option_js.ajax_a,"data_id="+data_id+"&data_div="+jQuery("#"+data_div).val()+"&action="+data_action+"&wpqa_delete_nonce="+data_nonce,function (data) {
				window.location = data_location;
			});
		}
		return false;
	});
	
	/* Delete attachment */
	jQuery(".delete-this-attachment").on("click",function () {
		var answer = confirm(option_js.confirm_delete_attachment);
		if (answer) {
	    	var delete_attachment = jQuery(this);
	    	var attachment_id = delete_attachment.attr("href");
	    	var post_id = jQuery("#post_ID").val();
	    	var single_attachment = "No";
	    	if (delete_attachment.hasClass("single-attachment")) {
	    		single_attachment = "Yes";
	    	}
	    	jQuery.post(option_js.ajax_a,"action=wpqa_confirm_delete_attachment&attachment_id="+attachment_id+"&post_id="+post_id+"&single_attachment="+single_attachment,function (result) {
	    		delete_attachment.parent().fadeOut(function() {
	    			jQuery(this).remove();
	    		});
	    	});
		}
		return false;
	});
	
	/* Send the custom mail */
	if (jQuery('.send-custom-mail').length) {
	    jQuery(".send-custom-mail").on("click",function() {
	    	jQuery("#loading").fadeIn("slow");
	    	jQuery(".send-custom-mail").css({"visibility":"hidden"});
			jQuery.ajax({
				url: option_js.ajax_a,
				type: "POST",
				data: { action : 'wpqa_send_custom_mail' },
				success:function(data) {
					setTimeout(function() {
	    				jQuery("#loading").fadeOut("slow");
	    				jQuery(".send-custom-mail").css({"visibility":"visible"});
	    			},200);
				}
			});
	    	return false;
	    });
	}
	
	/* Send the custom notification */
	if (jQuery('.send-custom-notification').length) {
	    jQuery(".send-custom-notification").on("click",function() {
	    	jQuery("#loading").fadeIn("slow");
	    	jQuery(".send-custom-notification").css({"visibility":"hidden"});
			jQuery.ajax({
				url: option_js.ajax_a,
				type: "POST",
				data: { action : 'wpqa_send_custom_notification' },
				success:function(data) {
					setTimeout(function() {
	    				jQuery("#loading").fadeOut("slow");
	    				jQuery(".send-custom-notification").css({"visibility":"visible"});
	    			},200);
				}
			});
	    	return false;
	    });
	}
	
	/* Send the custom message */
	if (jQuery('.send-custom-message').length) {
	    jQuery(".send-custom-message").on("click",function() {
	    	jQuery("#loading").fadeIn("slow");
	    	jQuery(".send-custom-message").css({"visibility":"hidden"});
			jQuery.ajax({
				url: option_js.ajax_a,
				type: "POST",
				data: { action : 'wpqa_send_custom_message' },
				success:function(data) {
					setTimeout(function() {
	    				jQuery("#loading").fadeOut("slow");
	    				jQuery(".send-custom-message").css({"visibility":"visible"});
	    			},200);
				}
			});
	    	return false;
	    });
	}
	
	/* Send the popup notification */
	if (jQuery('.send-popup-notification').length) {
	    jQuery(".send-popup-notification").on("click",function() {
	    	var popup = jQuery(this);
	    	var popup_id = popup.attr("data-post");
	    	jQuery("#loading").fadeIn("slow");
	    	popup.css({"visibility":"hidden"});
	    	var post_id = (popup_id !== undefined && popup_id !== false?popup_id:"");
			jQuery.ajax({
				url: option_js.ajax_a,
				type: "POST",
				data: { action : 'wpqa_send_popup_notification',post_id : post_id },
				success:function(data) {
					setTimeout(function() {
	    				jQuery("#loading").fadeOut("slow");
	    				popup.css({"visibility":"visible"});
	    			},200);
				}
			});
	    	return false;
	    });
	}
	
	/* Delete the history */
	if (jQuery('.delete-rows').length) {
		jQuery(".delete-rows").on("click",function () {
			var answer = confirm(option_js.confirm_delete_history);
			if (answer) {
		    	var delete_history = jQuery(this);
		    	var history_name = delete_history.data("history");
		    	var user_id = delete_history.data("user");
		    	delete_history.text(option_js.deleting);
		    	jQuery.post(option_js.ajax_a,"action=wpqa_confirm_delete_history&history_name="+history_name+"&user_id="+user_id,function (result) {
		    		delete_history.hide().closest(".section-info").fadeOut(function() {
		    			jQuery(this).remove();
		    		});
		    	});
			}
			return false;
		});
	}
	
	/* Fix the user counts */
	if (jQuery('.fixed-counts').length) {
		jQuery(".fixed-counts").on("click",function () {
	    	var fixed_counts = jQuery(this);
	    	var user_id = fixed_counts.data("user");
	    	fixed_counts.text(option_js.fixing);
	    	jQuery.post(option_js.ajax_a,"action=wpqa_confirm_fix_counts&user_id="+user_id,function (result) {
	    		fixed_counts.hide().closest(".section-info").fadeOut(function() {
	    			jQuery(this).remove();
	    		});
	    	});
			return false;
		});
	}



	/* Copy text */
	if (jQuery(".framework-input-copy").length) {
		jQuery(".framework-input-copy").on("focus",function() {
			var copyText = jQuery(this);
			copyText.select();
			document.execCommand("copy");
			return false;
		});

		jQuery(".framework-icon-copy").on("click",function() {
			var copyInput = jQuery(this);
			var copyText = copyInput.parent().find("input");
			copyText.select();
			document.execCommand("copy");
			return false;
		});
	}
	
	/* Fix the site counts */
	if (jQuery('.fix-site-counts').length) {
		jQuery(".fix-site-counts").on("click",function () {
	    	var fixed_counts = jQuery(this);
	    	fixed_counts.text(option_js.fixing);
	    	jQuery.post(option_js.ajax_a,"action=wpqa_confirm_fix_counts",function (result) {
	    		fixed_counts.hide().closest(".section-info").fadeOut(function() {
	    			jQuery(this).remove();
	    		});
	    	});
			return false;
		});
	}
	
	/* Fix the site counts */
	if (jQuery('.recreate-menus').length) {
		jQuery(".recreate-menus").on("click",function () {
	    	var recreate_menus = jQuery(this);
			if (confirm(option_js.confirm_creating)) {
				recreate_menus.text(option_js.creating);
				jQuery.post(option_js.ajax_a,"action=wpqa_recreate_menus",function (result) {
		    		recreate_menus.hide().closest(".section-info").fadeOut(function() {
		    			jQuery(this).remove();
		    		});
		    	});
			}
			return false;
		});
	}
	
	/* User actions */
	if (jQuery('.user-action-buttons').length) {
		jQuery(".user-action-buttons").on("click",function () {
			var user_actions = jQuery(this);
	    	var user_id = user_actions.data("user");
	    	var user_action = user_actions.data("action");
	    	var text_confirm_action = option_js.confirm_approving;
	    	if (user_action == "activate_user") {
	    		text_confirm_action = option_js.confirm_activating;
	    	}else if (user_action == "remove_subscription") {
	    		text_confirm_action = option_js.confirm_removing;
	    	}
			var confirm_action = confirm(text_confirm_action);
			if (confirm_action) {
		    	var text_action = option_js.approving;
		    	if (user_action == "activate_user") {
		    		text_action = option_js.activating;
		    	}else if (user_action == "remove_subscription") {
		    		text_action = option_js.removing;
		    	}
		    	user_actions.text(text_action);
		    	jQuery.post(option_js.ajax_a,"action=wpqa_user_actions&user_id="+user_id+"&actions="+user_action,function (result) {
		    		user_actions.hide().closest(".section-info").fadeOut(function() {
		    			jQuery(this).remove();
		    			location.reload();
		    		});
		    	});
			}
			return false;
		});
	}

	/* Comments, answers, reports, payments and user links */
	function wpqa_js_menus(link,type,id) {
		var v_link = link+'='+type;
		if (type != "") {
			jQuery('#'+id+' .wp-submenu-wrap li').each(function() {
				var $this = jQuery(this);
				$this.removeClass('current');
				$this.find('a').removeClass('current');
				if ($this.find('a').attr('href') == v_link) {
					$this.addClass('current');
					$this.find('a').addClass('current');
				}
			});
		}
	}
	wpqa_js_menus('edit-comments.php?comment_status',option_js.comment_status,"menu-comments");
	wpqa_js_menus('users.php?role',option_js.user_roles,"menu-users");
	wpqa_js_menus('edit.php?post_type=statement&statement',option_js.statement,"menu-posts-statement");
	wpqa_js_menus('edit.php?post_type=request&request',option_js.request,"menu-posts-request");
	wpqa_js_menus('edit.php?post_type=report&types',option_js.report_type,"menu-posts-report");

	/* Payment new */
	if (option_js.new_payments > 0) {
		jQuery("#menu-posts-statement .wp-menu-name").append(' <span class="count_report_new awaiting-mod count-'+option_js.new_payments+'"><span class="count_lasts">'+option_js.new_payments+'</span></span>');
	}
	if (jQuery('.payment_new').length) {
		jQuery(".payment_new").each(function () {
	    	var payment_new = jQuery(this);
	    	payment_new.closest(".type-statement").addClass("unapproved");
		});
	}
	
	/* Refund the payment */
	if (jQuery('.refund-button').length) {
	    jQuery(".refund-button").on("click",function() {
	    	var confirm_refunded = confirm(option_js.confirm_refund);
			if (confirm_refunded) {
		    	var refund = jQuery(this);
		    	var post_id = refund.data("id");
		    	var user_id = refund.data("user");
		    	var payment_id = refund.data("pi");
		    	payment_id = (payment_id !== undefined && payment_id !== false?payment_id:"");
		    	refund.css({"visibility":"hidden"});
				jQuery.ajax({
					url: option_js.ajax_a,
					type: "POST",
					data: { action : 'wpqa_refund_payment', post_id : post_id, user_id : user_id, payment_id : payment_id },
					success:function(data) {
						refund.closest(".type-statement").find(".column-price .money-span").after('<span class="money-span refund-span margin_l_20">'+option_js.refunded+'</span>');
						if (data != "") {
							refund.closest(".type-statement").find(".column-transaction").append('<span class="gray-span margin_l_20">'+data+'</span>');
						}
					}
				});
			}
	    	return false;
	    });
	}

	/* Report new */
	if (option_js.new_reports > 0) {
		jQuery("#menu-posts-report .wp-menu-name").append(' <span class="count_report_new awaiting-mod count-'+option_js.new_reports+'"><span class="count_lasts">'+option_js.new_reports+'</span></span>');
		jQuery("#menu-posts-report a[href='edit.php?post_type=report&types=questions']").append(' <span class="count_report_new awaiting-mod count-'+option_js.new_question_reports+'"><span class="count_lasts">'+option_js.new_question_reports+'</span></span>');
		jQuery("#menu-posts-report a[href='edit.php?post_type=report&types=answers']").append(' <span class="count_report_new awaiting-mod count-'+(option_js.new_answer_reports)+'"><span class="count_lasts">'+(option_js.new_answer_reports)+'</span></span>');
		jQuery("#menu-posts-report a[href='edit.php?post_type=report&types=users']").append(' <span class="count_report_new awaiting-mod count-'+(option_js.new_user_reports)+'"><span class="count_lasts">'+(option_js.new_user_reports)+'</span></span>');
	}
	if (jQuery('.report_new').length) {
		jQuery(".report_new").each(function () {
	    	var report_new = jQuery(this);
	    	report_new.closest(".type-report").addClass("unapproved");
		});
	}

	/* Request new */
	if (option_js.new_requests > 0) {
		jQuery("#menu-posts-request .wp-menu-name").append(' <span class="count_report_new awaiting-mod count-'+option_js.new_requests+'"><span class="count_lasts">'+option_js.new_requests+'</span></span>');
	}
	if (jQuery('.request_new').length) {
		jQuery(".request_new").each(function () {
	    	var request_new = jQuery(this);
	    	request_new.closest(".type-request").addClass("unapproved");
		});
	}
	
	/* Fix the counter */
	if (jQuery('.fix-comments').length) {
		jQuery(".fix-comments").on("click",function () {
	    	var fix_comments = jQuery(this);
	    	var post_id = fix_comments.data("post");
	    	jQuery.post(option_js.ajax_a,"action=wpqa_confirm_fix_comments&post_id="+post_id,function (result) {
		    	location.reload();
	    	});
			return false;
		});
	}
	
})(jQuery);