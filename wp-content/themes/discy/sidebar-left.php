<?php $dynamic_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebar_layout("_2"):"");

$show_sidebar = apply_filters("discy_show_sidebar_2",true);
if ($show_sidebar == true) {
	if ($dynamic_sidebar != "") {
		dynamic_sidebar(sanitize_title($dynamic_sidebar));
	}
}else {
	do_action("discy_sidebar_2");
}?>