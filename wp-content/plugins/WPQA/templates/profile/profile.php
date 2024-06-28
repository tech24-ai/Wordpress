<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_action_on_user_page");

include wpqa_get_template("head.php","profile/");

include wpqa_get_template("content.php","profile/");?>