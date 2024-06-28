<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$live_search       = wpqa_options("live_search");
$show_button       = (isset($show_button)?$show_button:true);
$search_type_box   = (!isset($search_type_box) || (isset($search_type_box) && $search_type_box == "on")?"show":"hide");
$live_search       = (is_home() || is_front_page()?$live_search:"");
$user_filter       = wpqa_options('user_filter');
$active_points     = wpqa_options("active_points");
$search_attrs      = wpqa_options("search_attrs");
$search_attrs_keys = (is_array($search_attrs) && !empty($search_attrs)?array_keys($search_attrs):"");
$search_type       = wpqa_search_type();
$search_value      = wpqa_search_terms();
if (isset($search_attrs) && is_array($search_attrs) && !empty($search_attrs)) {
	$i_count = $k_count = 0;
	while ($i_count < count($search_attrs)) {
		if (isset($search_attrs[$search_attrs_keys[$i_count]]["value"]) && $search_attrs[$search_attrs_keys[$i_count]]["value"] != "" && $search_attrs[$search_attrs_keys[$i_count]]["value"] != "0") {
			$first_one_search = $i_count;
			break;
		}
		$i_count++;
	}
	if (isset($first_one_search)) {
		$first_one_search = $search_attrs[$search_attrs_keys[$first_one_search]]["value"];
	}
	foreach ($search_attrs as $key => $value) {
		if (isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {
			$k_count++;
			$count_search_attrs = $k_count;
			$search_attr_value = $value["value"];
		}
	}
}

if ($search_type_box == "show") {
	if (isset($search_type) && $search_type == "users" && $user_filter == "on" && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users" && isset($first_one_search)) {
		if (isset($count_search_attrs) && $count_search_attrs > 1) {
			$search_class = "col4 col-boot-sm-4";
		}else {
			$search_class = "col6 col-boot-sm-6";
		}
	}else {
		if (isset($count_search_attrs) && $count_search_attrs > 1) {
			$search_class = "col6 col-boot-sm-6";
		}else {
			$search_class = "col12 col-boot-sm-12";
		}
	}
}else {
	$search_class = "col12 col-boot-sm-12";
}?>
<div class="main-search block-section-div post-search<?php echo ($search_value != ""?"":" search-not-get").(is_home() || is_front_page()?" search-home":"")?>">
	<?php if ((has_himer() || has_knowly()) && !is_home() && !is_front_page()) {?>
		<h2 class="post-title-3"><i class="icon-ios-search-strong"></i><?php esc_html_e("Search","wpqa")?></h2>
	<?php }?>
	<form role="search" method="get" class="searchform main-search-form" action="<?php echo esc_url(wpqa_get_search_permalink())?>">
		<div class="row row-warp row-boot">
			<div class="form-group col<?php echo esc_attr(isset($search_class) && $search_class != ""?" ".$search_class:"")?>">
				<input type="search" class="form-control<?php echo ($live_search == "on"?" live-search":"")?>"<?php echo ($live_search == "on"?" autocomplete='off'":"")?> name="search" value="<?php if ($search_value != "") {echo esc_html($search_value);}else {esc_html_e("Hit enter to search","wpqa");}?>" onfocus="if(this.value=='<?php esc_attr_e("Hit enter to search","wpqa")?>')this.value='';" onblur="if(this.value=='')this.value='<?php esc_attr_e("Hit enter to search","wpqa")?>';">
				<?php if ($live_search == "on") {?>
					<div class="loader_2 search_loader"></div>
					<div class="live-search-results mt-2 search-results results-empty"></div>
				<?php }?>
			</div>
			<?php if (isset($search_attrs) && is_array($search_attrs) && !empty($search_attrs) && isset($first_one_search) && isset($count_search_attrs) && $count_search_attrs > 1 && $search_type_box == "show") {?>
				<div class="form-group col <?php echo (isset($search_type) && $search_type == "users" && $user_filter == "on" && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users"?"col4 col-boot-sm-4":"col6 col-boot-sm-6")?>">
					<span class="styled-select">
						<select name="search_type" class="form-control search_type<?php echo ($user_filter == "on"?" user_filter_active":"")?>">
							<?php if (isset($count_search_attrs) && $count_search_attrs > 1) {?>
								<option value="-1"><?php esc_html_e("Select kind of search","wpqa")?></option>
							<?php }
							foreach ($search_attrs as $key => $value) {
								do_action("wpqa_search_attrs_options",$search_attrs,$key,$value,$search_type);
								if ($key == "questions" && isset($search_attrs["questions"]["value"]) && $search_attrs["questions"]["value"] == "questions") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"questions")?> value="questions"><?php esc_html_e("Questions","wpqa")?></option>
								<?php }else if ($key == "answers" && isset($search_attrs["answers"]["value"]) && $search_attrs["answers"]["value"] == "answers") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"answers")?> value="answers"><?php esc_html_e("Answers","wpqa")?></option>
								<?php }else if ($key == wpqa_question_categories && isset($search_attrs[wpqa_question_categories]["value"]) && $search_attrs[wpqa_question_categories]["value"] == wpqa_question_categories) {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),wpqa_question_categories)?> value="<?php echo wpqa_question_categories?>"><?php esc_html_e("Question categories","wpqa")?></option>
								<?php }else if ($key == wpqa_question_tags && isset($search_attrs[wpqa_question_tags]["value"]) && $search_attrs[wpqa_question_tags]["value"] == wpqa_question_tags) {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),wpqa_question_tags)?> value="<?php echo wpqa_question_tags?>"><?php esc_html_e("Question tags","wpqa")?></option>
								<?php }else if ($key == "knowledgebases" && isset($search_attrs["knowledgebases"]["value"]) && $search_attrs["knowledgebases"]["value"] == "knowledgebases") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"knowledgebases")?> value="knowledgebases"><?php esc_html_e("Knowledgebases","wpqa")?></option>
								<?php }else if ($key == wpqa_knowledgebase_categories && isset($search_attrs[wpqa_knowledgebase_categories]["value"]) && $search_attrs[wpqa_knowledgebase_categories]["value"] == wpqa_knowledgebase_categories) {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),wpqa_knowledgebase_categories)?> value="<?php echo wpqa_knowledgebase_categories?>"><?php esc_html_e("Knowledgebase categories","wpqa")?></option>
								<?php }else if ($key == wpqa_knowledgebase_tags && isset($search_attrs[wpqa_knowledgebase_tags]["value"]) && $search_attrs[wpqa_knowledgebase_tags]["value"] == wpqa_knowledgebase_tags) {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),wpqa_knowledgebase_tags)?> value="<?php echo wpqa_knowledgebase_tags?>"><?php esc_html_e("Knowledgebase tags","wpqa")?></option>
								<?php }else if ($key == "posts" && isset($search_attrs["posts"]["value"]) && $search_attrs["posts"]["value"] == "posts") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"posts")?> value="posts"><?php esc_html_e("Posts","wpqa")?></option>
								<?php }else if ($key == "comments" && isset($search_attrs["comments"]["value"]) && $search_attrs["comments"]["value"] == "comments") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"comments")?> value="comments"><?php esc_html_e("Comments","wpqa")?></option>
								<?php }else if ($key == "category" && isset($search_attrs["category"]["value"]) && $search_attrs["category"]["value"] == "category") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"category")?> value="category"><?php esc_html_e("Post categories","wpqa")?></option>
								<?php }else if ($key == "post_tag" && isset($search_attrs["post_tag"]["value"]) && $search_attrs["post_tag"]["value"] == "post_tag") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"post_tag")?> value="post_tag"><?php esc_html_e("Post tags","wpqa")?></option>
								<?php }else if ($key == "users" && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"users")?> value="users"><?php esc_html_e("Users","wpqa")?></option>
								<?php }else if ($key == "groups" && isset($search_attrs["groups"]["value"]) && $search_attrs["groups"]["value"] == "groups") {?>
									<option <?php selected((isset($search_type) && $search_type != ""?$search_type:""),"groups")?> value="groups"><?php esc_html_e("Groups","wpqa")?></option>
								<?php }
							}?>
						</select>
					</span>
				</div>
			<?php }
			if ($search_type_box == "show" && $user_filter == "on" && isset($first_one_search) && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {
				$user_sort = (isset($_GET["user_filter"]) && $_GET["user_filter"] != ""?esc_html($_GET["user_filter"]):"user_registered");
				echo '<div class="form-group col '.($search_class == "col6" || $search_class == "col6 col-boot-sm-6"?"col6 col-boot-sm-6":"col4 col-boot-sm-4").' user-filter-div'.(isset($search_type) && $search_type == "users"?" user-filter-show":"").'">
					<span class="styled-select user-filter">
						<select class="form-control"'.(isset($search_type) && $search_type == "users"?' name="user_filter"':'').'>
							<option value="user_registered" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"user_registered",false).'>'.esc_html__('Date Registered','wpqa').'</option>
							<option value="display_name" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"display_name",false).'>'.esc_html__('Name','wpqa').'</option>
							<option value="ID" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"ID",false).'>'.esc_html__('ID','wpqa').'</option>
							<option value="question_count" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"question_count",false).'>'.esc_html__('Questions','wpqa').'</option>
							<option value="answers" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"answers",false).'>'.esc_html__('Answers','wpqa').'</option>
							<option value="the_best_answer" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"the_best_answer",false).'>'.esc_html__('Best Answers','wpqa').'</option>';
							if ($active_points == "on") {
								echo '<option value="points" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"points",false).'>'.esc_html__('Points','wpqa').'</option>';
							}
							echo '<option value="post_count" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"post_count",false).'>'.esc_html__('Posts','wpqa').'</option>
							<option value="comments" '.selected((isset($user_sort) && $user_sort != ""?$user_sort:""),"comments",false).'>'.esc_html__('Comments','wpqa').'</option>
						</select>
					</span>
				</div>';
			}?>
		</div>
		<?php if ($search_type_box != "show") {?>
			<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
		<?php }
		if ($show_button == true) {?>
			<div class="wpqa_form">
				<?php if (wpqa_input_button() == "button") {?>
					<button type="submit" class="button-default btn btn__primary btn__large__height btn__block"><?php esc_attr_e('Search','wpqa')?></button>
				<?php }else {?>
					<input type="submit" class="button-default" value="<?php esc_attr_e('Search','wpqa')?>">
				<?php }?>
			</div>
		<?php }?>
	</form>
</div>
<?php do_action("wpqa_after_search")?>