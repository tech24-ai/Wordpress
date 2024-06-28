<?php $dynamic_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebar_layout():"sidebar_default");

$show_sidebar = apply_filters("discy_show_sidebar",true);
if ($show_sidebar == true) {
	if ($dynamic_sidebar != "") {
		dynamic_sidebar(sanitize_title($dynamic_sidebar));
	}
}else {
	do_action("discy_sidebar");
}?>