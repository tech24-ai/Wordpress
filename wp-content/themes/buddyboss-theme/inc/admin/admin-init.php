<?php
/**
 * Register BuddyBoss Menu Page
 */

if ( ! function_exists( 'register_buddyboss_menu_page' ) ) {

	function register_buddyboss_menu_page() {
		// don't create menu if buddyboss platform in activated
		if (function_exists('buddypress') && isset(buddypress()->boddyboss)) {
			return;
		}

		global $menu;

		// Add BuddyBoss Menu separator above the BuddyBoss and below the BuddyBoss
		$menu[] = array( '', 'read', 'separator-buddyboss-theme', '', 'wp-menu-separator buddyboss-theme' ); // WPCS: override ok.
		$menu[] = array( '', 'read', 'separator-plugins', '', 'wp-menu-separator plugins' ); // WPCS: override ok.

		// Set position with odd number to avoid confict with other plugin/theme.
		add_menu_page( 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings', '', get_template_directory_uri() . '/assets/images/svg/menu-icon.svg', 3 );

		// To remove empty parent menu item.
		add_submenu_page( 'buddyboss-settings', 'BuddyBoss', 'BuddyBoss', 'manage_options', 'buddyboss-settings' );
		remove_submenu_page( 'buddyboss-settings', 'buddyboss-settings' );
	}

	add_action( 'admin_menu', 'register_buddyboss_menu_page' );

	/**
	 * Add the separator above the BuddyBoss menu in admin.
	 *
	 * @param int $menu_order Menu order.
	 * @return array
	 */
	function buddyboss_theme_menu_order( $menu_order ) {

		// don't create separator if buddyboss platform is activated
		if (function_exists('buddypress') && isset(buddypress()->boddyboss)) {
			return $menu_order;
		}

		// Initialize our custom order array.
		$buddyboss_menu_order = array();

		// Get the index of our custom separator.
		$buddyboss_separator = array_search( 'separator-buddyboss-settings', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'buddyboss-settings' === $item ) {
				$buddyboss_menu_order[] = 'separator-buddyboss-theme';
				$buddyboss_menu_order[] = $item;
				unset( $menu_order[ $buddyboss_separator ] );
			} elseif ( ! in_array( $item, array( 'separator-buddyboss-theme' ), true ) ) {
				$buddyboss_menu_order[] = $item;
			}

		}

		// Return order.
		return $buddyboss_menu_order;
	}

	/**
	 * Add the separator above the plugins menu in admin.
	 *
	 * @param int $menu_order Menu order.
	 * @return array
	 */
	function buddyboss_theme_plugins_menu_order( $menu_order ) {

		// don't create separator if buddyboss platform is activated
		if (function_exists('buddypress') && isset(buddypress()->boddyboss)) {
			return $menu_order;
		}

		// Initialize our custom order array.
		$plugins_menu_order = array();

		// Get the index of our custom separator.
		$plugins_separator = array_search( 'separator-plugins.php', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'plugins.php' === $item ) {
				$plugins_menu_order[] = 'separator-plugins';
				$plugins_menu_order[] = $item;
				unset( $menu_order[ $plugins_separator ] );
			} elseif ( ! in_array( $item, array( 'separator-plugins' ), true ) ) {
				$plugins_menu_order[] = $item;
			}

		}

		// Return order.
		return $plugins_menu_order;
	}

	// Add the separator above the BuddyBoss in admin.
	add_filter( 'menu_order', 'buddyboss_theme_menu_order' );

	// Add the separator above the plugins in admin.
	add_filter( 'menu_order', 'buddyboss_theme_plugins_menu_order' );
}


/**
 * Load extensions - MUST be loaded before your options are set
 */
if ( file_exists( dirname( __FILE__ ) . '/buddyboss-extensions/extensions-init.php' ) ) {
	require_once( dirname( __FILE__ ) . '/buddyboss-extensions/extensions-init.php' );
}

/**
 * Load redux
 */
if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/framework/ReduxCore/framework.php' ) ) {
	require_once( dirname( __FILE__ ) . '/framework/ReduxCore/framework.php' );
}

/**
 * Load the theme/plugin options
 */
if ( !function_exists( 'load_boss_theme_options' ) ) {

	function load_boss_theme_options() {
		if ( file_exists( dirname( __FILE__ ) . '/options-init.php' ) ) {
			require_once( dirname( __FILE__ ) . '/options-init.php' );
		}
		if ( file_exists( dirname( __FILE__ ) . '/plugin-support.php' ) ) {
			require_once( dirname( __FILE__ ) . '/plugin-support.php' );
		}
	}

	// This is used to show xProfile fields in option settings.
	if ( function_exists( 'bp_is_active' ) ) {
		add_action( 'bp_init', 'load_boss_theme_options' );
	} else {
		load_boss_theme_options();
	}

}

/**
 * Remove redux menu under the tools
 */
if ( !function_exists( 'boss_remove_redux_menu' ) ) {

	function boss_remove_redux_menu() {
		remove_submenu_page( 'tools.php', 'redux-about' );
	}

	add_action( 'admin_menu', 'boss_remove_redux_menu', 12 );
}

/**
 * Remove redux demo links
 */
if ( !function_exists( 'boss_remove_DemoModeLink' ) ) {

	function boss_remove_DemoModeLink() {
		// Be sure to rename this function to something more unique
		if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
			remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks' ), null, 2 );
		}

		if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
			remove_action( 'admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );
		}
	}

	add_action( 'init', 'boss_remove_DemoModeLink' );
}

/**
 * Remove redux dashboard widget
 */
if ( !function_exists( 'boss_remove_dashboard_widget' ) ) {

	function boss_remove_dashboard_widget() {
		remove_meta_box( 'redux_dashboard_widget', 'dashboard', 'side' );
	}

	// Hook into the 'wp_dashboard_setup' action to register our function
	add_action( 'wp_dashboard_setup', 'boss_remove_dashboard_widget', 999 );
}

/**
 * Custom panel styles
 */
if ( !function_exists( 'boss_custom_panel_styles_scripts' ) ) {

	function boss_custom_panel_styles_scripts() {

		wp_register_style( 'redux-custom-panel', get_template_directory_uri() . '/inc/admin/assets/css/redux-custom-panel.css', array( 'redux-admin-css' ), time(), 'all' );
		wp_enqueue_style( 'redux-custom-panel' );

		wp_register_script( 'redux-custom-script', get_template_directory_uri() . '/inc/admin/assets/js/boss-custom-admin.js' );
		wp_enqueue_script( 'redux-custom-script' );

		wp_localize_script( 'redux-custom-script', 'BOSS_CUSTOM_ADMIN', array(
		        'elementor_pro_active' => ! empty( buddyboss_theme()->elementor_pro_helper() ) ? '1' : '0'
        ) );

	}

	// This example assumes your opt_name is set to redux_demo, replace with your opt_name value
	add_action( 'redux/page/buddyboss_theme_options/enqueue', 'boss_custom_panel_styles_scripts' );
}

/**
 * Hide Redux Notifications and Ads
 */
if ( !function_exists( 'boss_remove_redux_ads' ) ) {

	function boss_remove_redux_ads() {
		echo '<style>
		#wpbody-content .redux-messageredux-notice,
		.redux-message.redux-notice,
		#redux-header .rAds,
		#buddyboss_theme_options-favicon,
		#buddyboss_theme_options-admin_custom_colors {
			display: none !important;
			opacity: 0;
			visibility: hidden;
		}
		</style>';
	}

	add_action( 'admin_head', 'boss_remove_redux_ads' );
}

/**
 * Redux dev mode false
 */
if ( !function_exists( 'redux_disable_dev_mode_plugin' ) ) {

	function redux_disable_dev_mode_plugin( $redux ) {
		if ( $redux->args[ 'opt_name' ] != 'buddyboss_theme_options' ) {
			$redux->args[ 'dev_mode' ]				 = false;
			$redux->args[ 'forced_dev_mode_off' ]	 = false;
		}
	}

	add_action( 'redux/construct', 'redux_disable_dev_mode_plugin' );
}

/**
 * When the last save mode is the normal save options
 */
if ( !function_exists('redux_options_buddyboss_theme_saved' ) ) {

    function redux_options_buddyboss_theme_saved() {

	    if ( isset( $_POST['action'] ) && in_array( $_POST['action'], array(
				    'buddyboss_theme_options_ajax_save',
				    'customize_save'
			    ) ) ) {
		    delete_transient( 'buddyboss_theme_typography' );
		    delete_transient( 'buddyboss_theme_compressed_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_bp_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_forums_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_learndash_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_woocommerce_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_jobsmanager_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_events_custom_css' );
		    delete_transient( 'buddyboss_theme_compressed_gamipress_custom_css' );
            delete_transient( 'buddyboss_theme_compressed_badgeos_custom_css' );
            delete_transient( 'buddyboss_theme_compressed_pmpro_custom_css' );
			delete_transient( 'buddyboss_theme_compressed_plugins_custom_css' );
			delete_transient( 'buddyboss_theme_compressed_lifterLMS_custom_css' );
			delete_transient( 'buddyboss_theme_compressed_elementor_custom_css' );
	    }

    }

    add_action( 'redux/options/buddyboss_theme_options/saved', 'redux_options_buddyboss_theme_saved' );
    add_action( 'customize_save', 'redux_options_buddyboss_theme_saved' );
}

if ( ! function_exists( 'buddyboss_theme_reset_profile_cover_position' ) ) {

	/**
	 * Reset Cover image for Member Profiles when changes backend setting(width and height).
	 *
	 * @since 1.5.8
	 *
	 * @param array $options        Array of theme options with ols value.
	 * @param array $changed_values Array of theme options with updated value.
	 */
	function buddyboss_theme_reset_profile_cover_position( $options, $changed_values ) {
		if ( ! empty( $changed_values ) ) {
			if (
				(
					array_key_exists( 'buddyboss_profile_cover_width', $options )
					&& array_key_exists( 'buddyboss_profile_cover_width', $changed_values )
					&& $options['buddyboss_profile_cover_width'] !== $changed_values['buddyboss_profile_cover_width']
				)
				|| (
					array_key_exists( 'buddyboss_profile_cover_height', $options )
					&& array_key_exists( 'buddyboss_profile_cover_height', $changed_values )
					&& $options['buddyboss_profile_cover_height'] !== $changed_values['buddyboss_profile_cover_height']
				)
			) {
				$all_users = get_users(
					array(
						'fields'   => 'ids',
						'meta_key' => 'bp_cover_position',
					)
				);

				if ( ! empty( $all_users ) ) {
					foreach ( $all_users as $id ) {
						delete_user_meta( $id, 'bp_cover_position' );
					}
				}
			}

			if (
				function_exists( 'bp_attachments_get_attachment' )
				&& array_key_exists( 'buddyboss_profile_cover_default', $options )
				&& array_key_exists( 'buddyboss_profile_cover_default', $changed_values )
				&& isset( $options['buddyboss_profile_cover_default']['url'] )
				&& isset( $changed_values['buddyboss_profile_cover_default']['url'] )
				&& $options['buddyboss_profile_cover_default'] !== $changed_values['buddyboss_profile_cover_default']
			) {
				$all_users = get_users(
					array(
						'fields'   => 'ids',
						'meta_key' => 'bp_cover_position',
					)
				);

				if ( ! empty( $all_users ) ) {
					foreach ( $all_users as $id ) {
						if ( empty( bp_attachments_get_attachment(
							'url',
							array(
								'object_dir' => 'members',
								'item_id'    => $id,
							)
						) ) ) {
							delete_user_meta( $id, 'bp_cover_position' );
						}
					}
				}
			}
		}
	}

	add_action( 'redux/options/buddyboss_theme_options/saved', 'buddyboss_theme_reset_profile_cover_position', 10, 2 );
}

if ( ! function_exists( 'buddyboss_theme_reset_bb_group_cover_position' ) ) {

	/**
	 * Reset Cover image for Social Groups when changes backend setting(width and height).
	 *
	 * @since 1.5.8
	 *
	 * @param array $options        Array of theme options with ols value.
	 * @param array $changed_values Array of theme options with updated value.
	 */
	function buddyboss_theme_reset_bb_group_cover_position( $options, $changed_values ) {
		if ( ! empty( $changed_values ) && function_exists( 'groups_get_groups' ) ) {
			if (
				(
					array_key_exists( 'buddyboss_group_cover_width', $options )
					&& array_key_exists( 'buddyboss_group_cover_width', $changed_values )
					&& $options['buddyboss_group_cover_width'] !== $changed_values['buddyboss_group_cover_width']
				)
				|| (
					array_key_exists( 'buddyboss_group_cover_height', $options )
					&& array_key_exists( 'buddyboss_group_cover_height', $changed_values )
					&& $options['buddyboss_group_cover_height'] !== $changed_values['buddyboss_group_cover_height']
				)
			) {
				$all_groups = groups_get_groups(
					array(
						'fields'      => 'ids',
						'per_page'    => 999999,
						'orderby'     => 'last_activity',
						'meta_query'  => array(
							array(
								'key'     => 'bp_cover_position',
								'compare' => 'EXISTS',
							)
						),
						'show_hidden' => true,
					)
				);

				if ( ! empty( $all_groups ) && ! empty( $all_groups['groups'] ) ) {
					foreach ( $all_groups['groups'] as $group_id ) {
						groups_delete_groupmeta( $group_id, 'bp_cover_position' );
					}
				}
			}

			if (
				function_exists( 'bp_attachments_get_attachment' )
				&& array_key_exists( 'buddyboss_group_cover_default', $options )
				&& array_key_exists( 'buddyboss_group_cover_default', $changed_values )
				&& isset( $options['buddyboss_group_cover_default']['url'] )
				&& isset( $changed_values['buddyboss_group_cover_default']['url'] )
				&& $options['buddyboss_group_cover_default'] !== $changed_values['buddyboss_group_cover_default']
			) {
				$all_groups = groups_get_groups(
					array(
						'fields'      => 'ids',
						'per_page'    => 999999,
						'orderby'     => 'last_activity',
						'meta_query'  => array(
							array(
								'key'     => 'bp_cover_position',
								'compare' => 'EXISTS',
							)
						),
						'show_hidden' => true,
					)
				);

				if ( ! empty( $all_groups ) && ! empty( $all_groups['groups'] ) ) {
					foreach ( $all_groups['groups'] as $group_id ) {
						if ( empty( bp_attachments_get_attachment( 'url', array(
							'object_dir' => 'groups',
							'item_id'    => $group_id
						) ) ) ) {
							groups_delete_groupmeta( $group_id, 'bp_cover_position' );
						}
					}
				}
			}
		}
	}

	add_action( 'redux/options/buddyboss_theme_options/saved', 'buddyboss_theme_reset_bb_group_cover_position', 10, 2 );
}

/**
 * Print page padding field template
 */
if ( ! function_exists( 'buddyboss_theme_page_template' ) ) {

	function buddyboss_theme_page_template() {
		global $typenow, $post;

		if ( $typenow !== 'page' ) {
			return;
		}

		$padding = false;
		if( !empty( $post ) ){
			$padding = get_post_meta( $post->ID, '_wp_page_padding', true );
		}

		$current_screen = get_current_screen();
		$is_block_editor = method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
		?>

		<?php if ( ! $is_block_editor ): ?>
		<script type="text/html" id="tmpl-classic-editor-page-padding">
			<div id="page-padding-label-wrapper">
				<p class="post-attributes-label-wrapper">
					<label for="page_padding" class="post-attributes-label"><?php esc_html_e( 'Padding', 'buddyboss-theme' ) ?></label>
				</p>
				<input name="page_padding" type="number" size="4" id="page_padding" value="<?php echo $padding; ?>">&nbsp;px
			</div>
		</script>
		<?php else: ?>
		<script type="text/html" id="tmpl-block-editor-page-padding">
			<div id="page-padding-label-wrapper" class="components-base-control__field">
				<label
					for="page_padding"
					class="components-base-control__label">
					<?php esc_html_e( 'Padding:', 'buddyboss-theme' ) ?>
				</label>
				<input
					name="_wp_page_padding"
					type="number"
					id="_wp_page_padding"
					value=""
					class="components-text-control__input"
				/>
			</div>
		</script>

		<style>
			#buddyboss-page-padding-metabox {
				display: none;
			}
		</style>
		<?php endif; ?>
		<?php
	}

	add_action( 'admin_footer', 'buddyboss_theme_page_template' );
}

/**
 * Save page padding
 */
if ( ! function_exists( 'buddyboss_theme_save_page_padding' ) ) {

	function buddyboss_theme_save_page_padding( $post_ID ) {

		if ( isset( $_POST['page_padding'] ) && ! empty( $_POST['page_padding'] ) ) {
			update_post_meta( $post_ID, '_wp_page_padding', $_POST['page_padding'] );
		} else {
			delete_post_meta( $post_ID, '_wp_page_padding' );
		}
	}

	add_action( 'save_post_page', 'buddyboss_theme_save_page_padding', 10, 1 );
}

/**
 * Register page padding metabox
 */
if ( ! function_exists( 'buddyboss_theme_page_padding_meta_box' ) ) {
	function buddyboss_theme_page_padding_meta_box() {
		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			add_meta_box( 'buddyboss-page-padding-metabox', 'Padding', 'buddyboss_page_padding_meta_box', 'page', 'side', 'core' );
		}
	}

	add_action( 'add_meta_boxes', 'buddyboss_theme_page_padding_meta_box' );
}

/**
 * Page padding metabox
 */
if ( ! function_exists( 'buddyboss_page_padding_meta_box' ) ) {
	function buddyboss_page_padding_meta_box() {
		global $post;
		$padding = get_post_meta( $post->ID, '_wp_page_padding', true );
		?>
		<p class="post-attributes-label-wrapper">
			<label for="fullscreen_padding" class="post-attributes-label">
				<?php esc_html_e( 'Padding', 'buddyboss-theme' ) ?>
			</label>
		</p>
		<input name="page_padding" type="number" size="4" id="page_padding" value="<?php echo $padding; ?>">&nbsp
		<?php
	}
}

/**
 * Display Hello Screen
 */
if ( ! function_exists( 'buddyboss_theme_activation_redirect' ) ) {
	function buddyboss_theme_activation_redirect() {
		global $pagenow;
		if ( "themes.php" == $pagenow && is_admin() && isset( $_GET['activated'] ) ) {
			wp_redirect( esc_url_raw( add_query_arg( 'hello', 'theme', admin_url() ) ) );
		}
	}
	add_action( 'admin_init', 'buddyboss_theme_activation_redirect' );
}

/**
 * Display About Screen
 */
if ( ! function_exists( 'about_theme_screen' ) ) {
	function about_theme_screen() {
		if ( 0 !== strpos( get_current_screen()->id, 'dashboard' ) || empty( $_GET['hello'] ) || $_GET['hello'] !== 'theme' ) {
			return;
		}

		include get_template_directory().'/template-parts/admin-hello-theme-popup.php';
	}
	// Hello Theme.
	add_action( 'admin_footer', 'about_theme_screen' );
}

if ( ! function_exists( 'buddyboss_theme_hello_theme_custom_wp_admin_style' ) ) {
	function buddyboss_theme_hello_theme_custom_wp_admin_style() {
        $rtl_css = is_rtl() ? '-rtl' : '';
        $minified_css = buddyboss_theme_get_option( 'boss_minified_css' );
        $mincss = $minified_css ? '.min' : '';

		$minified_js = buddyboss_theme_get_option( 'boss_minified_js' );
		$minjs = $minified_js ? '.min' : '';

		if ( 0 !== strpos( get_current_screen()->id, 'dashboard' ) || empty( $_GET['hello'] ) || $_GET['hello'] !== 'theme' ) {

		} else {
            wp_register_style( 'buddyboss-theme-hello-css', get_template_directory_uri() . '/assets/css' . $rtl_css . '/hello-theme' . $mincss . '.css', '', buddyboss_theme()->version() );
			wp_enqueue_style( 'buddyboss-theme-hello-css' );

            wp_register_script( 'buddyboss-theme-hello-js', get_template_directory_uri() . '/assets/js/hello-theme' . $minjs . '.js', array( 'jquery' ), buddyboss_theme()->version(), true );
			wp_enqueue_script( 'buddyboss-theme-hello-js' );
		}
	}

	add_action( 'admin_enqueue_scripts', 'buddyboss_theme_hello_theme_custom_wp_admin_style' );
}

/**
 * Display notice if LearnDash version if below LearnDash 3.0
 */
if ( ! function_exists( 'buddyboss_theme_ld_30_admin_notice' ) ) {
	function buddyboss_theme_ld_30_admin_notice() {

		if ( in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

			$plugin_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ).'sfwd-lms/sfwd_lms.php' );
			$plugin_version = ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : 0;

			if ( $plugin_version && version_compare( $plugin_version, '3.0', '<') ) {
				?>
                <div class="notice notice-success">
                    <p><?php echo sprintf( __( 'BuddyBoss Theme requires LearnDash 3.0 or above.  <a href="%s">Update LearnDash</a>', 'buddyboss-theme' ), admin_url( 'plugins.php' ) ); ?></p>
                </div>
				<?php
			} else {
			    if ( function_exists( 'learndash_is_active_theme' ) && learndash_is_active_theme( 'legacy' ) ) {
			        ?>
                    <div class="notice notice-success">
                        <p><?php echo sprintf( __( 'BuddyBoss Theme requires the <strong>LearnDash 3.0</strong>  template, however you are using the <strong>Legacy</strong> template. <a href="%s">Repair</a>', 'buddyboss-theme' ), add_query_arg( [
		                        'page' => 'learndash_lms_settings',
	                        ],
		                        admin_url( 'admin.php' ) ) ); ?></p>
                    </div>
                    <?php
                }
            }
		}
	}

	add_action( 'admin_notices', 'buddyboss_theme_ld_30_admin_notice' );
}

/**
 * Typography field for default fonts
 */
if ( ! function_exists( 'buddyboss_theme_add_typography_field_default_fonts' ) ) {

	function buddyboss_theme_add_typography_field_default_fonts( $fonts ) {
		$fonts[ __( 'Default Fonts', 'buddyboss-theme' ) ] = array(
			'SF UI Display' => array(
				'variants' => array(
					'400' => __( 'Normal 400', 'buddyboss-theme' ),
					'500' => __( 'Medium 500', 'buddyboss-theme' ),
					'700' => __( 'Bold 700', 'buddyboss-theme' ),
				),
			),
			'SF UI Text' => array(
				'variants' => array(
					'300normal' => __( 'Light 300', 'buddyboss-theme' ),
					'400normal' => __( 'Normal 400', 'buddyboss-theme' ),
					'500'       => __( 'Medium 500', 'buddyboss-theme' ),
					'600'       => __( 'Semi Bold 600', 'buddyboss-theme' ),
					'300italic' => __( 'Light 300 Italic', 'buddyboss-theme' ),
					'400italic' => __( 'Normal 400 Italic', 'buddyboss-theme' ),
				),
			),
		);

		return $fonts;
	}

	add_filter( 'redux/buddyboss_theme_options/field/bb_typography/custom_fonts', 'buddyboss_theme_add_typography_field_default_fonts', 9 );
}

/**
 * Admin notice to update to BuddyBoss Platform 1.4.0 to fix fonts issues
 */
if ( ! function_exists( 'buddyboss_theme_plugin_update_notice' ) ) {
	function buddyboss_theme_plugin_update_notice() {
		if ( function_exists( 'buddypress' ) && version_compare( BP_PLATFORM_VERSION, '1.4.0', '<' ) ) {
			$class   = 'notice notice-error';
			$message = __( 'Please update BuddyBoss Platform to v1.4.0 to maintain compatibility with BuddyBoss Theme. Some icons in your theme will look wrong until you update.', 'buddyboss-theme' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}
	}
	add_action( 'admin_notices', 'buddyboss_theme_plugin_update_notice' );
}
