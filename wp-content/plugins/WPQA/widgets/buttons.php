<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Buttons */
add_action( 'widgets_init', 'wpqa_buttons_widget' );
function wpqa_buttons_widget() {
	register_widget( 'Widget_Ask' );
}

class Widget_Ask extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'ask-widget'  );
		$control_ops = array( 'id_base' => 'ask-widget' );
		parent::__construct( 'ask-widget',wpqa_widgets.' - Buttons', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );?>
		<div class="widget card widget_ask">
			<?php $button = (isset($instance['button'])?esc_html($instance['button']):"");
			if ($button == "custom") {
				$filter_class  = $button_class = "";
				$button_target = (isset($instance['button_target'])?esc_html($instance['button_target']):'');
				$button_link   = (isset($instance['button_link'])?esc_html($instance['button_link']):'');
				$button_text   = (isset($instance['button_text'])?esc_html($instance['button_text']):'');
			}else if ($button == "post") {
				$filter_class = "post";
				$button_class = "wpqa-post";
				$button_link  = wpqa_add_post_permalink();
				$button_text  = esc_html__("Add A New Post","wpqa");
			}else if ($button == "group") {
				$filter_class = "group";
				$button_class = "wpqa-group";
				$button_link  = wpqa_add_group_permalink();
				$button_text  = esc_html__("Create A New Group","wpqa");
			}else if (!is_user_logged_in() && $button == "login") {
				$activate_login = wpqa_options("activate_login");
				if ($activate_login != 'disabled') {
					$filter_class = "login";
					$button_class = "login-panel";
					$button_link  = wpqa_login_permalink();
					$button_text  = esc_html__("Login","wpqa");
				}
			}else if (!is_user_logged_in() && $button == "signup") {
				$activate_register = wpqa_options("activate_register");
				if ($activate_register != 'disabled') {
					$filter_class = "signup";
					$button_class = "signup-panel";
					$button_link  = wpqa_signup_permalink();
					$button_text  = esc_html__("Create A New Account","wpqa");
				}
			}else if ($button == "poll") {
				$filter_class = "question";
				$button_class = "wpqa-question";
				$button_link  = esc_url_raw(add_query_arg(array("type" => "poll"),wpqa_add_question_permalink()));
				$button_text  = esc_html__("Add A Poll","wpqa");
			}else {
				$filter_class = "question";
				$button_class = "wpqa-question";
				$button_link  = wpqa_add_question_permalink();
				$button_text  = esc_html__("Ask A Question","wpqa");
			}
			$button_target = ($button == "custom" && isset($button_target) && $button_target == "new_page"?"_blank":"_self");
			echo '<a target="'.esc_attr($button_target).'" href="'.esc_url($button_link).'" class="button-default btn btn__primary btn__block btn__semi__height '.$button_class.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_'.$filter_class,'').'">'.$button_text.'</a>';?>
		</div>
	<?php }

	public function form( $instance ) {
		/* Save Button */
	}
}?>