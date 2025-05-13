<?php
/**
 * Plugin Name: Smart Scroll to Top
 * Description: A customizable smart scroll to top button with multiple options and styles.
 * Version: 1.0.0
 * Author: Titu
 * Author URI: https://github.com/titugithub
 * Text Domain: smart-scroll-to-top
 * Domain Path: /languages
 * License: GPL v2 or later
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) exit;

define('SSTT_VERSION', '1.0.0');
define('SSTT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSTT_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once SSTT_PLUGIN_DIR . 'includes/class-smart-scroll-to-top.php';
require_once SSTT_PLUGIN_DIR . 'includes/class-smart-scroll-to-top-admin.php';

function run_smart_scroll_to_top() {
    $plugin = new Smart_Scroll_To_Top();
    $plugin->run();
}
run_smart_scroll_to_top(); 