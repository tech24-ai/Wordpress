<?php
/* Subscribe */
add_action( 'widgets_init', 'discy_widget_subscribe_widget' );
function discy_widget_subscribe_widget() {
	register_widget( 'Widget_Subscribe' );
}
class Widget_Subscribe extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'subscribe-widget' );
		$control_ops = array( 'id_base' => 'subscribe-widget' );
		parent::__construct( 'subscribe-widget',discy_theme_name.' - Subscribe', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title             = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$newsletter_action = (isset($instance['newsletter_action'])?esc_html($instance['newsletter_action']):'');

		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Subscribe","discy")."</h3>";
			}?>
			<div class="widget-wrap">
				<form class="validate" action="<?php echo esc_attr($newsletter_action)?>" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
					<input name="EMAIL" type="email" value="<?php esc_attr_e("Type Your Email","discy");?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;">
					<button name="subscribe" type="submit" class="button-default" aria-label="<?php esc_attr_e('Subscribe','discy')?>"><i class="icon-right-open"></i></button>
				</form>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>