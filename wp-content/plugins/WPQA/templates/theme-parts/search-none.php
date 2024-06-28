<div class="card">
	<div class="card-body">
		<div class="no-results not-found text-center">
			<h3 class="error-desc"><?php esc_html_e( 'Sorry, No Results Found.', 'wpqa' ); ?></h3>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wpqa' ); ?></p>
			<?php $back_to_home = apply_filters('wpqa_back_to_home',true);
			if ($back_to_home == true) {?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button-default btn btn__semi__height btn__primary"><?php esc_html_e( 'Back To Homepage', 'wpqa' ); ?></a>
			<?php }?>
		</div><!-- no-results -->
	</div>
</div>