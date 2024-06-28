jQuery(document).ready(function($) {
	jQuery(window).on("load",function() {
		if (jQuery(".upload_image_button.upload_image_button_m").length) {
			var custom_uploader;
			jQuery('.upload_image_button.upload_image_button_m').on("click",function(e) {
				var image_var = jQuery(this);
				e.preventDefault();
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
				//Extend the wp.media object
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: theme_js_var.choose_image,
					button: {
						text: theme_js_var.choose_image
					},
					multiple: true
				});
				custom_uploader.on('select', function() {
					var selection = custom_uploader.state().get('selection');
					selection.map( function( attachment ) {
						attachment = attachment.toJSON();
						if (jQuery("#"+image_var.attr("data-id")+"-item-"+attachment.id).length == 0) {
							jQuery("#"+image_var.attr("data-id")).append("<li id='"+image_var.attr("data-id")+"-item-"+attachment.id+"' class='multi-images'>\
								<div class='multi-image'>\
									<img alt='"+attachment.url+"' src='"+attachment.url+"'><input type='hidden' name='"+image_var.attr("data-name")+"[]' value='"+attachment.id+"'>\
									<div class='image-overlay'></div>\
									<div class='image-media-bar'>\
										<a class='image-edit-media' title='"+theme_js_var.edit_image+"' href='post.php?post="+attachment.id+"&amp;action=edit' target='_blank'>\
											<span class='dashicons dashicons-edit'></span>\
										</a>\
										<a href='#' class='image-remove-media' title='"+theme_js_var.remove_image+"'>\
											<span class='dashicons dashicons-no-alt'></span>\
										</a>\
									</div>\
								</div>\
							</li>");
						}
					});
				});
				custom_uploader.open();
			});
		}
		
		jQuery(document).on("click",".image-remove-media",function () {
			jQuery(this).parent().parent().parent().addClass('removered').fadeOut(function() {
				jQuery(this).remove();
			});
			return false;
		});
		
		jQuery(".builder_select").on('mouseup',function () {
			var copyText = jQuery(this);
			copyText.select();
			document.execCommand("copy");
		});
		
		/* Change values for radio & checkboxes & text & select */
		jQuery("#builder").on('click',"input[type='radio']",function() {
			jQuery(this).parent().find(":radio").removeAttr("checked");
			jQuery(this).attr("checked","checked");
		});
		
		jQuery("#builder").on('click',"input[type='checkbox']",function() {
			if (jQuery(this).is(":checked")) {
				jQuery(this).attr("checked","checked");
			}else {
				jQuery(this).removeAttr("checked");
			}
		});
		
		function change_inputs_values() {
			change_inputs_values_text();
			change_inputs_values_select();
		}
		
		function change_inputs_values_text() {
			jQuery("#builder input[type='text'],#builder_slide input[type='text']").each(function () {
				jQuery(this).on('keyup change',function() {
					jQuery(this).attr("value",jQuery(this).val());
				});
			});
		}
		
		function change_inputs_values_select() {
			jQuery("#builder select").each(function () {
				jQuery(this).on('change',function() {
					var main_select = jQuery(this);
					var multiple_s = main_select.attr("multiple");
					if (multiple_s !== undefined && multiple_s !== false) {
						var m_select = [];
						jQuery(this).find("option").each(function(i, selectedElement) {
							m_select[i] = jQuery(selectedElement).val();
							if (jQuery.inArray(m_select[i],main_select.val()) > -1) {
								jQuery(this).attr("selected","selected");
							}else {
								jQuery(this).removeAttr("selected");
							}
						});
					}else {
						main_select.find("option").each(function () {
							if (jQuery(this).val() == main_select.val()) {
								if (jQuery(this).filter(':selected')) {
									jQuery(this).attr("selected","selected");
								}else {
									jQuery(this).removeAttr("selected");
								}
							}else {
								jQuery(this).removeAttr("selected");
							}
						});
					}
				});
			});
		}
		
		/* Sort the sections */
		function sort_sections() {
			jQuery('.framework-main .sort-sections').each(function () {
				if (!jQuery(this).hasClass("not-sort") && jQuery(this).hasClass("sort-sections-with")) {
					jQuery(this).sortable({
						placeholder: "ui-state-highlight",
						connectWith: "ul.sort-sections-with",
						handle: ".widget-head,.widget-handle",
						cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item,.switch,.not-sort .widget-handle,.not-sort .del-builder-item",
						receive: function (event, ui) {
							ui.item.addClass('builder_moved');
							var data_js_2 = jQuery(".builder_moved").attr("data-js-2");
							var builder_id = jQuery(".builder_moved").parent().attr("data-id").replace("builder_item_","");
							if (builder_id !== data_js_2) {
								var builder_li = jQuery(".builder_moved").parent().find(" > li").length;
								var builder_moved = jQuery(".builder_moved").clone().html();
								var data_id = jQuery(".builder_moved").attr("data-id");
								var data_js = jQuery(".builder_moved").attr("data-js");
								var last_ids = builder_id+'_'+builder_li;
								
								var i_count = 1;
								while (i_count < builder_li) {
									if (jQuery("#inner_builder_"+last_ids).length) {
										builder_li++;
										var last_ids = builder_id+'_'+builder_li;
									}
									i_count++;
								}
								
								var inner_id = "inner_builder_"+last_ids;
								if (jQuery('.builder_moved .adv_code_text').length) {
									var builder_id_editor = jQuery('.builder_moved .adv_code_text').attr("data-ids");
									var content = "";
									if (jQuery("#"+builder_id_editor+"-tmce").length) {
										jQuery("#"+builder_id_editor+"-tmce").trigger("click");
									}
									if (jQuery("#"+builder_id_editor).length) {
										content = tinyMCE.get(builder_id_editor).getContent();
									}
								}
								builder_moved = builder_moved.split(data_id+"["+data_js+"]").join("builder_item["+builder_id+"][options]["+builder_li+"]");
								jQuery(".builder_moved").attr("data-js",builder_li).attr("id",inner_id).attr("data-js-2",builder_id).attr("data-id",jQuery(".builder_moved").parent().attr("data-id")+"_"+builder_li);
								jQuery(".builder_moved").html(builder_moved);
								if (jQuery('#'+inner_id).find('.adv_code_text').length) {
									jQuery('#'+inner_id).find('.adv_code_text').attr("data-ids","builder_item_"+last_ids).attr("data-names","builder_item["+builder_id+"][options]["+builder_li+"][adv_code]");
									framework_new_editor("builder_item_"+last_ids,"builder_item["+builder_id+"][options]["+builder_li+"][adv_code]",content,jQuery('#'+inner_id).find('.adv_code_text > div'));
								}
							}
							ui.item.removeClass('builder_moved');
						}
					});
				}
			});
		}
		
		/* Change the head title */
		function framework_check(selector) {
			if (selector.find("input[type='checkbox']").length) {
				selector.find("input[type='checkbox']").each(function () {
					var framework_checkbox = jQuery(this);
					var checkbox_attr = framework_checkbox.attr("id");
					if (checkbox_attr !== undefined && checkbox_attr !== false) {
						checkbox_attr = " for='"+checkbox_attr+"'";
					}else {
						checkbox_attr = "";
					}
					framework_checkbox.wrap("<label class='switch'"+checkbox_attr+"></label>");
					framework_checkbox.after("<label"+checkbox_attr+" data-on='ON' data-off='OFF'></label>");
				});
			}
		}
		
		/* Click on radio */
		jQuery("#builder").on("click",".checkbox_select .checkbox-select",function () {
			jQuery(this).parent().parent().find(":radio").removeAttr('checked');
			jQuery(this).parent().parent().find("li").removeClass("selected");
			jQuery(this).parent().addClass("selected");
			jQuery(this).parent().find(":radio").attr("checked","checked");
			return false;
		});
		
		jQuery(".checkbox_select input:checked").parent().addClass("selected");
		
		/* Checkbox */
		jQuery(".checkbox_checkbox input:checked").parent().addClass("selected");
		jQuery("#builder").on("click",".checkbox_checkbox .checkbox-select",function () {
			jQuery(this).parent().toggleClass("selected");
			jQuery(this).parent().find(":checkbox").attr('checked', !jQuery(this).parent().find(":checkbox").attr('checked'));
			return false;
		});
		
		/* Sort elements */
		jQuery("#builder,#builder_slide").sortable({placeholder: "ui-state-highlight",handle: ".widget-head",cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item,.switch"});
		
		/* Toggle open & close */
		jQuery("#builder,#builder_slide").on("click",".builder-toggle-open",function () {
			var this_toggle = jQuery(this);
			this_toggle.parent().parent().find(" > .widget-content").slideToggle(300);
			this_toggle.css("display","none");
			this_toggle.parent().parent().find(" > .widget-head .builder-toggle-close").css("display","block");
		});
		
		jQuery("#builder,#builder_slide").on("click",".builder-toggle-close",function () {
			var this_toggle = jQuery(this);
			this_toggle.parent().parent().find(" > .widget-content").slideToggle("fast");
			this_toggle.css("display","none");
			this_toggle.parent().parent().find(" > .widget-head .builder-toggle-open").css("display","block");
		});
		
		/* Clone row & element  */
		jQuery("#builder,#builder_slide").on("click",".builder_clone",function() {
			var builder_li = jQuery(this).parent().parent().parent().find(" > li").length;
			var builder_clone = jQuery(this).parent().parent().clone().html();
			var builder_id = jQuery(this).parent().parent().parent().attr("id");
			var bui_id = jQuery(this).parent().parent().attr("id").replace("builder_","");
			var data_id = jQuery(this).parent().parent().attr("data-id");
			builder_li = builder_li+1;
			if (builder_id == "builder_slide") {
				var inner_builder_id = 'builder_slide_'+builder_li;
				builder_clone = builder_clone.split(data_id).join("builder_slide_item["+builder_li+"]");
				jQuery(this).parent().parent().after('<li id="'+inner_builder_id+'" data-id="builder_slide_item['+builder_li+']">'+builder_clone+"</li>");
				jQuery('#'+inner_builder_id+' > .widget-head > span > span').text(builder_li);
				jQuery("html,body").animate({scrollTop: jQuery("#"+inner_builder_id).offset().top-35},"slow");
				jQuery("#"+inner_builder_id).hide().fadeIn();
			}else {
				var data_js = jQuery(this).parent().parent().attr("data-js");
				var data_js_2 = jQuery(this).parent().parent().attr("data-js-2");
				var last_ids = data_js_2+"_"+builder_li;
				
				var i_count = 1;
				while (i_count < builder_li) {
					if (jQuery("#builder_"+data_js_2+" #inner_builder_"+data_js_2+"_"+builder_li).length) {
						builder_li++;
						var last_ids = data_js_2+"_"+builder_li;
					}
					i_count++;
				}
				
				var inner_builder_id = 'inner_builder_'+last_ids;
				builder_clone = builder_clone.split(data_id+"["+data_js+"]").join(data_id+"["+builder_li+"]");
				jQuery(this).parent().parent().after('<li id="'+inner_builder_id+'" data-js="'+builder_li+'" data-js-2="'+data_js_2+'" data-id="builder_item['+data_js_2+'][options]">'+builder_clone+"</li>");
				jQuery("html,body").animate({scrollTop: jQuery("#inner_builder_"+last_ids).offset().top-35},"slow");
				jQuery("#inner_builder_"+last_ids).hide().fadeIn();
			}
			sort_sections();
			change_inputs_values();
			jQuery('.tooltip_s').tipsy({gravity: 's'});
		});
		
		if (jQuery(".framework_scroll").length) {
			jQuery(".framework_scroll").scrollTop(300);
		}
		
		/* Builder */
		var categories_select = jQuery('#framework_categories_select').html();
		jQuery('#framework_categories_select').remove();
		var product_cat_select = jQuery('#framework_product_cat').html();
		jQuery('#framework_product_cat').remove();
		var sidebars_select = jQuery('#framework_sidebars_select').html();
		jQuery('#framework_sidebars_select').remove();
		
		jQuery("#framework_builder_meta_tab,#"+theme_js_var.framework_name+"_slideshow_post").on("click",".add-item",function () {
			var builder_item = jQuery(this).attr("data-item");
			if (builder_item == "add_slide") {
				var builder_slide_j = jQuery('#builder_slide > li').length;
				builder_slide_j++;
				var i_count = 1;
				while (i_count < builder_slide_j) {
					if (jQuery("#builder_slide_"+builder_slide_j).length) {
						builder_slide_j++;
					}
					i_count++;
				}
				
				jQuery('#'+theme_js_var.framework_name+'_slideshow_post ul').append('<li id="builder_slide_'+builder_slide_j+'" data-id="builder_slide_item['+builder_slide_j+']"><div><a class="widget-handle ui-sortable-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a><div title="Clone" class="builder_clone"><span class="dashicons dashicons-welcome-add-page"></span></div></div><div class="widget-content"><h4 class="heading">'+theme_js_var.image_url+'</h4><div class="custom-meta-input"><input id="builder_slide_item['+builder_slide_j+'][image_url]" name="builder_slide_item['+builder_slide_j+'][image_url]" type="text" class="upload upload_image_'+builder_slide_j+'"><input class="upload_image_button button upload-button-2" rel="'+builder_slide_j+'" type="button" value="Upload"><input type="hidden" class="image_id" name="builder_slide_item['+builder_slide_j+'][image_id]"></div><div class="clear"></div><h4 class="heading">Slide Link</h4><div class="custom-meta-input"><input id="builder_slide_item['+builder_slide_j+'][slide_link]" name="builder_slide_item['+builder_slide_j+'][slide_link]" value="#" type="text"></div><div class="clear"></div></div></li>');
				jQuery("html,body").animate({scrollTop: jQuery("#builder_slide_"+builder_slide_j).offset().top-35},"slow");
				jQuery('#builder_slide_'+builder_slide_j).hide().fadeIn();
			}
			
			change_inputs_values();
			jQuery('.tooltip_s').tipsy({gravity: 's'});
			return false;
		});
		
		jQuery(".framework-main #role_add").click(function() {
			var data_id = jQuery(this).attr("data-id");
			var role_name = jQuery('#role_name').val();
			if (role_name != "" ) {
				if (role_name.length) {
					var roles_j = jQuery('.framework-main #roles_list li').length+1;
					
					var i_count = 1;
					while (i_count < roles_j) {
						if (jQuery("#li_roles_"+roles_j).length) {
							roles_j++;
						}
						i_count++;
					}
					
					var checkboxes_fields = {
						ask_question: theme_js_var.ask_question,
						ask_question_payment: theme_js_var.ask_question_payment,
						add_poll: theme_js_var.add_poll,
						ask_other_users: theme_js_var.ask_other_users,
						show_question: theme_js_var.show_question,
						approve_question: theme_js_var.approve_question,
						approve_question_media: theme_js_var.approve_question_media,
						add_category: theme_js_var.add_category,
						add_answer: theme_js_var.add_answer,
						add_answer_payment: theme_js_var.add_answer_payment,
						show_answer: theme_js_var.show_answer,
						approve_answer: theme_js_var.approve_answer,
						approve_answer_media: theme_js_var.approve_answer_media,
						add_group: theme_js_var.add_group,
						add_group_payment: theme_js_var.add_group_payment,
						approve_group: theme_js_var.approve_group,
						edit_other_groups: theme_js_var.edit_other_groups,
						add_post: theme_js_var.add_post,
						add_post_payment: theme_js_var.add_post_payment,
						show_post: theme_js_var.show_post,
						approve_post: theme_js_var.approve_post,
						show_comment: theme_js_var.show_comment,
						approve_comment: theme_js_var.approve_comment,
						add_comment: theme_js_var.add_comment,
						send_message: theme_js_var.send_message,
						upload_files: theme_js_var.upload_files,
						pay_to_user: theme_js_var.pay_to_user,
						pay_maximum: theme_js_var.pay_maximum,
						without_ads: theme_js_var.without_ads,
						show_knowledgebase: theme_js_var.show_knowledgebase
					};

					var inputs_fields = {
						questions_month: {language: theme_js_var.questions_month,value: 50},
						questions_day: {language: theme_js_var.questions_day,value: 10},
						answers_month: {language: theme_js_var.answers_month,value: 50},
						answers_day: {language: theme_js_var.answers_day,value: 10}
					};

					var checkboxes_output = inputs_output = '';
					for (var key in checkboxes_fields) {
						if (checkboxes_fields.hasOwnProperty(key) && (key != "show_knowledgebase" || (key == "show_knowledgebase" && theme_js_var.activate_knowledgebase == 'yes'))) {
							checkboxes_output += '<div class="section section-checkbox"><div class="option"><div class="controls"><label class="switch" for="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']"><input id="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']" value="on" class="checkbox framework-input" type="checkbox" checked name="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']"><label for="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']" data-on="'+theme_js_var.on+'" data-off="'+theme_js_var.off+'"></label></label><label class="explain explain-checkbox" for="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']">'+checkboxes_fields[key]+'</label></div></div></div>';
						}
					}
					for (var key in inputs_fields) {
						inputs_output += '<div class="section section-text framework-form-text"><div class="name-with-desc"><h4 class="heading">'+inputs_fields[key].language+'</h4></div><div class="controls"><input id="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']" value="'+inputs_fields[key].value+'" class="framework-input framework-form-control" type="text" name="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +']['+key+']"></div></div>';
					}
					jQuery('#roles_list').append('<li id="li_roles_'+roles_j+'">\
						<div class="widget-head">'+role_name+'<a class="del-roles-item del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>\
						<div class="widget-content">\
							<div class="widget-content-div">\
								<input id="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +'][new]" type="hidden" name="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +'][new]" value="new">\
								<input id="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +'][group]" type="hidden" name="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +'][group]" value="'+role_name+'">\
								<input type="hidden" class="group_role_name" name="'+theme_js_var.framework_theme+'['+data_id+'][group_'+ roles_j +'][id]" value="group_'+ roles_j +'">\
								<div class="clearfix"></div>\
								'+checkboxes_output+inputs_output+'\
							</div>\
						</div>\
					</li>');
					jQuery("html,body").animate({scrollTop: jQuery("#li_roles_"+roles_j).offset().top-35},"slow");
				}
			}else {
				alert("Please write the name.");
			}
			jQuery('#role_name').val("");
	
		});
		
		/* Add a custom addition */
		jQuery(".framework-main .add-item.add-item-2.add-item-6").on("click",function () {
			var add_item = jQuery(this);
			var item_id = jQuery(this).data("id");
			var item_type = jQuery(this).data("type");
			var addto = jQuery(this).data("addto");
			var toadd = jQuery(this).data("toadd");
			var item_name = jQuery(this).data("name");
			var select_val = add_item.parent().find("select").val();
			var select_text = add_item.parent().find("select option:selected").text();
			
			if (jQuery("#"+item_id+'_'+select_val).length) {
				jQuery("#"+item_id+'_'+select_val).addClass("removered").slideUp(function() {
					jQuery(this).slideDown().removeClass("removered");
				});
			}else {
				jQuery("#"+addto+"-ul").append('<li class="additions-li" id="'+item_id+'_'+select_val+'"><div class="widget-head"><'+(toadd == 'yes'?'label':'span')+'>'+select_text+'</'+(toadd == 'yes'?'label':'span')+'>'+(toadd == 'yes'?'':'</div>')+(toadd == 'yes'?'<input name="'+item_name+'['+item_type+'-'+select_val+']['+item_type+']" value="yes" type="hidden">':'')+'<input name="'+item_name+'['+item_type+'-'+select_val+']'+(toadd == 'yes'?'[value]':'')+'" value="'+select_val+'" type="hidden"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="'+(toadd == 'yes'?'del-cat-item ':'')+'del-builder-item"><span class="dashicons dashicons-trash"></span></a></div>'+(toadd == 'yes'?'</div>':'')+'</li>');
			}
		});
		
		/* Add a new poll */
		jQuery("#upload_add_ask").click(function() {
			var poll_item = jQuery('#question_poll_item li').length+1;

			var i_count = 1;
			while (i_count < poll_item) {
				if (jQuery("#poll_item_"+poll_item).length) {
					poll_item++;
				}
				i_count++;
			}

			jQuery('#question_poll_item').append('<li id="poll_item_'+poll_item+'"><div><a class="widget-handle ui-sortable-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div><div class="widget-content"><h4 class="heading">Value</h4><div class="custom-meta-input"><input id="ask['+poll_item+'][title]" name="ask['+poll_item+'][title]" type="text"><input id="ask['+poll_item+'][id]" name="ask['+poll_item+'][id]" value="'+poll_item+'" type="hidden"></div><div class="clear"></div></div></li>');
			return false;
		});
		
		/* Delete the item */
		jQuery(document).on("click",".del-builder-item",function() {
			if (jQuery(this).hasClass("del-element-item")) {
				jQuery(this).parent().parent().parent().parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			}else if (jQuery(this).hasClass("del-cat-item")) {
				jQuery(this).parent().parent().parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			}else if (jQuery(this).hasClass("del-roles-item")) {
				var roles_id = jQuery(this).parent().parent();
				var roles_val = roles_id.find(".group_role_name").val();
				jQuery.post(theme_js_var.ajax_a,{action:"wpqa_delete_role",roles_val:roles_val});
				roles_id.addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			}else if (jQuery(this).hasClass("del-sidebar-item")) {
				jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			}else {
				jQuery(this).parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			}
			return false;
		});
		
		/* Functions */
		change_inputs_values();
		sort_sections();

		/* Templates */
		jQuery(".components-dropdown.edit-post-post-template__dropdown").on("click",function () {
			var typingTimer;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(function () {
				jQuery("option[value='template-badges.php']").parent().addClass("wpqa_page_template");
			},1000);
		});
		if (jQuery("#page_template").length) {
			var page_template = "#page_template";
		}else {
			var page_template = ".wpqa_page_template";
		}
		jQuery(document).on("change",page_template,function () {
			var page_template_val = jQuery(this).val();
			if (jQuery(".framework-admin-content .nav-tab[data-template]") !== undefined && jQuery(".nav-tab[data-template]") !== false) {
				if (jQuery(".nav-tab[data-template='"+page_template_val+"']").length) {
					jQuery(".framework-main > .framework-group").hide();
					jQuery(".framework-admin-content .nav-tab").removeClass('nav-tab-active');
					
					jQuery(".framework-admin-content .nav-tab[data-template],.framework-main > .framework-group[data-template]").hide().addClass("hide");
					
					jQuery(".framework-admin-content .nav-tab[data-template='"+page_template_val+"']").addClass('nav-tab-active');
					jQuery(".framework-admin-content .nav-tab[data-template='"+page_template_val+"'],.framework-main > .framework-group[data-template='"+page_template_val+"']").show().removeClass('hide');
				}else {
					jQuery(".framework-admin-content .nav-tab[data-template],.framework-main > .framework-group[data-template]").hide().addClass("hide").removeClass('nav-tab-active');
					if (jQuery(".framework-admin-content .nav-tab.nav-tab-active").length) {
						/* Has active tab */
					}else {
						jQuery('.framework-main > .framework-group:not(.hide):first-child').animate({
							opacity: 'show',
							height: 'show'
						}, 200, function() {
							jQuery(this).removeClass('hide');
						});
						jQuery('.framework-admin-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
					}
				}
			}else {
				jQuery('.framework-main > .framework-group:not(.hide):first-child').animate({
					opacity: 'show',
					height: 'show'
				}, 200, function() {
					jQuery(this).removeClass('hide');
				});
				jQuery('.framework-admin-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
			}
		});
	});
	
	function framework_admin_add_file(event, selector) {
		
		var frame,
		$el = jQuery(this),
		framework_admin_upload,
		framework_admin_selector = selector;
		
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( framework_admin_upload ) {
			framework_admin_upload.open();
		} else {
			// Create the media frame.
			framework_admin_upload = wp.media.frames.framework_admin_upload = wp.media({
				// Set the title of the modal.
				title: $el.data('choose'),
				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: $el.data('update'),
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			framework_admin_upload.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = framework_admin_upload.state().get('selection').first();
				var attachment_attr = attachment.toJSON();
				var attr_width = framework_admin_selector.find('input[type="button"]').attr("data-width");
				var attr_height = framework_admin_selector.find('input[type="button"]').attr("data-height");
				framework_admin_upload.close();
				framework_admin_selector.find('.upload').val(attachment.attributes.url).change();
				
				if (attr_height !== undefined && attr_height !== false) {
					jQuery('#'+attr_height).val(attachment_attr.height);
				}
				if (attr_width !== undefined && attr_width !== false) {
					jQuery('#'+attr_width).val(attachment_attr.width);
				}
				
				if (framework_admin_selector.hasClass("upload-button-2")) {
					framework_admin_selector.parent().find('.upload').val(attachment.attributes.url);
					framework_admin_selector.parent().find('.image_id').val(attachment.attributes.id);
				}else if (framework_admin_selector.find(".image_id")) {
					framework_admin_selector.find('.image_id').val(attachment.attributes.id);
				}
				if (attachment.attributes.type == 'image') {
					framework_admin_selector.find('.screenshot').empty().removeClass("hide").hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image">'+theme_js_var.remove_image+'</a>').slideDown('fast');
				}
				framework_admin_selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(theme_js_var.remove_image);
				framework_admin_selector.find('.framework-background-properties').slideDown();
				framework_admin_selector.find('.remove-image, .remove-file').on('click', function() {
					framework_admin_remove_file(jQuery(this).parent().parent());
				});
			});

		}

		// Finally, open the modal.
		framework_admin_upload.open();
	}

	function framework_admin_remove_file(selector) {
		selector.find('.remove-image').hide();
		selector.find('.upload,.image_id').val('');
		selector.find('.framework-background-properties').hide();
		selector.find('.screenshot').animate({
			opacity: 'hide',
			height: 'hide'
		}, 200, function() {
			selector.find('.screenshot').addClass('hide');
		});
		selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(theme_js_var.upload_image).change();
		if ( jQuery('.section-upload .upload-notice').length ) {
			jQuery('.upload-button').remove();
		}
		selector.find('.upload-button').on('click', function(event) {
			framework_admin_add_file(event,jQuery(this).parent().parent());
		});
	}
		
	var update_form_elements = function(container,element = "") {
	
		container.find('.framework-form-text .framework-form-control,.framework-form-textarea .framework-form-control').keyup(function() {
			framework_form_change.call(this, jQuery(this).val());
		});
		
		container.find('.framework-form-select .framework-form-control,.framework-form-radio .framework-form-control,.framework-form-images .framework-form-control,.framework-form-checkbox .framework-form-control').change(function() {
			var ct = jQuery(this);
			var checked = [];
			if (ct.attr('type') == "checkbox") {
				if (ct.prop('checked')) {
					checked.push(ct.val());
				}
				checked = checked == "on" ? checked : false;
			}else {
				checked = jQuery(this).val();
			}
			framework_form_change.call(this, checked);
		});
		
		container.find('.section-multicheck_sort,.section-multicheck').each(function() {
			var pt = jQuery(this),
					lastChecked = null;
		
			jQuery(this).find('.framework-form-control').on('change', function(e) {
				var checked = [];
				pt.find('.framework-form-control').each(function() {
					var ct = jQuery(this);
					if (ct.prop('checked')) {
						checked.push(ct.val());
					}
				});
				checked = (checked.length > 0 ? checked : "no");
				framework_form_change.call(this, checked);
			});
		
			jQuery(this).find('li > .widget-head > label').on('click', function(e) {
				var t = jQuery(this).find('.framework-form-control');
				if (!lastChecked) {
					lastChecked = t;
					return;
				}
		
				if (e.shiftKey) {
					var curStart = t.parents('li').index(),
							curEnd = lastChecked.parents('li').index(),
							startIndex = Math.min(curStart, curEnd),
							endIndex = Math.max(curStart, curEnd) + 1,
							i;
		
					for (i = startIndex; i < endIndex; i++) {
						if (t.parents('li').index() != i) {
							pt.find('li').eq(i).find('.framework-form-control').prop('checked', lastChecked.prop('checked'));
						}
					}
				}
				lastChecked = t;
			});
		});
		
		jQuery(".framework-form-elements").find(element+' .framework-form-text .framework-form-control,'+element+' .framework-form-textarea .framework-form-control').each(function() {
			framework_form_change.call(this, jQuery(this).val());
		});
		
		jQuery(".framework-form-elements").find(element+' .framework-form-select .framework-form-control,'+element+' .framework-form-select_category .framework-form-control,'+element+' .framework-form-radio .framework-form-control,'+element+' .framework-form-images .framework-form-control,'+element+' .framework-form-checkbox .framework-form-control').each(function() {
			var ct = jQuery(this);
			var checked = [];
			if (ct.attr('type') == "checkbox") {
				if (ct.prop('checked')) {
					checked.push(ct.val());
				}
				checked = checked == "on" ? checked : false;
			}else {
				checked = jQuery(this).val();
			}
			framework_form_change.call(this, checked);
		});
		
		jQuery(".framework-form-elements").find(element+' .section-multicheck_sort,'+element+' .section-multicheck').each(function() {
			var pt = jQuery(this),
					lastChecked = null;
		
			var checked = [];
			pt.find('.framework-form-control').each(function() {
				var ct = jQuery(this);
				if (ct.prop('checked')) {
					checked.push(ct.val());
				}
			});
			checked = (checked.length > 0 ? checked : "no");
			framework_form_change.call(this, checked);
		});
		
		/* Image Options */
		container.find('.framework-radio-img-img').each(function () {
			var radio_img = jQuery(this);
			radio_img.parent().find('.framework-radio-img-label,.framework-radio-img-radio').hide();
			radio_img.show().click(function() {
				var radio_img = jQuery(this);
				radio_img.parent().parent().find('.framework-radio-img-img').removeClass('framework-radio-img-selected');
				radio_img.addClass('framework-radio-img-selected');
				radio_img.parent().find(".framework-radio-img-radio").trigger("click").attr('checked','checked');
				framework_form_change.call(this, radio_img.attr("value"));
			});
		});
		
		/* form element: colorpicker */
		container.find('.framework-form-color .framework-color,.framework-form-typography .framework-color,.framework-form-background .framework-color').each(function() {
			var t = this,
				is_set = jQuery(t).hasClass('wp-color-picker');

			if (is_set || jQuery(t).closest("#available-widgets").length) {
				return;
			}
			jQuery(t).wpColorPicker();
		});
		
		/* form element: datepicker */
		container.find('.framework-form-date .framework-date,.site-form-date .site-date').each(function() {
			var t = this,
				is_set = jQuery(t).hasClass('hasDatepicker');

			if (is_set || jQuery(t).closest("#available-widgets").length) {
				return;
			}
			jQuery(t).datepicker(jQuery(t).data('js'));
		});
		
		/* form element: sort sections */
		container.find('.section .sort-sections').each(function() {
			var t = this;

			if (!jQuery(t).hasClass("not-sort")) {
				jQuery(t).sortable({
					placeholder: "ui-state-highlight",
					handle: ".widget-head,.widget-handle",
					cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item,.switch,.not-sort .widget-handle,.not-sort .del-builder-item"
				});
			}
		});
		
		/* form element: multicheck */
		container.find('.framework-form-multicheck_category .widget-switch').each(function() {
			var t = this,
				is_set = jQuery(t).hasClass('widget-switch-already'),
				checkbox_attr = jQuery(t).find(" > input").attr("id"),
				checkbox_for = "";

			if (is_set) {
				return;
			}

			if (checkbox_attr !== undefined && checkbox_attr !== false) {
				checkbox_for = " for='"+checkbox_attr+"'";
			}

			jQuery(t).addClass("widget-switch-already").attr("for",checkbox_attr).find(" > input").after("<label"+checkbox_for+" data-on='ON' data-off='OFF'></label>");
		});

		/* form element: sliderui */
		container.find('.framework-form-sliderui .v_sliderui,.framework-form-slider .v_sliderui').each(function() {
			var t = this,
			sId = "#" + jQuery(t).data('id'),
				to,
				d = {
					range : "min",
					value : parseInt(jQuery(t).data('val')),
					min   : parseInt(jQuery(t).data('min')),
					max   : parseInt(jQuery(t).data('max')),
					step  : parseInt(jQuery(t).data('step')),
					slide: function(e, ui) {
						if(typeof to != 'undefined') {
							clearTimeout(to);
						}
						jQuery(sId).val( ui.value );
						to = setTimeout(function() {
							framework_form_change.call(t, ui.value);
						}, 400);
					}
				},
				i;

			for(i in d) {
				if(typeof jQuery(t).data(i) != 'undefined') {
					d[i] = jQuery(t).data(i);
				}
			}

			jQuery(t).slider(d);
		});
		
		/* form element: upload */
		container.find('.framework-form-upload .form-upload-images,.framework-form-background .form-upload-images').each(function() {
			var t = this;
			jQuery(t).find('.remove-image,.remove-file').on('click', function() {
				framework_admin_remove_file(jQuery(this).parent().parent());
			});
		
			jQuery(t).find('.upload-button').on("click", function( event ) {
				framework_admin_add_file(event,jQuery(this).parent().parent());
			});
			
			jQuery(t).find('.upload-button-2').on( "click", function( event ) {
				framework_admin_add_file(event,jQuery(this).parent().parent());
			});
		});

		/* form element: code */
		container.find('.framework-code-editor').each(function() {
			var t = this,
				id = jQuery(t).attr('id'),
				name = jQuery(t).data('name'),
				mode = jQuery(t).data('mode'),
				theme = jQuery(t).data('theme'),
				el_holder = jQuery(t).siblings('textarea[name="'+name+'"]');

			if(!el_holder.length) {
				el_holder = jQuery('<textarea>').attr({name:name}).addClass('framework-form-control').hide();
				jQuery(t).before(el_holder);
			}

			var framework_editor = framework.edit(id);
			framework_editor.setTheme(theme_js_var.framework_name+'/theme/'+ theme);
			framework_editor.getSession().setMode(theme_js_var.framework_name+'/mode/'+ mode);

			framework_editor.on('change', function(ev, editor) {
				var v = editor.getValue();
				el_holder.text(v);
				framework_form_change.call(t, v);
			});
		});
		
		/* fix wp_editor on ajax call */
		container.find('.framework-form-wp-editor').each(function() {

			var t = jQuery(this),
				p = t.parents('.framework-form-block'),
				p_is_hidden = p.hasClass('framework_hide'),
				id = t.find('.wp-editor-area').attr('id'),
				preloaded_wp_editor_id = 'framework_preloaded_editor_id';

			if ( typeof id == 'undefined'
				|| typeof quicktags == 'undefined'
				|| typeof tinyMCEPreInit != 'object'
				|| typeof tinyMCEPreInit.mceInit != 'object'
				|| ! tinyMCEPreInit.mceInit.hasOwnProperty(preloaded_wp_editor_id) ) {
				return;
			}

			p.removeClass('framework_hide');

			var mceinit_params = jQuery.extend(true, {}, tinyMCEPreInit.mceInit[preloaded_wp_editor_id]);

			mceinit_params.selector = '#'+id;
			mceinit_params.resize = true;
			mceinit_params.toolbar1 = mceinit_params.toolbar1.replace( /(fullscreen)/g, '' );
			mceinit_params.toolbar2 = mceinit_params.toolbar2.replace( /(wp_help)/g, '' );

			if (mceinit_params.hasOwnProperty('body_class')) {
				mceinit_params.body_class = mceinit_params.body_class.replace(preloaded_wp_editor_id, id);
			}

			tinyMCE.init(mceinit_params);
			tinyMCE.execCommand('mceAddEditor', false, id);
			quicktags({id : id});

			if (p_is_hidden) {
				p.addClass('framework_hide');
			}

		});
			
		return container;
	};

	jQuery(document).on('widget-updated widget-added',function(event, widget) {
		update_form_elements(widget);
	});
		
	jQuery(document).ajaxSuccess(function(e, xhr, settings) {
		var getParameterByName = function(name, url) {
			name = name.replace(/[\[\]]/g, "\\$&");
			var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
				results = regex.exec(url);
			if (!results) return null;
			if (!results[2]) return '';
			return decodeURIComponent(results[2].replace(/\+/g, " "));
		},
		action = getParameterByName( 'action', settings.data );

		if (action == 'save-widget') {
			var widget_id = getParameterByName( 'widget-id', settings.data ),
				sidebar = getParameterByName( 'sidebar', settings.data );

			jQuery('#'+ sidebar +' .widget').each(function() {
				var t = jQuery(this),
					id = t.attr('id');

				if (id.indexOf(widget_id) != -1) {
					update_form_elements(t);
				}
			});
		}
	});
	
	/* Condition */
	var objectValues = function(obj) {
		var arr = [],
				o;
	
		for (o in obj) {
			arr.push(obj[o]);
		}
		return arr;
	};
	
	get_form_data = function(container) {
		var form_data = {},
				i;
	
		container.find('.section,.wrap_class,.options-group').each(function() {
			var t = jQuery(this),
					id = t.attr('data-id'),
					type = t.attr('data-type'),
					fc = t.find('.framework-form-control');
			if (fc.length == 0) {
				return;
			}
	
			form_data[id] = {};
			var checked = [];
	
			if (fc.length > 1) {
				fc.each(function() {
					var it = jQuery(this),
							i = it.attr('value');
	
					if (typeof i != 'undefined') {
						if (type != 'checkbox' || it.prop('checked')) {
							if (it.prop('checked')) {
								checked.push(it.val());
								form_data[id] = checked;
							}else {
								form_data[id][i] = it.val();
							}
						}
					}
				});
			}else if (fc.length == 1) {
				if (type == 'checkbox') {
					if (fc.prop('checked')) {
						checked.push(fc.val());
					}
					checked = checked == "on" ? checked : false;
					form_data[id] = checked;
				}else {
					form_data[id] = fc.val();
				}
				if (type == 'wp_editor' && typeof(tinyMCE) != 'undefined') {
					var ed = tinyMCE.editors[fc.attr('id')];
					if (typeof ed != 'undefined') {
						form_data[id] = ed.getContent();
					}
				}
			}
		});
		return form_data;
	}
	
	framework_form_change = function(value) {
		//set_data_changed();
		var el = jQuery(this),
				el_form_block = el.parents('.section,.wrap_class,.options-group');
	
		if (!el_form_block.length) {
			return;
		}
		var id = el_form_block.attr('data-id');
		if (typeof id == 'undefined') {
			return;
		}
		var el_container = el.parents('.framework-main');
		var form_data = get_form_data(el_container);
		el_container.find('.section,.wrap_class,.options-group').each(function() {
	
			var t = jQuery(this),
					condition = t.attr('data-condition'),
					operator = t.attr('data-operator');
	
			if (typeof condition == 'undefined') {
				return;
			}
	
			if (typeof operator == 'undefined' || ['and', 'or'].indexOf(operator) == -1) {
				operator = 'and';
			}
	
			var bool_arr = [],
					cond_arr = condition.split('),'),
					i;
	
			for (i in cond_arr) {
				if (cond_arr[i].slice(-1) != ')') {
					cond_arr[i] += ')';
				}
	
				var m = cond_arr[i].match(/^([a-z0-9_-]+)\:(not|is|has|has_not)\(([a-z0-9-_\,]+)\)$/i),
						m_bool = false;
				
				if (m != null) {
					var m_id = m[1],
							m_op = m[2],
							m_val = m[3];
					if (!form_data.hasOwnProperty(m_id)) {
						form_data[m_id] = '';
					}
					
					if (['is', 'not'].indexOf(m_op) != -1) {
						if (m_val == "empty" && (m_op == 'not' || m_op == 'is')) {
							if (m_op == 'not') {
								m_bool = (form_data[m_id] != "");
							}else {
								m_bool = (form_data[m_id] == "");
							}
						}else {
							m_bool = (form_data[m_id] == m_val);
							if (m_op == 'not') {
								m_bool = !m_bool;
							}
						}
					}else if (['has', 'has_not'].indexOf(m_op) != -1) {
						if (typeof form_data[m_id] == 'string') {
							form_data[m_id] = form_data[m_id].split(',');
						}else if (typeof form_data[m_id] != 'object') {
							form_data[m_id] = [];
						}else if (!(form_data[m_id] instanceof Array)) {
							form_data[m_id] = [];
						}
						m_val = m_val.split(',');
						var j, k = [];
						for (j in m_val) {
							if (m_val != 0) {
								k.push(form_data[m_id].indexOf(m_val[j]) != -1);
							}
						}
						m_bool = (k.indexOf(false) == -1);
						if (m_op == 'has_not') {
							m_bool = !m_bool;
						}
					}
				}
				bool_arr.push(m_bool);
			}
	
			var is_hidden = false;
			if (operator == 'or') {
				is_hidden = (bool_arr.indexOf(true) == -1);
			}else {
				is_hidden = (bool_arr.indexOf(false) != -1);
			}
	
			if (is_hidden) {
				t.animate({
					opacity: 'hide',
					height: 'hide'
				}, 200, function() {
					t.addClass('hide');
				});
			}else {
				t.animate({
					opacity: 'show',
					height: 'show'
				}, 200, function() {
					t.removeClass('hide');
				});
			}
		});
	}
	
	/*
	jQuery('body').on('click', '.framework-form-control', function(e) {
		e.preventDefault();
		var t = jQuery(this),
		v = t.data('value'),
		c = 'active';
	
		if(typeof v == 'undefined') {
			return;
		}
	
		t.addClass(c).siblings('li').removeClass(c);
		t.parents('ul').siblings('.framework-form-control').val(v);
		framework_form_change.call(this, v);
	});
	*/
	
	/* Add a new element */
	jQuery(document).on("click",".framework-main .add_element",function () {
		var add_element = jQuery(this);
		if (!add_element.hasClass("not_add_element")) {
			var theme_options = add_element.attr("data-options");
			var framework_theme_var = theme_options;
			var data_id	= add_element.attr("data-id");
			var data_name = add_element.attr("data-name");
			var data_widget_id	= add_element.attr("data-widget-id");
			var data_widget	= add_element.attr("data-widget");
			var data_first_widget	= add_element.attr("data-first-widget");
			var data_add_to = add_element.parent().find(" > .all_elements > ul").attr("data-to");
			var data_id_name = "["+data_id+"]";
			var data_add_to_name = "["+data_add_to+"]";
			var no_theme_options = add_element.attr("data-class");
			if (no_theme_options !== undefined && no_theme_options !== false) {
				add_element.addClass("no_theme_options");
			}
			
			if (add_element.hasClass("no_theme_options")) {
				var framework_theme_var = "";
				var data_id_name = data_id;
				var data_add_to_name = data_add_to;
			}
			
			var data_title  = add_element.attr("data-title");
			if (data_add_to !== undefined && data_add_to !== false) {
				var add_element_j = jQuery("#"+data_add_to+" li").length;
				add_element_j++;
				var data_add_to_id = data_add_to;
			}else {
				var add_element_j = add_element.parent().find("."+data_id+"_j").attr("data-js");
				var data_add_to_id = data_id;
			}

			var element_id = "elements_"+data_add_to_id+"_"+add_element_j;
			var i_count = 1;
			while (i_count < add_element_j) {
				if (jQuery("#"+element_id).length) {
					add_element_j++;
					element_id = "elements_"+data_add_to_id+"_"+add_element_j;
				}
				i_count++;
			}
			
			add_element.parent().find(" > .all_elements > ul > li").clone().attr("id",element_id).appendTo('#'+data_add_to_id);
			jQuery("html,body").animate({scrollTop: jQuery("#"+element_id).offset().top-35},"slow");
			
			if (data_title !== undefined && data_title !== false) {
				jQuery("#"+element_id+" .del-builder-item,#"+element_id+" a.widget-handle").wrapAll("<div class='widget-head' />");
				jQuery("#"+element_id+" > div:not(.widget-content)").prepend(jQuery(add_element.parent().find(" > .all_elements > ul > li input[data-title='"+data_title+"']")).val());
			}
			
			jQuery("#"+element_id+" .section,#"+element_id+" .wrap_class").each(function () {
				var this_each = jQuery(this);
				var this_attr = this_each.attr("data-attr");
				if (!this_each.hasClass("wrap_class")) {
					if (data_add_to !== undefined && data_add_to !== false) {
						var last_id = framework_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr;
					}else {
						var last_id = framework_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
					}
				}

				var condition = this_each.attr("data-condition");
				if (condition !== undefined && condition !== false) {
					this_each.attr("data-condition",condition.split("[%id%]").join(framework_theme_var+"_"+data_add_to_id+"_"+add_element_j+"_"));
				}

				if (!this_each.hasClass("wrap_class")) {
					this_each.attr("data-id",last_id).attr("id","section-"+last_id);
					
					if (this_each.find("div.v_slidersui").length) {
						this_each.find("div.v_slidersui").attr("id",last_id+"-slider").attr("data-id",last_id).addClass('v_sliderui').removeClass('v_slidersui');
					}
				}
			});
			
			jQuery("#"+element_id+" .widget-content select").each(function () {
				var this_each = jQuery(this);
				var this_attr = (this_each.hasClass("check-parent-class")?this_each.parent().attr("data-attr"):this_each.attr("data-attr"));
				if (data_widget !== undefined && data_widget !== false) {
					var last_id   = framework_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
					var last_name = data_widget+"["+add_element_j+"]["+this_attr+"]";
				}else if (data_add_to !== undefined && data_add_to !== false) {
					var last_id   = framework_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr;
					var last_name = framework_theme_var+data_add_to_name+"["+add_element_j+"]["+this_attr+"]";
				}else {
					var last_id   = framework_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
					var last_name = framework_theme_var+(data_name !== undefined && data_name !== false?data_name:data_id_name)+"["+add_element_j+"]["+this_attr+"]";
				}
				this_each.attr("name",last_name).attr("id",last_id);
			});
			
			jQuery("#"+element_id+" .widget-content input,#"+element_id+" .widget-content textarea").each(function () {
				var this_each = jQuery(this);
				var this_attr = this_each.attr("data-attr");
				var this_multicheck = this_each.attr("data-multicheck");
				if (data_widget !== undefined && data_widget !== false) {
					var last_id   = framework_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr+(typeof(this_multicheck) !== "undefined"?"-"+this_multicheck:"");
					var last_name = data_widget+"["+add_element_j+"]["+this_attr+"]"+(typeof(this_multicheck) !== "undefined"?"["+this_multicheck+"]":"");
				}else if (data_add_to !== undefined && data_add_to !== false) {
					var last_id   = framework_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr+(typeof(this_multicheck) !== "undefined"?"-"+this_multicheck:"");
					var last_name = framework_theme_var+data_add_to_name+"["+add_element_j+"]["+this_attr+"]"+(typeof(this_multicheck) !== "undefined"?"["+this_multicheck+"]":"");
				}else {
					var last_id   = framework_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr+(typeof(this_multicheck) !== "undefined"?"-"+this_multicheck:"");
					var last_name = framework_theme_var+(data_name !== undefined && data_name !== false?data_name:data_id_name)+"["+add_element_j+"]["+this_attr+"]"+(typeof(this_multicheck) !== "undefined"?"["+this_multicheck+"]":"");
				}
				this_each.not('.not-increase').attr("id",last_id);
				this_each.not('[type="button"]').attr("name",last_name);
				if (this_each.is('.add_element')) {
					this_each.attr("id",last_id.replace(framework_theme_var+"_",""));
				}
				if (this_each.is('[type="hidden"]')) {
					this_each.not(".not-increase").val(add_element_j);
				}
				if (this_each.is('[type="radio"]')) {
					this_each.attr("id",last_id+"_"+this_each.attr("value")).next("label").attr("for",last_id+"_"+this_each.attr("value"));
				}
				if (this_each.is('[type="checkbox"]')) {
					var this_switch = this_each.closest(".switch");
					this_switch.attr("for",last_id).find(" > label").attr("for",last_id);
					this_switch.parent().find(" > label").attr("for",last_id);
				}
				if (this_each.is('[data-type="uniq_id"]')) {
					this_each.val(add_element_j);
				}
			});

			jQuery("#"+element_id+" .section-elements").each(function () {
				var this_each = jQuery(this);
				var this_attr = (this_each.hasClass("check-parent-class")?this_each.parent().attr("data-attr"):this_each.attr("data-attr"));
				var add_element_button = this_each.find(" > .all-option > .add_element");
				var no_theme_options = add_element_button.attr("data-class");
				var add_element_first = add_element_button.attr("data-first");
				var data_first_name = "["+add_element_first+"]";
				if (add_element.hasClass("no_theme_options")) {
					var data_first_name = add_element_first;
				}
				var add_element_second = add_element_button.attr("data-second");
				var data_id_name = "["+add_element_second+"]";
				if (no_theme_options !== undefined && no_theme_options !== false) {
					add_element_button.addClass("no_theme_options");
				}
				if (add_element_button.hasClass("no_theme_options")) {
					var data_id_name = add_element_second;
				}
				add_element_button.addClass("add_element_elements").attr("data-id",add_element_button.attr("id")).attr("data-id",add_element_button.attr("id")).attr("data-name",data_first_name+"["+add_element_j+"]["+add_element_second+"]");
				add_element_button.parent().find(" > .elements_elements_js").attr("class","elements_elements_js "+add_element_button.attr("id")+"_j");
				add_element_button.parent().find(" > .sort-sections-elements").attr("id",add_element_button.attr("id"));
				add_element_button.parent().find(".all_elements *[name]").attr("name","");
				if (data_widget !== undefined && data_widget !== false) {
					add_element_button.attr("data-widget",data_widget_id+"["+data_first_widget+"]["+add_element_j+"]["+add_element_second+"]");
				}
			});
			
			update_form_elements(jQuery('#'+element_id),'#'+element_id);
			if (data_widget !== undefined && data_widget !== false) {
				jQuery('.widget #'+element_id).find("input").change();
			}
			
			if (data_add_to !== undefined && data_add_to !== false) {
				jQuery("#"+element_id).append('<input name="'+framework_theme_var+'['+data_add_to+']['+add_element_j+'][getthe]" value="'+data_add_to+'" type="hidden">');
			}
			if (!add_element.parent().find(" > .all_elements > ul > li input").is(':radio') && !add_element.parent().find(" > .all_elements > ul > li input").is(':checkbox') && !add_element.parent().find(" > .all_elements > ul > li input.upload_image_button,.upload_image_button_m")) {
				add_element.parent().find(" > .all_elements > ul > li input.framework-form-not-std").val("");
			}
			if (!add_element.parent().find(" > .all_elements > ul > li textarea")) {
				add_element.parent().find(" > .all_elements > ul > li textarea.framework-form-not-std").val("");
			}
			add_element_j++;
			add_element.parent().find("."+data_id+"_j").attr("data-js",add_element_j);
			
			jQuery('#'+element_id+' .framework-colors').wpColorPicker();

			jQuery('#'+element_id+' .elements-custom-menu select').select2({
				width: '100%',
			});
			
			var attr_js = jQuery('#'+element_id+' .builder-datepicker').data('js');
			jQuery('#'+element_id+' .builder-datepicker').removeClass("builder-datepicker").removeClass("hasDatepicker").addClass("framework-datepicker").datepicker((attr_js !== undefined && attr_js !== false?attr_js:{}));
			jQuery("#"+element_id).closest("ul").removeClass("sort-sections-empty");
		}
	});
	
	/* Meta box */
	if (jQuery("#post_type").length) {
		var post_type = jQuery("#post_type").val();
		if (post_type == theme_js_var.question || post_type == theme_js_var.asked_question) {
			jQuery("#commentsdiv > h2 > span").text(theme_js_var.answers);
			jQuery("#commentsdiv #add-new-comment a").text(theme_js_var.add_answer_button);
			jQuery("#commentsdiv #no-comments").text(theme_js_var.no_answers);
		}
	}
	
	if (jQuery('.framework-typography-face').length) {
		jQuery('.framework-typography-face').fontselect();
	}
	
	/* Datepicker */
	jQuery('.framework-datepicker,.builder-datepicker').each(function () {
		var this_date = jQuery(this);
		var attr_js = this_date.data('js');
		this_date.datepicker((attr_js !== undefined && attr_js !== false?attr_js:{}));
	});
	
	if (jQuery('#framework-admin-wrap select[multiple],.framework-custom-menu select,.elements-custom-menu select').length) {
		jQuery('#framework-admin-wrap select[multiple],.framework-custom-menu select,.elements-custom-menu select').select2({
			width: '100%',
		});
	}
	
	if (jQuery('.tooltip_n').length) {
		jQuery('.tooltip_n').tipsy({gravity: 'n'});
	}
	
	if (jQuery('.tooltip_s').length) {
		jQuery('.tooltip_s').tipsy({gravity: 's'});
	}
	
	update_form_elements(jQuery('body'));

	/* Loads tabbed sections if they exist */
	if ( jQuery('#framework_meta_tabs .framework-admin-content .nav-tab-wrapper').length ) {
		framework_admin_tabs("meta");
	}else if ( jQuery('.framework-admin-content .nav-tab-wrapper').length ) {
		framework_admin_tabs(jQuery("#framework-admin-settings").val());
	}

	function framework_admin_tabs(page = '') {
		// Hides all the .framework-group sections to start
		jQuery('.framework-group').hide();
		
		// Find if a selected tab is saved in localStorage
		var framework_v = '';
		var framework_option = theme_js_var.framework_name+"_"+page;
		if ( typeof(localStorage) != 'undefined' ) {
			framework_v = localStorage.getItem(framework_option);
		}

		// If active tab is saved and exists, load it's .framework-group
		if (framework_v != '' && jQuery(framework_v).length && !jQuery(framework_v + '-tab').hasClass('hide') && !jQuery(framework_v + '-tab').hasClass('custom-link') ) {
			jQuery(framework_v).animate({
				opacity: 'show',
				height: 'show'
			}, 200, function() {
				jQuery(this).removeClass('hide');
				if (jQuery(this).find(".framework_tabs").length == 0) {
					jQuery(this).find(".main-head").slideDown(300);
				}
			});
			jQuery(framework_v + '-tab').addClass('nav-tab-active');
		}else {
			jQuery('.framework-group:not(.hide):first-child').animate({
				opacity: 'show',
				height: 'show'
			}, 200, function() {
				jQuery(this).removeClass('hide');
				if (jQuery(this).find(".framework_tabs").length == 0) {
					jQuery(this).find(".main-head").slideDown(300);
				}
			});
			jQuery('.framework-admin-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
		}
		
		// Bind tabs clicks
		jQuery(document).on('click','.framework-admin-content .nav-tab-wrapper a',function() {
			var nav_tab = jQuery(this);
			if (!nav_tab.hasClass("custom-link")) {
				// Remove active class from all tabs
				jQuery('.framework-admin-content .nav-tab-wrapper a').removeClass('nav-tab-active');

				nav_tab.addClass('nav-tab-active').blur();

				var group = nav_tab.attr('href');

				if (typeof(localStorage) != 'undefined' ) {
					localStorage.setItem(framework_option, nav_tab.attr('href') );
				}

				jQuery('.framework-group').hide();
				jQuery(group).animate({
					opacity: 'show',
					height: 'show'
				}, 200, function() {
					jQuery(this).removeClass('hide');
					if (jQuery(this).find(".framework_tabs").length == 0) {
						jQuery(this).find(".main-head").slideDown(300);
					}
				});

				// Editor height sometimes needs adjustment when unhidden
				jQuery('.wp-editor-wrap').each(function() {
					var editor_iframe = jQuery(this).find('iframe');
					if ( editor_iframe.height() < 30 ) {
						editor_iframe.css({'height':'auto'});
					}
				});
				return false;
			}
		});
		
		/* Framework tabs */
		var framework_tab_value = '';
		if (typeof(localStorage) != 'undefined') {
			framework_tab_value = localStorage.getItem(framework_option+'_tab_value');
		}
		if (framework_tab_value != '' && jQuery(".framework_tabs a[href='"+framework_tab_value+"']").length) {
			jQuery(".framework_tabs a[href='"+framework_tab_value+"']").closest(".framework-group").find(".head-group").hide(10);
			if (jQuery(".framework_tabs a[href='"+framework_tab_value+"']").closest(".framework-group").find("#"+framework_tab_value+",."+framework_tab_value).length) {
				jQuery(".framework_tabs a[href='"+framework_tab_value+"']").addClass("framework_active").closest(".framework-group").find("#"+framework_tab_value+",."+framework_tab_value+",#main-"+framework_tab_value).show(10).slideDown(300);
			}else {
				jQuery(framework_v).find(".framework_tabs > li:first-child a").addClass("framework_active").trigger("click");
				jQuery(jQuery(framework_v).find(".framework_tabs > li:first-child a").attr("href")).show(10).slideDown(300);
			}
		}else {
			jQuery(framework_v).find(".framework_tabs > li:first-child a").addClass("framework_active").trigger("click");
		}
		
		jQuery('.framework_tabs a').click(function(evt) {
			evt.preventDefault();
			var framework_tab_a = jQuery(this);
			var framework_tab_href = framework_tab_a.attr('href');
			framework_tab_a.closest(".framework-group").find(".head-group").hide(10).closest(".main-head").hide(10);
			framework_tab_a.parent().parent().find(".framework_active").removeClass("framework_active");
			framework_tab_a.addClass("framework_active").closest(".framework-group").find("#"+framework_tab_href+",."+framework_tab_href+",#main-"+framework_tab_href).show(10).slideDown(300);
			if (typeof(localStorage) != 'undefined') {
				localStorage.setItem(framework_option+'_tab_value', framework_tab_href);
			}
		});
		
		jQuery(".framework_tabs").each(function () {
			var data_std = jQuery(this).attr("data-std");
			if (data_std != jQuery(".framework_tabs a[href='"+framework_tab_value+"']").parent().parent().attr("data-std")) {
				jQuery(".framework_tabs a[href='"+data_std+"']").addClass("framework_active");
				jQuery("#"+data_std+",."+data_std+",#main-"+data_std).show(10).slideDown(300);
			}
		});
	}
	
	/* Save options */
	if (jQuery('input.framework_save').length) {
		jQuery("input.framework_save").click(function() {
			var typingTimer;
			var framework_save = jQuery(this);
			var option_settings = framework_save.closest("#main_options_form").find("#framework-admin-settings").val();
			jQuery("#ajax-saving").fadeIn("slow");
			jQuery("#loading").show();
			if (jQuery(".framework_editor").length && typeof(tinyMCE) != 'undefined' && tinyMCE !== undefined && tinyMCE !== false) {
				tinyMCE.triggerSave();
			}
			
			var $data = jQuery('#main_options_form').serialize();
			jQuery('#main_options_form').find('input[type=checkbox]').each(function() {
				if ( typeof $( this ).attr( 'name' ) !== "undefined" ) {
					var chkVal = $( this ).is( ':checked' ) ? $( this ).val() : "0";
					$data += "&" + $( this ).attr( 'name' ) + "=" + chkVal;
				}
			});
			var import_setting = jQuery("#import_setting").val();
			if (import_setting !== undefined && import_setting != "") {
				var saving_nonce = jQuery("#saving_nonce").val();
				jQuery.ajax({
					type: "POST",
					url: theme_js_var.ajax_a,
					data: {
						action: "wpqa_import_options",
						saving_nonce: saving_nonce,
						data: import_setting
					},
					success: function (results) {
						jQuery(".framework_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},500);
						jQuery("#import_setting").val("");
						if (results == 3) {
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}else {
							location.reload();
						}
					},error: function (jqXHR, textStatus, errorThrown) {
						// Error
					},complete: function () {
						// Done
					}
				});
			}else {
				jQuery.ajax({
					type: "POST",
					url: theme_js_var.ajax_a,
					data: {
						action: "wpqa_update_options",
						option_settings: option_settings,
						data: $data
					},
					cache: false,
					dataType: "json",
					success: function (results) {
						jQuery(".framework_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},500);
						if (results == 2) {
							location.reload();
						}
						if (results == 3) {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}
					},error: function (jqXHR, textStatus, errorThrown) {
						// Error
					},complete: function (results) {
						results = (typeof(results) !== 'undefined' && results.hasOwnProperty('responseJSON')?results.responseJSON:results);
						jQuery(".framework_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},500);
						if (results == 2) {
							location.reload();
						}
						if (results == 3) {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}
					}
				});
			}
			return false;
		});
	}
	
	/* Reset options */
	if (jQuery('#reset_c').length) {
		jQuery("#reset_c").click(function() {
			var answer = confirm(theme_js_var.confirm_reset);
			if (answer) {
				jQuery("#ajax-reset").fadeIn("slow");
				var typingTimer;
				var saving_nonce = jQuery("#saving_nonce").val();
				var option_settings = saving_nonce.closest("#main_options_form").find("#framework-admin-settings").val();
				var defaults = "&action=wpqa_reset_options&saving_nonce="+saving_nonce+"&option_settings="+option_settings;
				jQuery.post(theme_js_var.ajax_a,defaults,function (results) {
					jQuery("#reset_c").blur();
					if (results == 3) {
						jQuery("#ajax-reset").fadeOut("slow");
						jQuery("#ajax-load").fadeIn("slow");
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-load").fadeOut("slow");
							location.reload();
						},3000);
					}else {
						setTimeout(function() {
							jQuery("#ajax-reset").fadeOut("slow");
							location.reload();
						},200);
					}
				});
			}
			return false;
		});
	}
	
	/* Live search at the admin setting */
	if (jQuery(".framework_search").length) {
		var live_search = jQuery(".framework_search input");
		var typingTimer;
		live_search.on("keyup",function() {
			live_search = jQuery(this);
			var search_value = live_search.val();
			var option_settings = live_search.closest("#main_options_form").find("#framework-admin-settings").val();
			if (search_value == "") {
				live_search.parent().find(".search-results").addClass("results-empty").html("").hide();
			}else {
				var search_loader = live_search.parent().find(".search_loader");
				clearTimeout(typingTimer);
				typingTimer = setTimeout(function () {
					search_loader.show(10);
					jQuery(".section-search-hide").removeClass("section-search-hide").addClass("hide");
					jQuery(".search-select").removeClass("search-select");
					jQuery.ajax({
						url: theme_js_var.ajax_a,
						type: "POST",
						data: { action : 'wpqa_admin_live_search',search_value : search_value,option_settings : option_settings },
						success:function(data) {
							live_search.parent().find(".search-results").removeClass("results-empty").html(data).slideDown(300);
							search_loader.hide(10);
						}
					});
				},500);
			}
		});
		
		live_search.on('focus',function() {
			var live_search  = jQuery(this);
			if (live_search.parent().find(".results-empty").length == 0) {
				live_search.parent().find(".search-results").show();
			}
		});
		
		live_search.parent().on('click','a',function() {
			var thisElem  = jQuery(this),
			itemId        = thisElem.attr('href'),
			thistextid    = jQuery("#"+itemId).closest('.framework-group'),
			thisparent    = jQuery("#"+itemId).closest('.options-group'),
			thiscondition = jQuery("#"+itemId).closest('.hide[data-condition]');
			
			jQuery(".section-search-hide").removeClass("section-search-hide").addClass("hide");
			jQuery(".search-select").removeClass("search-select");
			
			jQuery('#'+thistextid.attr("id")+'-tab').addClass("nav-tab-active").trigger("click");
			jQuery(".framework_tabs").find("a[href='#"+thisparent.attr("id")+"']").trigger("click");
			jQuery("#"+itemId).addClass("search-select");
			if (jQuery("#"+itemId).hasClass("hide")) {
				jQuery("#"+itemId).removeClass("hide").addClass("section-search-hide");
			}
			if (thiscondition.hasClass("hide")) {
				thiscondition.removeClass("hide").addClass("section-search-hide");
			}
			jQuery("html,body").animate({scrollTop: jQuery("#"+itemId).offset().top-35},"slow");
			return false;
		});
		
		var outputContainer = live_search.parent().find('.search-results');
		var input           = live_search.get(0);
		jQuery('body').on('click', function(e) {
			if (!jQuery.contains(outputContainer.get(0), e.target) && e.target != input) {
				outputContainer.hide();
			}
		});
	}
});