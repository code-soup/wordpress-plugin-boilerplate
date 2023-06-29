#!/bin/bash

# Set color variables for text formatting
RED='\033[0;31m'    # Red color
GREEN='\033[0;32m'  # Green color
NC='\033[0m'       # No color

# Prompt the user to enter Plugin Name (required)
read -rp "$(echo -e "${RED}Plugin Name (required):${NC} ")" plugin_name
while [[ -z "$plugin_name" ]]; do
    read -rp "$(echo -e "${RED}Plugin Name (required):${NC} ")" plugin_name
done

# Prompt the user to enter Plugin URI (optional)
read -rp "Plugin URI (The home page of the plugin. Leave blank if not applicable): " plugin_uri

# Prompt the user to enter Description (required)
read -rp "$(echo -e "${RED}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description
while [[ -z "$plugin_description" ]]; do
    read -rp "$(echo -e "${RED}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description
done

# Prompt the user to enter Version with a default value of 0.0.1
read -rp "$(echo -e "${GREEN}Version (The current version number of the plugin) [default: 0.0.1]:${NC} ")" plugin_version
plugin_version=${plugin_version:-0.0.1}

# Prompt the user to enter Requires at least with a default value of 6.0
read -rp "$(echo -e "${GREEN}Requires at least (The lowest WordPress version that the plugin will work on) [default: 6.0]:${NC} ")" requires_wordpress
requires_wordpress=${requires_wordpress:-6.0}

# Prompt the user to enter Requires PHP with a default value of 7.4
read -rp "$(echo -e "${GREEN}Requires PHP (The minimum required PHP version) [default: 7.4]:${NC} ")" requires_php
requires_php=${requires_php:-7.4}

# Prompt the user to enter Author (optional)
read -rp "Author (The name of the plugin author. Leave blank if not applicable): " plugin_author

# Prompt the user to enter Author URI (optional)
read -rp "Author URI (The author’s website or profile on another website. Leave blank if not applicable): " plugin_author_uri

# Prompt the user to enter License with a default value of GPL-3.0+
read -rp "$(echo -e "${GREEN}License (The short name (slug) of the plugin’s license (e.g. GPLv2).) [default: GPL-3.0+]:${NC} ")" plugin_license
plugin_license=${plugin_license:-GPL-3.0+}

# Prompt the user to enter License URI (optional)
read -rp "License URI (A link to the full text of the license. Leave blank if not applicable): " license_uri

# Prompt the user to enter Text Domain (required)
read -rp "$(echo -e "${RED}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain
while [[ -z "$plugin_textdomain" ]]; do
    read -rp "$(echo -e "${RED}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain
done

# Prompt the user for input to replace WPPB with PHP namespace
read -rp "$(echo -e "${RED}Enter PHP namespace (required): ${NC} ")" php_namespace
while [[ -z "$php_namespace" ]]; do
    read -rp "$(echo -e "${RED}Enter PHP namespace (required): ${NC} ")" php_namespace
done

# Prompt the user for input plugin prefix
read -rp "$(echo -e "${RED}Enter Plugin Prefix (required): ${NC} ")" plugin_prefix
while [[ -z "$plugin_prefix" ]]; do
    read -rp "$(echo -e "${RED}Enter Plugin Prefix (required): ${NC} ")" plugin_prefix
done
