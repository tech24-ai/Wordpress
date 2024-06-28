<div class="card">
	<?php if (has_knowly() || has_himer()) {
		if ( is_home() && current_user_can( 'publish_posts' ) ) : 
			$show_error_title = true;
		elseif ( ( wpqa_is_search() ) || is_search() ) :
		elseif ( is_tax() || is_post_type_archive() || is_archive() || isset($not_fount_error) ) :
		elseif (is_404()) :
			$show_error_title = true;
		else :
			$show_error_title = true;
		endif;
		if (isset($show_error_title)) {?>
			<div class="card-header d-flex align-items-center flex-wrap justify-content-between">
				<h2 class="card-title mb-0 d-flex align-items-center">
					<i class="icon-alert-circled font-xl card-title__icon"></i>
					<span><?php esc_html_e('404 Error','wpqa')?></span>
				</h2>
		    </div>
		<?php }
	}?>
	<div class="card-body">
		<div class="no-results not-found text-center">
			<?php $whats_post_type = esc_html__('Posts','wpqa');
			if (isset($its_post_type) && (wpqa_questions_type == $its_post_type || wpqa_asked_questions_type == $its_post_type)) {
				$whats_post_type = esc_html__('Questions','wpqa');
			}else if (isset($its_post_type) && wpqa_knowledgebase_type == $its_post_type) {
				$whats_post_type = esc_html__('Knowledgebases','wpqa');
			}
			$templates = array("search.php");
			$wpqa_get_template = wpqa_get_template($templates,(isset($folder) && $folder != ""?$folder."/":""));
			if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<h3 class="error-desc"><?php printf(esc_html__('Sorry, No %1$s Found.','wpqa'),$whats_post_type)?></h3>
				<p class="mb-2rem"><?php printf( esc_html__( 'Ready to publish your first post? %1$s Get started here %2$s.', 'wpqa' ), '<a href="'.esc_url( admin_url( 'post-new.php' ) ).'">', '</a>' ); ?></p>
			<?php elseif ( wpqa_is_search() || is_search() ) : ?>
				<h3 class="error-desc"><?php esc_html_e( 'Sorry, No Results Found.', 'wpqa' ); ?></h3>
				<p class="mb-2rem"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wpqa' ); ?></p>
				<?php isset($wpqa_get_template)?include $wpqa_get_template:'';
			elseif ( is_tax() || is_post_type_archive() || is_archive() || isset($not_fount_error) ) :?>
				<h3 class="error-desc"><?php printf(esc_html__('Sorry, No %1$s Found.','wpqa'),$whats_post_type)?></h3>
				<p class="mb-2rem"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wpqa' ); ?></p>
				<?php isset($wpqa_get_template)?include $wpqa_get_template:'';
			elseif (is_404()) : ?>
				<h2 class="error-404 mb-0"><?php esc_html_e( '404', 'wpqa' ); ?></h2>
				<h3 class="error-desc"><?php esc_html_e( 'Oops! Page Not Found.', 'wpqa' ); ?></h3>
				<p class="mb-2rem"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wpqa' ); ?></p>
				<?php isset($wpqa_get_template)?include $wpqa_get_template:'';
			else : ?>
				<p class="mb-2rem"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wpqa' ); ?></p>
				<?php isset($wpqa_get_template)?include $wpqa_get_template:'';
			endif;?>
		</div><!-- no-results -->
	</div>
</div>