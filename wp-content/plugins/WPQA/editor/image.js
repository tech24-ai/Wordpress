function wpqa_upload_file(inp,editor) {
	var input = inp.get(0);
	var data = new FormData();
	data.append('image[file]',input.files[0]);
	data.append('action','wpqa_editor_upload_image');
	var editor_element = jQuery(editor.getElement()).parent();
	var editor_wrap = editor_element.closest(".wp-editor-wrap");
	editor_wrap.append('<span class="load_span"><span class="loader_2"></span></span>');
	editor_wrap.find(".load_span").show(10);
	wp.media.model.settings.post.id = 0;

	jQuery.ajax({
		url: (typeof(wpqa_custom) !== 'undefined'?wpqa_custom.admin_url:theme_js_var.ajax_a),
		type: 'POST',
		data: data,
		dataType: "JSON",
		processData: false,
		contentType: false,
		success: function(result,textStatus,jqXHR) {
			if (result.success == 0) {
				if (editor_wrap.find(".wpqa_error").length == 0) {
					editor_wrap.prepend('<div class="wpqa_error"><span class="required-error">'+result.error+'</span></div>');
				}else {
					editor_wrap.find(".wpqa_error .required-error"),html(result.error);
				}
				editor_wrap.find(".wpqa_error").animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
			}else {
				editor.insertContent('<img class="content-img" src="'+result.success+'"/>');
			}
			editor_wrap.find(".load_span,.wpqa_error").hide(10);
		},
		error: function(jqXHR,textStatus,errorThrown) {
			if (jqXHR.responseText) {
				errors = JSON.parse(jqXHR.responseText).errors;
				if (editor_wrap.find(".wpqa_error").length == 0) {
					editor_wrap.prepend('<div class="wpqa_error"><span class="required-error">'+(typeof(wpqa_custom) !== 'undefined'?wpqa_custom.error_uploading_image:theme_js_var.error_uploading_image)+'</span></div>');
				}else {
					editor_wrap.find(".wpqa_error .required-error"),html((typeof(wpqa_custom) !== 'undefined'?wpqa_custom.error_uploading_image:theme_js_var.error_uploading_image));
				}
				editor_wrap.find(".wpqa_error").animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
			}
		}
	});
}

(function() {
	if (typeof(wpqa_custom) !== 'undefined' || typeof(theme_js_var) !== 'undefined') {
		tinymce.create('tinymce.plugins.WPQA',{
			init : function(editor,url) {
				var editor_element = jQuery(editor.getElement()).parent();
				if (editor_element.find(".tinymce-uploader").length == 0) {
					var inp = jQuery('<input class="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
				}
				editor_element.append(inp);

				editor.addButton('wpqa_custom_image',{
					title : (typeof(wpqa_custom) !== 'undefined'?wpqa_custom.insert_image:theme_js_var.insert_image),
					cmd : 'wpqa_custom_image',
					image : url+'/image.png',
				});

				inp.on("change",function(e) {
					wpqa_upload_file(jQuery(this),editor);
				});

				editor.addCommand('wpqa_custom_image',function() {
					inp.click();
				});
			},
		});
		tinymce.PluginManager.add('WPQA',tinymce.plugins.WPQA);
	}
})();