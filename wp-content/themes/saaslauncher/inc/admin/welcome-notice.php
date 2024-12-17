<?php

/**
 * file for holding dashboard welcome page for theme
 */
if (! function_exists('saaslauncher_is_plugin_installed')) {
	function saaslauncher_is_plugin_installed($plugin_slug)
	{
		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug;
		return file_exists($plugin_path);
	}
}
if (! function_exists('saaslauncher_is_plugin_activated')) {
	function saaslauncher_is_plugin_activated($plugin_slug)
	{
		return is_plugin_active($plugin_slug);
	}
}
if (! function_exists('saaslauncher_welcome_notice')) :
	function saaslauncher_welcome_notice()
	{
		if (get_option('saaslauncher_dismissed_custom_notice')) {
			return;
		}
		global $pagenow;
		$current_screen = get_current_screen();

		if (is_admin()) {
			if ($current_screen->id !== 'dashboard' && $current_screen->id !== 'themes') {
				return;
			}
			if (is_network_admin()) {
				return;
			}
			if (! current_user_can('manage_options')) {
				return;
			}
			$theme = wp_get_theme();

			if (is_child_theme()) {
				$theme = wp_get_theme()->parent();
			}
			$saaslauncher_version = $theme->get('Version');

?>
			<div class="saaslauncher-admin-notice notice notice-info is-dismissible content-install-plugin theme-info-notice" id="saaslauncher-dismiss-notice">
				<div class="info-content">
					<div class="notice-holder">
						<h5><span class="theme-name"><span><?php esc_html_e('Welcome to SaasLauncher', 'saaslauncher'); ?></span></h5>
						<h2><?php esc_html_e('Start building your website with the most advanced WordPress theme ever! 🚀 ', 'saaslauncher'); ?></h2>
						</h3>
						<div class="notice-text">
							<p><?php esc_html_e('Please install and activate all recommended plugins to use 40+ advanced blocks, 200+ pre-built sections, and 10+ additional starter site demos. Enhance website building and launch your site within minutes with just a few clicks! - Cozy Addons, Cozy Essential Addons, Advanced Import.', 'saaslauncher'); ?></p>
						</div>
						<a href="#" id="install-activate-button" class="button admin-button info-button"><?php esc_html_e('Getting started with a single click', 'saaslauncher'); ?></a>
						<a href="<?php echo admin_url(); ?>themes.php?page=about-saaslauncher" class="button admin-button info-button"><?php esc_html_e('Explore SaasLauncher', 'saaslauncher'); ?></a>

					</div>
				</div>
				<div class="theme-hero-screens">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/inc/admin/images/theme_screen_img.png'); ?>" />
				</div>

			</div>
	<?php
		}
	}
endif;
add_action('admin_notices', 'saaslauncher_welcome_notice');
function saaslauncher_dismissble_notice()
{
	if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'saaslauncher_admin_nonce')) {
		wp_send_json_error(array('message' => 'Nonce verification failed.'));
		return;
	}

	$result = update_option('saaslauncher_dismissed_custom_notice', 1);

	if ($result) {
		wp_send_json_success();
	} else {
		wp_send_json_error(array('message' => 'Failed to update option'));
	}
}
add_action('wp_ajax_saaslauncher_dismissble_notice', 'saaslauncher_dismissble_notice');
// Hook into a custom action when the button is clicked
add_action('wp_ajax_saaslauncher_install_and_activate_plugins', 'saaslauncher_install_and_activate_plugins');
add_action('wp_ajax_nopriv_saaslauncher_install_and_activate_plugins', 'saaslauncher_install_and_activate_plugins');
add_action('wp_ajax_saaslauncher_rplugin_activation', 'saaslauncher_rplugin_activation');
add_action('wp_ajax_nopriv_saaslauncher_rplugin_activation', 'saaslauncher_rplugin_activation');

// Function to install and activate the plugins



function check_plugin_installed_status($pugin_slug, $plugin_file)
{
	return file_exists(ABSPATH . 'wp-content/plugins/' . $pugin_slug . '/' . $plugin_file) ? true : false;
}

/* Check if plugin is activated */


function check_plugin_active_status($pugin_slug, $plugin_file)
{
	return is_plugin_active($pugin_slug . '/' . $plugin_file) ? true : false;
}

require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
function saaslauncher_install_and_activate_plugins()
{
	if (! current_user_can('manage_options')) {
		return;
	}
	check_ajax_referer('saaslauncher_welcome_nonce', 'nonce');
	// Define the plugins to be installed and activated
	$recommended_plugins = array(
		array(
			'slug' => 'cozy-addons',
			'file' => 'cozy-addons.php',
			'name' => __('Cozy Blocks', 'saaslauncher'),
		),
		array(
			'slug' => 'advanced-import',
			'file' => 'advanced-import.php',
			'name' => __('Advanced Import', 'saaslauncher'),
		),
		array(
			'slug' => 'cozy-essential-addons',
			'file' => 'cozy-essential-addons.php',
			'name' => __('Cozy Essential Addons', 'saaslauncher'),
		),
		// Add more plugins here as needed
	);

	// Include the necessary WordPress functions

	// Set up a transient to store the installation progress
	set_transient('install_and_activate_progress', array(), MINUTE_IN_SECONDS * 10);

	// Loop through each plugin
	foreach ($recommended_plugins as $plugin) {
		$plugin_slug = $plugin['slug'];
		$plugin_file = $plugin['file'];
		$plugin_name = $plugin['name'];

		// Check if the plugin is active
		if (is_plugin_active($plugin_slug . '/' . $plugin_file)) {
			update_install_and_activate_progress($plugin_name, 'Already Active');
			continue; // Skip to the next plugin
		}

		// Check if the plugin is installed but not active
		if (is_saaslauncher_plugin_installed($plugin_slug . '/' . $plugin_file)) {
			$activate = activate_plugin($plugin_slug . '/' . $plugin_file);
			if (is_wp_error($activate)) {
				update_install_and_activate_progress($plugin_name, 'Error');
				continue; // Skip to the next plugin
			}
			update_install_and_activate_progress($plugin_name, 'Activated');
			continue; // Skip to the next plugin
		}

		// Plugin is not installed or activated, proceed with installation
		update_install_and_activate_progress($plugin_name, 'Installing');

		// Fetch plugin information
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin_slug,
				'fields' => array('sections' => false),
			)
		);

		// Check if plugin information is fetched successfully
		if (is_wp_error($api)) {
			update_install_and_activate_progress($plugin_name, 'Error');
			continue; // Skip to the next plugin
		}

		// Set up the plugin upgrader
		$upgrader = new Plugin_Upgrader();
		$install  = $upgrader->install($api->download_link);

		// Check if installation is successful
		if ($install) {
			// Activate the plugin
			$activate = activate_plugin($plugin_slug . '/' . $plugin_file);

			// Check if activation is successful
			if (is_wp_error($activate)) {
				update_install_and_activate_progress($plugin_name, 'Error');
				continue; // Skip to the next plugin
			}
			update_install_and_activate_progress($plugin_name, 'Activated');
		} else {
			update_install_and_activate_progress($plugin_name, 'Error');
		}
	}

	// Delete the progress transient
	$redirect_url = admin_url('themes.php?page=advanced-import');

	// Delete the progress transient
	delete_transient('install_and_activate_progress');
	// Return JSON response
	wp_send_json_success(array('redirect_url' => $redirect_url));
}

// Function to check if a plugin is installed but not active
function is_saaslauncher_plugin_installed($plugin_slug)
{
	$plugins = get_plugins();
	return isset($plugins[$plugin_slug]);
}

// Function to update the installation and activation progress
function update_install_and_activate_progress($plugin_name, $status)
{
	$progress   = get_transient('install_and_activate_progress');
	$progress[] = array(
		'plugin' => $plugin_name,
		'status' => $status,
	);
	set_transient('install_and_activate_progress', $progress, MINUTE_IN_SECONDS * 10);
}


function saaslauncher_dashboard_menu()
{
	add_theme_page(esc_html__('About SaasLauncher', 'saaslauncher'), esc_html__('About SaasLauncher', 'saaslauncher'), 'edit_theme_options', 'about-saaslauncher', 'saaslauncher_theme_info_display');
}
add_action('admin_menu', 'saaslauncher_dashboard_menu');
function saaslauncher_theme_info_display()
{

	?>
	<div class="dashboard-about-saaslauncher">
		<div class="saaslauncher-dashboard">
			<ul id="saaslauncher-dashboard-tabs-nav">
				<li><a href="#saaslauncher-welcome"><?php esc_html_e('Get Started', 'saaslauncher'); ?></a></li>
				<li><a href="#saaslauncher-setup"><?php esc_html_e('Setup Instruction', 'saaslauncher'); ?></a></li>
				<li><a href="#saaslauncher-comparision"><?php esc_html_e('FREE VS PRO', 'saaslauncher'); ?></a></li>
			</ul> <!-- END tabs-nav -->
			<div id="tabs-content">
				<div id="saaslauncher-welcome" class="tab-content">
					<h1> <?php esc_html_e('Welcome to the SaasLauncher', 'saaslauncher'); ?></h1>
					<p><?php esc_html_e('SaasLauncher is a cutting-edge Full Site Editing (FSE) WordPress theme tailored for SaaS companies, corporate businesses, and creative agencies. It comes packed with 50+ pre-built sections, 20+ integrated fonts, 15 pre-defined global styles, and pre-built layouts for the Homepage and Blog, along with templates for essential pages like About Us, Contact, Services, and Pricing. Designed for global reach, SaasLauncher supports popular languages such as Spanish, French, and English and is fully translatable into any language. With seamless design flexibility, one-click color scheme switching, and total creative control, it\'s perfect for crafting SaaS landing pages, corporate websites, personal portfolios, or any business niche. SaasLauncher empowers you to create a visually stunning website effortlessly and with precision. Explore SaasLauncher at https://cozythemes.com/saaslauncher-wordpress-theme/', 'saaslauncher'); ?></p>
					<h3><?php esc_html_e('Required Plugins', 'saaslauncher'); ?></h3>
					<p class="notice-text"><?php esc_html_e('Please install and activate all recommended plugin to import the demo with "one click demo import" feature and unlock premium features!(for pro version)', 'saaslauncher'); ?></p>
					<ul class="saaslauncher-required-plugin">
						<li>

							<h4><?php esc_html_e('1. Cozy Blocks', 'saaslauncher'); ?>
								<?php
								if (saaslauncher_is_plugin_activated('cozy-addons/cozy-addons.php')) {
									esc_html_e(': Plugin activated!', 'saaslauncher');
								} elseif (saaslauncher_is_plugin_installed('cozy-addons/cozy-addons.php')) {
									esc_html_e(': Plugin deactivated, please activate the plugin to use one click demo import and unlock premium features.', 'saaslauncher');
								} else {
									echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=cozy-addons&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'saaslauncher') . '</a>';
								}
								?>
							</h4>
						</li>
						<li>

							<h4><?php esc_html_e('2. Cozy Essential Addons', 'saaslauncher'); ?>
								<?php
								if (saaslauncher_is_plugin_activated('cozy-essential-addons/cozy-essential-addons.php')) {
									esc_html_e(': Plugin activated!', 'saaslauncher');
								} elseif (saaslauncher_is_plugin_installed('cozy-essential-addons/cozy-essential-addons.php')) {
									esc_html_e(': Plugin deactivated, please activate the plugin to use one click demo import and unlock premium features.', 'saaslauncher');
								} else {
									echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=cozy-essential-addons&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'saaslauncher') . '</a>';
								}
								?>
							</h4>
						</li>
						<li>
							<h4><?php esc_html_e('3. Advanced Import - Needed only to use "one click demo import" feature', 'saaslauncher'); ?>
								<?php
								if (saaslauncher_is_plugin_activated('advanced-import/advanced-import.php')) {
									esc_html_e(': Plugin activated!', 'saaslauncher');
								} elseif (saaslauncher_is_plugin_installed('advanced-import/advanced-import.php')) {
									esc_html_e(': Plugin deactivated, please activate the plugin to use one click demo import feature.', 'saaslauncher');
								} else {
									echo ': <a href="' . get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=advanced-import&TB_iframe=true&width=600&height=550">' . esc_html__('Install and Activate', 'saaslauncher') . '</a>';
								}
								?>
							</h4>
						</li>
					</ul>
					<a href="#" id="install-activate-button" class="button admin-button info-button"><?php esc_html_e('Getting started with a single click', 'saaslauncher'); ?></a>
				</div>
				<div id="saaslauncher-setup" class="tab-content">
					<h3 class="saaslauncher-baisc-guideline-header"><?php esc_html_e('Basic Theme Setup', 'saaslauncher'); ?></h3>
					<div class="saaslauncher-baisc-guideline">
						<div class="featured-box">
							<ul>
								<li><strong><?php esc_html_e('Setup Header Layout:', 'saaslauncher'); ?></strong>
									<ul>
										<li> - <?php esc_html_e('Go to Appearance -> Editor -> Patterns -> Template Parts -> Header:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('click on Header > Click on Edit (Icon) -> Add or Remove Required block/content as your requirement.:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on save to update your layout', 'saaslauncher'); ?></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="featured-box">
							<ul>
								<li><strong><?php esc_html_e('Setup Footer Layout:', 'saaslauncher'); ?></strong>
									<ul>
										<li> - <?php esc_html_e('Go to Appearance -> Editor -> Patterns -> Template Parts -> Footer:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('click on Footer > Click on Edit (Icon) > Add or Remove Required block/content as your requirement.:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on save to update your layout', 'saaslauncher'); ?></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="featured-box">
							<ul>
								<li><strong><?php esc_html_e('Setup Templates like Homepage/404/Search/Page/Single and more templates Layout:', 'saaslauncher'); ?></strong>
									<ul>
										<li> - <?php esc_html_e('Go to Appearance -> Editor -> Templates:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('click on Template(You need to edit/update) > Click on Edit (Icon) > Add or Remove Required block/content as your requirement.:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on save to update your layout', 'saaslauncher'); ?></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="featured-box">
							<ul>
								<li><strong><?php esc_html_e('Restore/Reset Default Content layout of Template(Like: Frontpage/Blog/Archive etc.)', 'saaslauncher'); ?></strong>
									<ul>
										<li> - <?php esc_html_e('Go to Appearance -> Editor -> Templates:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on Manage all Templates', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on 3 Dots icon at right side of respective Template', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on Clear Customization', 'saaslauncher'); ?></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="featured-box">
							<ul>
								<li><strong><?php esc_html_e('Restore/Reset Default Content layout of Template Parts(Header/Footer/Sidebar)', 'saaslauncher'); ?></strong>
									<ul>
										<li> - <?php esc_html_e('Go to Appearance -> Editor -> Patterns:', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on Manage All Template Parts', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on 3 Dots icon at right side of respective Template parts', 'saaslauncher'); ?></li>
										<li> - <?php esc_html_e('Click on Clear Customization', 'saaslauncher'); ?></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div id="saaslauncher-comparision" class="tab-content">
					<div class="featured-list">
						<div class="half-col free-features">
							<h3><?php esc_html_e('SaasLauncher Features (Free)', 'saaslauncher'); ?></h3>
							<ul>
								<li><strong> - <?php esc_html_e('SaasLauncher offers 30+ ready-to-use, pre-built sections, designed to streamline your SaaS website building experience.', 'saaslauncher'); ?></strong>
									<ul>
										<li><?php esc_html_e('Hero/Banner Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('About Us Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Services Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Featured Content Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Photo Gallery Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Featured Work Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Testimonial Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Call To Action Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('FAQ Section ', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Counter Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Latest Post Display Section ', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Brands Logo Showcase', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Team Showcase', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Profile Links Card', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Features Display Section', 'saaslauncher'); ?></li>
										<li><?php esc_html_e('Pricing Tables Section', 'saaslauncher'); ?></li>

									</ul>
								</li>

								<li> <strong>- <?php esc_html_e('FSE Templates Ready', 'saaslauncher'); ?></strong>
									<ul>
										<li> <?php esc_html_e('404 Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Archive Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Product Catalog Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Blank Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Front Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Blog Home Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Index Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Search Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Sitemap Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Left Sidebar Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Right sidebar page  Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Single Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Single Product Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Cart Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Checkout Page Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Left Sidebar Single Template', 'saaslauncher'); ?></li>
										<li> <?php esc_html_e('Full Width Single Template', 'saaslauncher'); ?></li>

									</ul>
								<li>
								<li><strong> - <?php esc_html_e('Fully Customizable Header Layout', 'saaslauncher'); ?></strong></li>
								<li> <strong>- <?php esc_html_e('Fully Customizable Footer Layout ', 'saaslauncher'); ?></strong></li>
								<li><strong> - <?php esc_html_e('12+ Beautiful Fonts Option', 'saaslauncher'); ?></strong></li>
								<li> <strong>- <?php esc_html_e('On Scroll Animation option', 'saaslauncher'); ?></strong></li>
								<li> <strong>- <?php esc_html_e('One Click Demo Import Features', 'saaslauncher'); ?></strong></li>
								<li> <strong>- <?php esc_html_e('Access Cozy Blocks with upto 20+ Advanced Blocks for FSE and Gutenberg Editor', 'saaslauncher'); ?></strong></li>
							</ul>
						</div>
						<div class="half-col pro-features">
							<h3><?php esc_html_e('Premium Features', 'saaslauncher'); ?></h3>
							<p><?php esc_html_e('Including all free features, SaasLauncher seamlessly integrates with Cozy Blocks, offering 40+ advanced blocks designed to elevate and empower your SaaS or corporate website. These blocks provide powerful capabilities, enabling you to integrate essential SaaS-specific features effortlessly. By combining these blocks with our ready-to-use patterns, you gain the flexibility to craft a professional and engaging online presence tailored specifically to SaaS and corporate needs.', 'saaslauncher'); ?></p>
							<h4><?php esc_html_e('10+ Additional Pre-built Sections, bringing the total to 40+ pre-built sections.', 'saaslauncher'); ?></h4>
							<ul>
								<li><?php esc_html_e('Hero Section with Video Popup', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Hero/Banner Slider Section', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Testimonial Carousel', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Team Carousel', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Service Grid', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Service Carousel', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Alternate Image Content Section', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Featured Content Section with Sticky Style', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('FAQ Accordion Section', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Pricing Table', 'saaslauncher'); ?></li>
							</ul>
							<h4><?php esc_html_e('40+ Advanced Blocks', 'saaslauncher'); ?></h4>
							<ul>
								<li><?php esc_html_e('Slider Block', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Counter Block', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Progress Bar Block', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Advanced Gallery with Lightbox, filterable and multiple layout', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Portfolio Block with Custom Post Type with lightbox, category filterable and multiple layout', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Team Block with grid and carousel', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Testimonial Block with grid and carousel', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('15 Post and Magazine Blocks', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('10 WooCommerce Blocks', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Social Shares Blocks', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Social Icons Blocks', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Breadcrumbs Block', 'saaslauncher'); ?></li>
								<li><?php esc_html_e('Popup builder Block to display offer and flash sale', 'saaslauncher'); ?>
									<?php esc_html_e('and access', 'saaslauncher'); ?> <a href="https://cozythemes.com/cozy-addons/" target="_blank"><?php esc_html_e('Cozy Blocks with more than 40+ advanced block.', 'saaslauncher'); ?></a>
								</li>

							</ul>
							<br />
							<a href="https://cozythemes.com/pricing-and-plans/" class="upgrade-btn button" target="_blank"><?php esc_html_e('Upgrade to Pro', 'saaslauncher'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
}
