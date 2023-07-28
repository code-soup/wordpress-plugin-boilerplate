#!/bin/bash

# Delete index.php file if it exists
if [[ -f "index.php" ]]; then
  rm index.php
fi

header_content="<?php

defined('WPINC') || die;

/**
 * Plugin Name: $plugin_name"

if [[ -n "$plugin_uri" ]]; then
    header_content+="\n * Plugin URI: $plugin_uri"
fi

header_content+="\n * Description: $plugin_description
 * Version: $plugin_version
 * Requires at least: $requires_wordpress
 * Requires PHP: $requires_php"

if [[ -n "$plugin_author" ]]; then
    header_content+="\n * Author: $plugin_author"
fi

if [[ -n "$plugin_author_uri" ]]; then
    header_content+="\n * Author URI: $plugin_author_uri"
fi

header_content+="\n * License: $plugin_license"

if [[ -n "$license_uri" ]]; then
    header_content+="\n * License URI: $license_uri"
fi

header_content+="\n * Text Domain: $plugin_textdomain
 */"

printf "%b" "$header_content" > index.php

# NOTE: Activation hooks need to be inside index.php file or it might not work properly
# It can fail without error, WordPress is silently failing in case of error

# The code that runs during plugin activation.
# - includes/Activator.php
activation_hook_content="\n
register_activation_hook( __FILE__, function() {

    // On activate do this
    \\$php_namespace\Activator::activate();
});"

printf "%b" "$activation_hook_content" >> index.php

# The code that runs during plugin deactivation.
# - includes/Deactivator.php
deactivation_hook_content="\n
register_deactivation_hook( __FILE__, function () {
    
    // On deactivate do that
    \\$php_namespace\Deactivator::deactivate();
});"

printf "%b" "$deactivation_hook_content" >> index.php

# Run plugin, run
run_content="include \"run.php\";"

printf "%b" "$run_content" >> index.php

echo "index.php setup completed."