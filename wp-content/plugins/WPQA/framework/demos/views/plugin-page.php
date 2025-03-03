<?php
/**
 * The plugin page view - the "settings" page of the plugin.
 *
 * @package ocdi
 */

namespace OCDI;

$predefined_themes = $this->import_files;

if ( ! empty( $this->import_files ) && isset( $_GET['import-mode'] ) && 'manual' === $_GET['import-mode'] ) {
	$predefined_themes = array();
}

/**
 * Hook for adding the custom plugin page header
 */
do_action( 'pt-ocdi/plugin_page_header' );
?>

<div class="ocdi  wrap  about-wrap">

	<?php ob_start(); ?>
		<h1 class="ocdi__title  dashicons-before  dashicons-upload"><?php esc_html_e( 'One Click Demo Import', 'wpqa' ); ?></h1>
	<?php
	$plugin_title = ob_get_clean();

	// Display the plugin title (can be replaced with custom title text through the filter below).
	$plugin_title = apply_filters( 'pt-ocdi/plugin_page_title', $plugin_title );
	if ($plugin_title != "") {
		echo wp_kses_post( $plugin_title );
	}

	// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
	if ( ini_get( 'safe_mode' ) ) {
		printf(
			esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'wpqa' ),
			'<div class="notice  notice-warning  is-dismissible"><p>',
			'<strong>',
			'</strong>',
			'</p></div>'
		);
	}

	// Start output buffer for displaying the plugin intro text.
	ob_start();
	?>

	<div class="ocdi__intro-notice  notice  notice-warning  is-dismissible">
		<p><?php esc_html_e( 'Before you begin, make sure all the required plugins are activated.', 'wpqa' ); ?></p>
	</div>

	<div class="ocdi__intro-text">
		<p class="about-description">
			<?php esc_html_e( 'Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme.', 'wpqa' ); ?>
			<?php esc_html_e( 'It will allow you to quickly edit everything instead of creating content from scratch.', 'wpqa' ); ?>
		</p>

		<hr>

		<p><?php esc_html_e( 'When you import the data, the following things might happen:', 'wpqa' ); ?></p>

		<ul>
			<li><?php esc_html_e( 'No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified.', 'wpqa' ); ?></li>
			<li><?php esc_html_e( 'Posts, pages, images, widgets, menus and other theme settings will get imported.', 'wpqa' ); ?></li>
			<li><?php esc_html_e( 'Please click on the Import button only once and wait, it can take a couple of minutes.', 'wpqa' ); ?></li>
		</ul>

		<?php if ( ! empty( $this->import_files ) ) : ?>
			<?php if ( empty( $_GET['import-mode'] ) || 'manual' !== $_GET['import-mode'] ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => $this->plugin_page_setup['menu_slug'], 'import-mode' => 'manual' ), menu_page_url( $this->plugin_page_setup['parent_slug'], false ) ) ); ?>" class="ocdi__import-mode-switch"><?php esc_html_e( 'Switch to manual import!', 'wpqa' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => $this->plugin_page_setup['menu_slug'] ), menu_page_url( $this->plugin_page_setup['parent_slug'], false ) ) ); ?>" class="ocdi__import-mode-switch"><?php esc_html_e( 'Switch back to theme predefined imports!', 'wpqa' ); ?></a>
			<?php endif; ?>
		<?php endif; ?>

		<hr>
	</div>

	<?php
	$plugin_intro_text = ob_get_clean();

	// Display the plugin intro text (can be replaced with custom text through the filter below).
	$plugin_intro_text = apply_filters( 'pt-ocdi/plugin_intro_text', $plugin_intro_text );
	if ($plugin_intro_text != "") {
		echo wp_kses_post( $plugin_intro_text );
	}
	?>

	<?php if ( empty( $this->import_files ) ) : ?>
		<div class="notice  notice-info  is-dismissible">
			<p><?php esc_html_e( 'There are no predefined import files available in this theme. Please upload the import files manually!', 'wpqa' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( empty( $predefined_themes ) ) : ?>

		<div class="ocdi__file-upload-container">
			<h2><?php esc_html_e( 'Manual demo files upload', 'wpqa' ); ?></h2>

			<div class="ocdi__file-upload">
				<h3><label for="content-file-upload"><?php esc_html_e( 'Choose a XML file for content import:', 'wpqa' ); ?></label></h3>
				<input id="ocdi__content-file-upload" type="file" name="content-file-upload">
			</div>

			<div class="ocdi__file-upload">
				<h3><label for="widget-file-upload"><?php esc_html_e( 'Choose a WIE or JSON file for widget import:', 'wpqa' ); ?></label></h3>
				<input id="ocdi__widget-file-upload" type="file" name="widget-file-upload">
			</div>

			<div class="ocdi__file-upload">
				<h3><label for="customizer-file-upload"><?php esc_html_e( 'Choose a DAT file for customizer import:', 'wpqa' ); ?></label></h3>
				<input id="ocdi__customizer-file-upload" type="file" name="customizer-file-upload">
			</div>

			<?php if ( class_exists( 'ReduxFramework' ) ) : ?>
			<div class="ocdi__file-upload">
				<h3><label for="redux-file-upload"><?php esc_html_e( 'Choose a JSON file for Redux import:', 'wpqa' ); ?></label></h3>
				<input id="ocdi__redux-file-upload" type="file" name="redux-file-upload">
				<div>
					<label for="redux-option-name" class="ocdi__redux-option-name-label"><?php esc_html_e( 'Enter the Redux option name:', 'wpqa' ); ?></label>
					<input id="ocdi__redux-option-name" type="text" name="redux-option-name">
				</div>
			</div>
			<?php endif; ?>
		</div>

		<p class="ocdi__button-container">
			<button class="ocdi__button  button  button-hero  button-primary  js-ocdi-import-data"><?php esc_html_e( 'Import Demo Data', 'wpqa' ); ?></button>
		</p>

	<?php elseif ( 1 === count( $predefined_themes ) ) : ?>

		<div class="ocdi__demo-import-notice  js-ocdi-demo-import-notice"><?php
			if ( is_array( $predefined_themes ) && ! empty( $predefined_themes[0]['import_notice'] ) ) {
				echo wp_kses_post( $predefined_themes[0]['import_notice'] );
			}
		?></div>

		<p class="ocdi__button-container">
			<button class="ocdi__button  button  button-hero  button-primary  js-ocdi-import-data"><?php esc_html_e( 'Import Demo Data', 'wpqa' ); ?></button>
		</p>

	<?php else : ?>

		<!-- OCDI grid layout -->
		<div class="ocdi__gl  js-ocdi-gl">
		<?php
			// Prepare navigation data.
			$categories = Helpers::get_all_demo_import_categories( $predefined_themes );
		?>
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="ocdi__gl-header  js-ocdi-gl-header">
					<nav class="ocdi__gl-navigation">
						<ul>
							<li class="active"><a href="#all" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php esc_html_e( 'All', 'wpqa' ); ?></a></li>
							<?php foreach ( $categories as $key => $name ) : ?>
								<li><a href="#<?php echo esc_attr( $key ); ?>" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php echo esc_html( $name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
					<div clas="ocdi__gl-search">
						<input type="search" class="ocdi__gl-search-input  js-ocdi-gl-search" name="ocdi-gl-search" value="" placeholder="<?php esc_attr_e( 'Search demos...', 'wpqa' ); ?>">
					</div>
				</div>
			<?php endif; ?>
			<div class="ocdi__gl-item-container  wp-clearfix  js-ocdi-gl-item-container">
				<?php foreach ( $predefined_themes as $index => $import_file ) : ?>
					<?php
						// Prepare import item display data.
						$img_src = isset( $import_file['import_preview_image_url'] ) ? $import_file['import_preview_image_url'] : '';
						// Default to the theme screenshot, if a custom preview image is not defined.
						if ( empty( $img_src ) ) {
							$theme = wp_get_theme();
							$img_src = $theme->get_screenshot();
						}

					?>
					<div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo esc_attr( Helpers::get_demo_import_item_categories( $import_file ) ); ?>" data-name="<?php echo esc_attr( strtolower( $import_file['import_file_name'] ) ); ?>">
						<div class="ocdi__gl-item-image-container">
							<?php if ( ! empty( $img_src ) ) : ?>
								<img class="ocdi__gl-item-image" src="<?php echo esc_url( $img_src ) ?>">
							<?php else : ?>
								<div class="ocdi__gl-item-image  ocdi__gl-item-image--no-image"><?php esc_html_e( 'No preview image.', 'wpqa' ); ?></div>
							<?php endif; ?>
						</div>
						<div class="ocdi__gl-item-footer<?php echo ! empty( $import_file['preview_url'] ) ? '  ocdi__gl-item-footer--with-preview' : ''; ?>">
							<h4 class="ocdi__gl-item-title" title="<?php echo esc_attr( $import_file['import_file_name'] ); ?>"><?php echo esc_html( $import_file['import_file_name'] ); ?></h4>
							<?php $show_import = true;
							if (isset($import_file["required_plugins"])) {
								$required_plugins = explode(",",$import_file["required_plugins"]);
								if (is_array($required_plugins) && !empty($required_plugins)) {
									foreach ($required_plugins as $plugin) {
										if (!is_plugin_active($plugin)) {
											$show_import = false;
										}
									}
								}
							}
							if ($show_import == true) {?>
								<div class="ocdi__gl-item-button import-button-div js-ocdi-gl-import-data" data-value="<?php echo esc_attr( $index ); ?>"><div><?php esc_html_e( 'Import', 'wpqa' ); ?></div></div>
							<?php }else {?>
								<div class="ocdi__gl-item-button import-button-div required-button-div"><div><a href="<?php echo admin_url('admin.php?page=install-required-plugins')?>" target="_blank"><?php esc_html_e( 'Required plugin', 'wpqa' ); ?></a></div></div>
							<?php }
							if ( ! empty( $import_file['preview_url'] ) ) : ?>
								<div class="preview-demo-div"><a class="ocdi__gl-item-button  button" href="<?php echo esc_url( $import_file['preview_url'] ); ?>" target="_blank"><?php esc_html_e( 'Live Demo', 'wpqa' ); ?></a></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="js-ocdi-modal-content"></div>

	<?php endif; ?>

	<p class="ocdi__ajax-loader  js-ocdi-ajax-loader">
		<span class="spinner"></span> <?php esc_html_e( 'Importing, please wait!', 'wpqa' ); ?>
	</p>

	<div class="ocdi__response  js-ocdi-ajax-response"></div>
</div>

<?php
/**
 * Hook for adding the custom admin page footer
 */
do_action( 'pt-ocdi/plugin_page_footer' );
