(function($) { "use strict";
	
	/* Rate */

	if (jQuery(".rate-article").length) {
		jQuery(document).on("click",".rate-article-link",function () {
			var rate = jQuery(this);
			var type = rate.data("type");
			var post_id = rate.data("post");
			var rate_parent = rate.parent();
			rate_parent.find(".rate-article-link").hide();
			rate_parent.parent().find(".small_loader").show().css({"display":"inline-block"});
			jQuery.ajax({
				type: "POST",
				url: wpqa_rate.admin_url,
				data: {action:"wpqa_rate",post_id:post_id,type:type},
				cache: false,
				dataType: "json",
				success: function (data) {
					var up_count = (typeof(data) !== 'undefined' && data.hasOwnProperty('up_count') && data.up_count > 0?data.up_count:0);
					var down_count = (typeof(data) !== 'undefined' && data.hasOwnProperty('down_count') && data.down_count > 0?data.down_count:0);
					rate_parent.find(".rate-article-up > span").text(up_count);
					rate_parent.closest(".knowledgebase-inner").find(".votes-meta > span").text(up_count);
					rate_parent.find(".rate-article-down > span").text(down_count);
					rate_parent.parent().find(".small_loader").hide();
					rate_parent.find(".rate-article-link").show();
				},error: function (jqXHR, textStatus, errorThrown) {
					// Error
				},complete: function () {
					// Done
				}
			});
		});
	}

	/* Rates style 3 and 4 */

	if (jQuery(".rates_action").length) {
		jQuery(document).on("click",".rates_action",function () {
			var rates = jQuery(this);
			var type = rates.data("type");
			var rate_id = rates.closest(".rate-id");
			var post_id = rate_id.data("id");
			var rates_parent = rates.parent();
			var rates__activated = rate_id.find(".rates__activated");
			rates__activated.removeClass("rates__activated");
			rates_parent.addClass("rates__activated");
			jQuery.ajax({
				type: "POST",
				url: wpqa_rate.admin_url,
				data: {action:"wpqa_rates",post_id:post_id,type:type},
				cache: false,
				dataType: "json",
				success: function (data) {
					rates__activated.removeClass("rates__activated");
					if (typeof(data) !== 'undefined' && data.hasOwnProperty('react') && data.react == "rated") {
						rates_parent.addClass("rates__activated");
					}
				},error: function (jqXHR, textStatus, errorThrown) {
					// Error
				},complete: function () {
					// Done
				}
			});
		});
	}
	
})(jQuery);