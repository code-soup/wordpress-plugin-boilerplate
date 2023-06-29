#!/bin/bash

RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

read -rp "$(echo -e "${RED}Plugin Name (required):${NC} ")" plugin_name
while [[ -z "$plugin_name" ]]; do
    read -rp "$(echo -e "${RED}Plugin Name (required):${NC} ")" plugin_name
done

read -rp "Plugin URI (The home page of the plugin, which should be a unique URL, preferably on your own website. This must be unique to your plugin. You cannot use a WordPress.org URL here.): " plugin_uri

read -rp "$(echo -e "${RED}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description
while [[ -z "$plugin_description" ]]; do
    read -rp "$(echo -e "${RED}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description
done

read -rp "$(echo -e "${YELLOW}Version (The current version number of the plugin) [default: 0.0.1]:${NC} ")" plugin_version
plugin_version=${plugin_version:-0.0.1}

read -rp "$(echo -e "${YELLOW}Requires at least (The lowest WordPress version that the plugin will work on) [default: 6.0]:${NC} ")" requires_wordpress
requires_wordpress=${requires_wordpress:-6.0}

read -rp "$(echo -e "${YELLOW}Requires PHP (The minimum required PHP version) [default: 7.4]:${NC} ")" requires_php
requires_php=${requires_php:-7.4}

read -rp "Author (The name of the plugin author. Multiple authors may be listed using commas.): " plugin_author
read -rp "Author URI (The author’s website or profile on another website, such as WordPress.org.): " plugin_author_uri

read -rp "$(echo -e "${YELLOW}License (The short name (slug) of the plugin’s license (e.g. GPLv2).) [default: GPL-3.0+]:${NC} ")" plugin_license
plugin_license=${plugin_license:-GPL-3.0+}

read -rp "License URI (A link to the full text of the license): " license_uri

read -rp "$(echo -e "${RED}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain
while [[ -z "$plugin_textdomain" ]]; do
    read -rp "$(echo -e "${RED}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain
done

# Prompt the user for input to replace WPPB with PHP namespace
read -rp "$(echo -e "${RED}Enter PHP namespace (required):${NC} ")" php_namespace
while [[ -z "$php_namespace" ]]; do
    read -rp "$(echo -e "${RED}Enter PHP namespace (required):${NC} ")" php_namespace
done
