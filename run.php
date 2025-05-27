<?php

namespace WPPB;

// If this file is called directly, abort.
defined('ABSPATH') || die;

// Load composer autoloader for dependencies
require "vendor/autoload.php";

// Load our custom WordPress-compatible PSR-4 autoloader
require_once "includes/core/class-autoloader.php";

/**
 * Make main plugin class available via global function call.
 *
 * @since 1.0.0
 * @return Core\Init Main plugin instance.
 */
function plugin_instance(): Core\Init {
    return Core\Init::get_instance();
}

try {
    // Init plugin
    $plugin = plugin_instance();
    $plugin->set_constants([
        'MIN_WP_VERSION_SUPPORT_TERMS' => '__PLUGIN_MIN_WP_VERSION__',
        'MIN_WP_VERSION'               => '__PLUGIN_MIN_WP_VERSION__',
        'MIN_PHP_VERSION'              => '__PLUGIN_MIN_PHP_VERSION__',
        'MIN_MYSQL_VERSION'            => '__PLUGIN_MIN_MYSQL_VERSION__',
        'PLUGIN_PREFIX'                => '__PLUGIN_PREFIX__',
        'PLUGIN_NAME'                  => '__PLUGIN_NAME__',
        'PLUGIN_VERSION'               => '__PLUGIN_VERSION__',
    ]);

    $plugin->init();
} catch (\Throwable $e) {
    // Log the error
    if (function_exists('error_log')) {
        error_log('__PLUGIN_NAME__ Plugin Error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
    
    // Only show admin notice if user is an admin
    if (is_admin() && current_user_can('manage_options')) {
        add_action('admin_notices', function() use ($e) {
            ?>
            <div class="notice notice-error">
                <p><?php echo esc_html('__PLUGIN_NAME__ Plugin Error: ' . $e->getMessage()); ?></p>
            </div>
            <?php
        });
    }
}