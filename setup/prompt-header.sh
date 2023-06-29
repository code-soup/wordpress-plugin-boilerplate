#!/bin/bash

read -rp "Plugin Name (required): " plugin_name
while [[ -z "$plugin_name" ]]; do
    read -rp "Plugin Name (required): " plugin_name
done

read -rp "Plugin URI (The home page of the plugin, which should be a unique URL, preferably on your own website. This must be unique to your plugin. You cannot use a WordPress.org URL here.): " plugin_uri

read -rp "Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required): " plugin_description
while [[ -z "$plugin_description" ]]; do
    read -rp "Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required): " plugin_description
done

read -rp "Version (The current version number of the plugin) [default: 0.0.1]: " plugin_version
plugin_version=${plugin_version:-0.0.1}

read -rp "Requires at least (The lowest WordPress version that the plugin will work on) [default: 6.0]: " requires_wordpress
requires_wordpress=${requires_wordpress:-6.0}

read -rp "Requires PHP (The minimum required PHP version) [default: 7.4]: " requires_php
requires_php=${requires_php:-7.4}

read -rp "Author (The name of the plugin author. Multiple authors may be listed using commas.): " plugin_author
read -rp "Author URI (The author’s website or profile on another website, such as WordPress.org.): " plugin_author_uri
read -rp "License (The short name (slug) of the plugin’s license (e.g. GPLv2).) [default: GPL-3.0+]: " plugin_license
plugin_license=${plugin_license:-GPL-3.0+}

read -rp "License URI (A link to the full text of the license): " license_uri
read -rp "Text Domain (The gettext text domain of the plugin.) (required): " plugin_textdomain
while [[ -z "$plugin_textdomain" ]]; do
    read -rp "Text Domain (The gettext text domain of the plugin.) (required): " plugin_textdomain
done

# Prompt the user for input to replace WPPB with PHP namespace
read -rp "Enter PHP namespace (required): " php_namespace
while [[ -z "$php_namespace" ]]; do
    read -rp "Enter PHP namespace (required): " php_namespace
done
