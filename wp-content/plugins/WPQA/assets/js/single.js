(function($) { "use strict";

	var $window = jQuery(window);
	
	/* Popup Share */
	
	function panel_pop_share(whatId) {
		if (jQuery(whatId).length && jQuery(".comments-popup-share").length == 0 && jQuery(".respond-popup-share").length == 0 && jQuery(".popup-share-show").length == 0) {
			var data_width = jQuery(whatId).attr("data-width");
			var data_height = jQuery(whatId).height();
			jQuery(".panel-pop").css({"top":"-100%","display":"none"});
			if (!jQuery(".wrap-pop").hasClass("wrap-pop-not-close")) {
				jQuery(".wrap-pop").remove();
				jQuery("body").removeClass("body-open-pop-up");
			}
			var is_RTL = jQuery('body').hasClass('rtl')?true:false;
			var cssMargin = (is_RTL == true?"margin-right":"margin-left");
			var cssValue = "-"+(data_width !== undefined && data_width !== false?data_width/2:"")+"px";
			var cssValue2 = "-"+(data_height !== undefined && data_height !== false?data_height/2:"")+"px";
			jQuery(whatId).css("width",(data_width !== undefined && data_width !== false?data_width:"")+"px").css({"position":"fixed","top":"50%","margin-top":cssValue2}).css(cssMargin,cssValue).addClass("popup-share-show").show();
			if (!jQuery(".wrap-pop").hasClass("wrap-pop-not-close")) {
				jQuery(".put-wrap-pop").prepend("<div class='wrap-pop'></div>");
				jQuery("body").addClass("body-open-pop-up");
			}
			if (!jQuery(".wrap-pop").hasClass("wrap-pop-not-close")) {
				jQuery(".wrap-pop").on("click",function () {
					jQuery.when(jQuery(".panel-pop").fadeOut(200)).done(function() {
						jQuery(this).css({"top":"-100%","display":"none"});
						jQuery("#wpqa-message .the-title").val("");
						jQuery(".wrap-pop").remove();
						jQuery("body").removeClass("body-open-pop-up");
					});
				});
			}
		}
	}

	if (jQuery(".popup-share-visit").length) {
		var popup_share_seconds = wpqa_single.popup_share_seconds;
		if (popup_share_seconds > 0) {
			setTimeout(function() {panel_pop_share("#wpqa-share");},popup_share_seconds*1000);
		}else {
			panel_pop_share("#wpqa-share");
		}
	}else {
		var scroll_position = 0;
		var ticking = false;
		var hidden_comments = (jQuery("#comments").length?jQuery("#comments").offset().top:0);
		var hidden_respond = (jQuery("#respond-all").length?jQuery("#respond-all").offset().top:0);
		function wpqa_open_share_popup(scroll_pos) {
			var viewPortTop = $window.scrollTop();
			var viewPortBottom = viewPortTop + $window.height();
			var scroll_share = '';
			if (typeof(localStorage) != 'undefined') {
				scroll_share = localStorage.getItem('scroll-share');
			}
			if (scroll_share > 0 && scroll_share > scroll_pos) {
				if ((hidden_comments > 0 && (hidden_comments - viewPortBottom) > 70 && (hidden_comments - viewPortBottom) < 300) || (hidden_respond > 0 && (hidden_respond - viewPortBottom) > 70 && (hidden_respond - viewPortBottom) < 300)) {
					panel_pop_share("#wpqa-share");
				}
			}
		}
		window.addEventListener('scroll', function(e) {
			scroll_position = window.scrollY;
			if (!ticking) {
				window.requestAnimationFrame(function() {
					wpqa_open_share_popup(scroll_position);
					if (typeof(localStorage) != 'undefined') {
						localStorage.setItem('scroll-share',scroll_position);
					}
					ticking = false;
				});
			ticking = true;
			}
		});
		if (jQuery("#comments").length) {
			jQuery("#comments").on("inview", function(event, isInView, visiblePartX, visiblePartY) {
				if (isInView) {
					panel_pop_share("#wpqa-share");
					jQuery("#comments").addClass("comments-popup-share");
				}
			});
		}else if (jQuery("#respond-all").length) {
			jQuery("#respond-all").on("inview", function(event, isInView, visiblePartX, visiblePartY) {
				if (isInView) {
					panel_pop_share("#wpqa-share");
					jQuery("#respond-all").addClass("respond-popup-share");
				}
			});
		}
	}

	/* Show answer */
	
	if (jQuery(".show-answer-form").length) {
		jQuery(".show-answer-form").on("click",function() {
			jQuery(".show-answer-form").hide(10);
			jQuery(".comment-form-hide").animate({opacity: 'show' , height: 'show'}, 400).addClass("comment-form-show");
			setTimeout(function () {
				jQuery(window).trigger('sticky_recalc');
			},1000);
		});
	}
	
	/* Delete question or post */
	
	if (jQuery(".post-delete,.question-delete").length) {
		jQuery(".post-delete,.question-delete").on("click",function () {
			var var_delete = (jQuery(".post-delete").length?wpqa_single.sure_delete_post:wpqa_single.sure_delete);
			if (confirm(var_delete)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	/* Close and open question */
	
	question_stats("close");
	question_stats("open");
	
	function question_stats(stats) {
		if (jQuery(".question-"+stats).length) {
			jQuery(".question-"+stats).on("click",function () {
				var question_stats = jQuery(this);
				var question_class = question_stats.closest(".article-question.article-post.question");
				var post_id = question_class.attr('id').replace("post-","");
				var wpqa_open_close_nonce = question_stats.data("nonce");
				question_stats.hide();
				jQuery.ajax({
					url: wpqa_single.admin_url,
					type: "POST",
					data: { action : 'wpqa_question_'+stats, wpqa_open_close_nonce : wpqa_open_close_nonce, post_id : post_id },
					success:function(data) {
						location.reload();
					}
				});
				return false;
			});
		}
	}

	/* Add Point */
	
	if (jQuery(".bump-question-area a").length) {
		jQuery(".bump-question-area a").on("click",function () {
			var point_a = jQuery(this);
			var input_add = jQuery("#input-add-point");
			var input_add_point = input_add.val();
			var question_class = point_a.closest(".article-question.article-post.question");
			var question_content = point_a.closest(".question-content");
			var post_id = question_class.attr("id").replace('post-',"");
			point_a.hide();
			point_a.parent().find(".load_span").show();
			jQuery.ajax({
				url: wpqa_single.admin_url,
				type: "POST",
				data: {action:'wpqa_add_point',input_add_point:input_add_point,post_id:post_id},
				success:function(data) {
					if (data == "get_points") {
						question_content.find(".wpqa_success").hide(10).text(wpqa_single.get_points).slideDown(200).delay(5000).slideUp(200);
					}else {
						question_content.find(".wpqa_error").hide(10).text(data).slideDown(200).delay(5000).slideUp(200);
					}
					point_a.show();
					point_a.parent().find(".load_span").hide();
					input_add.val("");
				}
			});
			return false;
		});
	}
	
	/* Comments & Answers */
	
	if (jQuery("#commentform").length) {
		jQuery("#commentform").attr("enctype","multipart/form-data").on("submit",function () {
			var thisform = jQuery(this);
			jQuery('.wpqa_error',thisform).hide().find(".required-error").remove();
			if (jQuery('.wpqa_captcha',thisform).length) {
				var wpqa_captcha = jQuery('.wpqa_captcha',thisform).parent().find("input");
				var url = wpqa_single.wpqa_dir+"captcha/captcha.php";
				var captcha_val = wpqa_captcha.attr("name")+"="+encodeURIComponent(wpqa_captcha.val());
				wpqa_captcha.css("border-color","#e1e2e3");
				if (wpqa_captcha.val() == "") {
					wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_single.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
					jQuery("#commentform .load_span").hide();
					jQuery("#commentform .button-hide-click").show();
					return false;
				}else if (wpqa_captcha.hasClass("captcha_answer")) {
					if (wpqa_captcha.val() != wpqa_single.captcha_answer) {
						wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_single.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery("#commentform .load_span").hide();
						jQuery("#commentform .button-hide-click").show();
						return false;
					}else {
						return true;
					}
				}else {
					var message = "";
					jQuery.ajax({
						url   : url,
						type  : "POST",
						data  : captcha_val,
						async : false,
						success: function(data){
							message = data;
						}
					});
					if (message.indexOf('wpqa_captcha_0') > -1) {
						wpqa_captcha.css("border-color","#F00").parent().parent().parent().find(".wpqa_error").html('<span class="required-error required-error-c">'+wpqa_single.wpqa_error_captcha+'</span>').animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery("#commentform .load_span").hide();
						jQuery("#commentform .button-hide-click").show();
						return false;
					}else {
						return true;
					}
				}
			}
		});
	}
	
	if (jQuery("li.comment").length) {
		wpqa_best_answer("best_answer_re");
		wpqa_best_answer("best_answer_a");
		
		function wpqa_best_answer(type) {
			jQuery("li.comment").on("click","."+type,function () {
				jQuery("#comments .wpqa_error").slideUp(200);
				var best_answer = jQuery(this);
				var comment_id = best_answer.closest("li.comment").attr('id').replace("li-comment-","");
				var nonce = best_answer.data("nonce");
				jQuery("."+type).hide();
				jQuery.ajax({
					url: wpqa_single.admin_url,
					type: "POST",
					data: { action : 'wpqa_'+type, comment_id : comment_id, wpqa_best_answer_nonce : nonce },
					success:function(result) {
						if (result == "best") {
							if (type == "best_answer_a") {
								jQuery("#comment-"+comment_id).addClass(".comment-best-answer");
								jQuery("#comment-"+comment_id+" .comment-meta").before('<div class="best-answer">'+wpqa_single.best_answer+'</div>');
								jQuery("#comment-"+comment_id+" .wpqa-add-remove-best-answer").html('<a class="best_answer_re" data-nonce="'+wpqa_single.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_single.cancel_best_answer+'"><i class="icon-cancel"></i>'+wpqa_single.cancel_best_answer+'</a>');
							}else {
								jQuery(".commentlist .wpqa-add-remove-best-answer").html('<a class="best_answer_a" data-nonce="'+wpqa_single.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_single.choose_best_answer+'"><i class="icon-check"></i>'+wpqa_single.choose_best_answer+'</a>');
								jQuery(".best-answer,.answers-area.best-answers").remove();
								jQuery(".comment-best-answer").removeClass("comment-best-answer");
							}
						}else if (result == "remove_best") {
							jQuery(".commentlist .wpqa-add-remove-best-answer").html('<a class="best_answer_a" data-nonce="'+wpqa_single.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_single.choose_best_answer+'"><i class="icon-check"></i>'+wpqa_single.choose_best_answer+'</a>');
							jQuery(".best-answer,.answers-area.best-answers").remove();
							jQuery(".comment-best-answer").removeClass("comment-best-answer");
						}else {
							jQuery("#comment-"+result).addClass(".comment-best-answer").find(".wpqa_error").text(wpqa_single.best_answer_selected).slideDown(200);
							jQuery("#comment-"+result+" .comment-meta").before('<div class="best-answer">'+wpqa_single.best_answer+'</div>');
							jQuery("#comment-"+result+" .wpqa-add-remove-best-answer").html('<a class="best_answer_re" data-nonce="'+wpqa_single.wpqa_best_answer_nonce+'" href="#" title="'+wpqa_single.cancel_best_answer+'"><i class="icon-cancel"></i>'+wpqa_single.cancel_best_answer+'</a>');
							jQuery("html,body").animate({scrollTop: jQuery("#comment-"+result).offset().top-35},"slow");
						}
						jQuery("."+type).parent().remove();
					}
				});
				return false;
			});
		}
	}
	
	if (jQuery(".single-question .comment-best-answer.not-activate-gender").length) {
		jQuery(".comment-best-answer").prependTo("ol.commentlist");
	}

	if (jQuery("#comments").length) {
		jQuery(document).on("click",".answer-question-not-jquery .best-answer-meta a,.comments-link",function () {
			jQuery("html,body").animate({scrollTop: jQuery("#comments").offset().top-35},"slow");
		});
	}
	
	if (jQuery("#comments-female,#comments-male,#comments-best,#comments-other").length) {
		jQuery(".answer-meta-gender.answer-meta-her,.answer-meta-gender.answer-meta-him,.answer-meta-gender.answer-meta-other,.best-answer-icon").each(function () {
			jQuery(this).on("click",function () {
				if (jQuery(this).hasClass("best-answer-icon")) {
					jQuery("html,body").animate({scrollTop: jQuery("#comments-best").offset().top-35},"slow");
				}else if (!jQuery(this).hasClass("best-answer-meta")) {
					var idAnimate = (jQuery(this).hasClass("answer-meta-her")?"#comments-female":(jQuery(this).hasClass("answer-meta-him")?"#comments-male":"#comments-other"));
					jQuery("html,body").animate({scrollTop: jQuery(idAnimate).offset().top-35},"slow");
				}
			});
		});
	}

	if (jQuery(".single-question").length) {
		if (window.location.hash == "#respond") {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show().addClass("comment-form-show");
			if (jQuery("#respond").length) {
				jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
			}else if (jQuery(".question-adv-comments .alert-message").length) {
				jQuery("html,body").animate({scrollTop: jQuery(".question-adv-comments .alert-message").offset().top-35},"slow");
			}
		}
	}
	
	if (jQuery("#respond").length) {
		jQuery(document).on("click",".meta-answer",function () {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show().addClass("comment-form-show");
			jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
			setTimeout(function () {
				jQuery(window).trigger('sticky_recalc');
			},1000);
		});
		
		jQuery(".single").on("click",".wpqa-reply-link",function () {
			if (wpqa_single.activate_editor_reply == "on" || wpqa_single.is_logged == "unlogged") {
				jQuery(".show-answer-form").remove();
				jQuery(".comment-form-hide,.comment-form-hide").show().addClass("comment-form-show");
				var reply_link = jQuery(this);
				jQuery(".wpqa-cancel-link").remove();
				jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
				jQuery(".respond-edit-delete").show();
				jQuery("#respond #comment_parent").val(reply_link.attr("data-id"));
				jQuery("#respond .section-title").append('<div class="wpqa-cancel-link cancel-comment-reply"><a rel="nofollow" id="cancel-comment-reply-link" href="#respond">'+wpqa_single.cancel_reply+'</a></div>');
			}else {
				var commentthis = jQuery(this);
				var post_id = commentthis.data("post_id");
				var comment_id = commentthis.data("id");
				var aria_label = commentthis.attr("aria-label");
				var comment_parent = jQuery("#li-comment-"+comment_id);
				jQuery("#respond-all,.comment #respond").hide(10);
				if (comment_parent.find(" > .comment-respond").length == 0) {
					comment_parent.find(" > .comment-body").after('<div id="respond" class="comment-respond wpqa_hide">\
						<h3 class="section-title">'+aria_label+'\
							<div class="wpqa-cancel-link cancel-comment-reply">\
								<a rel="nofollow" id="cancel-comment-reply-link" href="#respond">'+wpqa_single.cancel_reply+'</a>\
							</div>\
						</h3>\
						<form action="'+wpqa_single.comment_action+'" method="post" id="commentform" class="post-section comment-form answers-form">\
							<p class="comment-login">\
								'+wpqa_single.logged_as+'\
								<a class="comment-login-login" href="'+wpqa_single.profile_url+'"><i class="icon-user"></i>'+wpqa_single.display_name+'</a>\
							<a class="comment-login-logout" href="'+wpqa_single.logout_url+'" title="'+wpqa_single.logout_title+'"><i class="icon-logout"></i>'+wpqa_single.logout+'</a>\
							</p>\
							<div class="wpqa_error"></div>\
							<div class="form-input form-textarea form-comment-normal">\
								<textarea id="comment" name="comment" rows="5" class="form-control" aria-required="true" placeholder="'+wpqa_single.reply+'"></textarea>\
								<i class="icon-pencil"></i>\
							</div>\
							<div class="clearfix"></div>\
							<p class="form-submit">\
								<input name="submit" type="submit" id="submit" class="button-default button-hide-click btn btn__primary" value="'+wpqa_single.submit+'">\
								<span class="clearfix"></span>\
								<span class="load_span"><span class="loader_2"></span></span>\
								<input type="hidden" name="comment_post_ID" value="'+post_id+'" id="comment_post_ID">\
								<input type="hidden" name="comment_parent" id="comment_parent" value="'+comment_id+'">\
							</p>\
						</form>\
					</div>');
				}
				comment_parent.find(" > .comment-respond").show();
				jQuery("html,body").animate({scrollTop: jQuery("#li-comment-"+comment_id+" > .comment-respond").offset().top-35},"slow");
			}
			setTimeout(function () {
				jQuery(window).trigger('sticky_recalc');
			},1000);
			return false;
		});
		
		jQuery(document).on("click",".wpqa-cancel-link a",function () {
			jQuery(".wpqa-cancel-link,.comment #respond").remove();
			jQuery(".respond-edit-delete").hide();
			jQuery("#respond-all").show();
			setTimeout(function () {
				jQuery(window).trigger('sticky_recalc');
			},1000);
			return false;
		});
		
		var check_email = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		jQuery("#respond").on("click","#submit",function () {
			var submint_button = jQuery(this);
			var respond = submint_button.closest("#respond");
			if (respond.closest("#respond-all").length && wpqa_single.comment_editor == "on") {
				if (respond.find(".tmce-active").length) {
					var comment_text = respond.find(".form-comment-editor iframe").contents().find("body").html();
				}else {
					var comment_text = respond.find(".wp-editor-area").val();
				}
			}else {
				var comment_text = respond.find("#comment").val();
			}

			var comment_name = (respond.find("#comment_name").length?respond.find("#comment_name").val():"not_empty");
			var comment_email = (respond.find("#comment_email").length?respond.find("#comment_email").val():"not_empty");
			if (wpqa_single.terms_active_comment == "on") {
				var agree_terms = respond.find("#agree_terms_comment").is(":checked");
				if (agree_terms != 1) {
					respond.find(".wpqa_error").animate({opacity: 'hide' , height: 'hide'}, 400).remove();
					respond.find(".comment-form").prepend('<div class="wpqa_error">'+wpqa_single.wpqa_error_terms+'</div>');
					respond.find(".wpqa_error").animate({opacity: 'show' , height: 'show'}, 400);
					jQuery("#commentform .load_span").hide();
					jQuery("#commentform .button-hide-click").show();
					return false;
				}
			}
			if ((wpqa_single.require_name_email == 'require_name_email' && comment_email != 'not_empty' && !check_email.test(comment_email)) || (wpqa_single.require_name_email == 'require_name_email' && (comment_name == '' || comment_email == '')) || comment_text == '' || comment_text == '<p><br data-mce-bogus="1"></p>' || comment_text == '<p><br></p>' || comment_text == '<p></p>') {
				if ((wpqa_single.require_name_email == 'require_name_email' && comment_email != 'not_empty' && !check_email.test(comment_email)) || (wpqa_single.require_name_email == 'require_name_email' && (comment_name == '' || comment_email == ''))) {
					if (comment_name == '') {
						var wpqa_text_error = wpqa_single.wpqa_error_name;
					}else if (comment_email == '') {
						var wpqa_text_error = wpqa_single.wpqa_error_email;
					}else if (comment_email !=  'not_empty' && !check_email.test(comment_email)) {
						var wpqa_text_error = wpqa_single.wpqa_valid_email;
					}
				}else {
					var wpqa_text_error = wpqa_single.wpqa_error_comment;
				}
				respond.find(".wpqa_error").animate({opacity: 'hide' , height: 'hide'}, 400).remove();
				if (wpqa_text_error !== undefined && wpqa_text_error !== false) {
					respond.find(".comment-form").prepend('<div class="wpqa_error">'+wpqa_text_error+'</div>');
				}
				respond.find(".wpqa_error").animate({opacity: 'show' , height: 'show'}, 400);
				jQuery("#commentform .load_span").hide();
				jQuery("#commentform .button-hide-click").show();
				return false;
			}

			var comment_limit_var = wpqa_single.comment_limit;
			var comment_min_limit_var = wpqa_single.comment_min_limit;
			if (respond.find(".button-default-question")) {
				comment_limit_var = wpqa_single.answer_limit;
				comment_min_limit_var = wpqa_single.answer_min_limit;
			}

			if (comment_limit_var > 0 || comment_min_limit_var > 0) {
				var message = "";
				jQuery.ajax({
					url: wpqa_single.admin_url,
					type: "POST",
					data: { action : 'wpqa_comment_limit', comment_text : comment_text, comment_limit : comment_limit_var, comment_min_limit : comment_min_limit_var },
					async : false,
					success: function(data) {
						message = data;
					}
				});
				var wpqa_error_limit = wpqa_single.wpqa_error_limit;
				if (message.indexOf('wpqa_error') > -1 || message.indexOf('wpqa_min_error') > -1) {
					respond.find(".wpqa_error").animate({opacity: 'hide' , height: 'hide'}, 400).remove();
					respond.find(".comment-form").prepend('<div class="wpqa_error">'+(message.indexOf('wpqa_error') > -1?wpqa_single.wpqa_error_limit+': '+comment_limit_var:wpqa_single.wpqa_error_min_limit+': '+comment_min_limit_var)+'</div>');
					respond.find(".wpqa_error").animate({opacity: 'show' , height: 'show'}, 400);
					jQuery("#commentform .load_span").hide();
					jQuery("#commentform .button-hide-click").show();
					return false;
				}
			}
		});
	}
	
})(jQuery);