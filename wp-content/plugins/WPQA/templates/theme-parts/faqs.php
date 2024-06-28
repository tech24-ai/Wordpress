<?php if (isset($custom_faqs) && is_array($custom_faqs)) {
	$faqs_i = 0;
	echo "<div class='accordion toggle-accordion'>";
		foreach ($custom_faqs as $faqs_key => $faqs) {
			$faqs_i++;
			$faqs_title = $faqs["text"];
			$faqs_content = $faqs["textarea"];
			echo "<div class='accordion-content accordion-item'>
				<h4 class='accordion-title accordion__header accordion__title collapsed' data-toggle='collapse' data-target='#collapse".$faqs_i."' aria-expanded='true'><a href='#'>".$faqs_title."</a></h4>
				<div class='accordion-inner collapse' id='collapse".$faqs_i."' data-parent='.accordion'><div class='accordion__body'>".nl2br($faqs_content)."</div></div>
			</div>";
		}
	echo "</div>";
}?>