<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Important Notices */
add_action( 'widgets_init', 'wpqa_widget_important_notices_widget' );
function wpqa_widget_important_notices_widget() {
	register_widget( 'Widget_important_notices' );
}
class Widget_important_notices extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-important-notices' );
		$control_ops = array( 'id_base' => 'widget_important_notices' );
		parent::__construct( 'widget_important_notices',wpqa_widgets.' - Important Notices', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		global $post;
		extract( $args );
		if (isset($args["widget_id"]) && $args["widget_id"] != "" && ((is_user_logged_in() && get_user_meta(get_current_user_id(),"wpqa_important_notices_".$args["widget_id"],true) == "wpqa_yes_hidden") || (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$args["widget_id"]]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$args["widget_id"]] == "wpqa_yes_hidden"))) {
			$yes_hidden = true;
		}
		if (!isset($yes_hidden)) {
			$show = (isset($instance['show'])?esc_html($instance['show']):'');
			$custom_pages = (isset($instance['custom_pages'])?esc_html($instance['custom_pages']):'');
			$custom_pages = explode(",",$custom_pages);
			if (((is_front_page() || is_home()) && $show == "home_page") || $show == "all_pages" || ($show == "all_posts" && is_singular("post")) || ($show == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || ($show == "all_knowledgebases" && is_singular(wpqa_knowledgebase_type)) || ($show == "custom_pages" && is_page() && isset($custom_pages) && is_array($custom_pages) && isset($post->ID) && in_array($post->ID,$custom_pages))) {
				$action = (isset($instance['action'])?esc_html($instance['action']):'');
				if ($action == "both" || ($action == "unlogged" && !is_user_logged_in()) || ($action == "logged" && is_user_logged_in())) {
					$user_id           = get_current_user_id();
					$title             = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
					$important_notices = (isset($instance['important_notices'])?$instance['important_notices']:'');
					echo ($before_widget);
					if ($title) {
						echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
					}else {
						echo "<h3 class='screen-reader-text'>".esc_html__("Important Notices","wpqa")."</h3>";
					}?>
					<div class="widget-wrap">
						<p><?php esc_html_e("For more attention and answers to your questions, kindly make sure to read the below notes:","wpqa")?></p>
						<ul class="list-with-arrows list-unstyled mb-3">
							<?php if (is_array($important_notices) && !empty($important_notices)) {
								foreach ($important_notices as $key => $value) {
									if (isset($value["term"]) && $value["term"] != "") {
										echo "<li class='widget-li'>".$value["term"]."</li>";
										do_action("wpqa_important_notices",$value["term"]);
									}
								}
							}?>
						</ul>
						<a class="button-default button-hide-click important-notices-button btn btn__primary btn__block btn__semi__height" href="#"><?php esc_html_e("Got It, Dimiss!","wpqa")?></a>
						<span class="load_span"><span class="loader_2"></span></span>
					</div>
					<?php echo ($after_widget);
				}
			}
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>