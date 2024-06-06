<?php
//error_reporting(0);
/**
 * @wordpress-plugin
 * Plugin Name:       LeadTrail
 * Description:       Easily capture and sell leads by connecting forms from multiple third party sources.
 * Version:           1.1.0
 * Author:            GHAX
 * Author URI:        https://leadtrail.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leadtrail
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/* Plugin Name */
$lmPluginName = "LeadTrail";

/* Use Domain as the folder name */
$PluginTextDomain = "leadtrail";

/* Constant */
define('GHAX_VERSION', '1.1.0');
define('GHAX_LEADTRAIL_SLUG', 'leadtrail');
define('GHAX_LEADTRAIL_PLUGIN_DIR', plugin_dir_path(__DIR__)); //wp-content/plugins/

define('GHAX_LEADTRAIL_ABSPATH', plugin_dir_path(__FILE__));
define('GHAX_LEADTRAIL_RELPATH', trailingslashit(plugin_dir_url(__FILE__)));

define('GHAX_LICENSE_PURCHASE_URL', 'https://leadtrail.io');


/**
 * The code that runs during plugin activation.
 */
function activate_leadtrail_plugin()
{
  require_once plugin_dir_path(__FILE__) . 'includes/classes/GHAXlt-activate-class.php';
  leadtrail_Activator::activate_leadtrail();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_leadtrail_plugin()
{
  require_once plugin_dir_path(__FILE__) . 'includes/classes/GHAXlt-deactive-class.php';
  leadtrail_Deactivator::deactivate_leadtrail();
}

/* Register Hooks For Start And Deactivate */
register_activation_hook(__FILE__, 'activate_leadtrail_plugin');
register_deactivation_hook(__FILE__, 'deactivate_leadtrail_plugin');

require_once GHAX_LEADTRAIL_ABSPATH . 'admin/ghx-admin.php';
require_once GHAX_LEADTRAIL_ABSPATH . 'public/ghx-public.php';

new GHAX_Leadtrail_Admin();
new GHAX_Leadtrail_Public();
