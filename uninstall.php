<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Smart_Scroll_To_Top
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('sstt_options'); 