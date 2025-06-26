#!/bin/bash


header_content="<?php
/**
 * Plugin main file.
 *
 * @package $php_namespace
 */

defined( 'ABSPATH' ) || die;

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

printf "%b" "$header_content" >index.php


echo "Plugin header setup completed."
