#!/bin/bash

# Set color variables for text formatting
YELLOW='\033[1;33m'

# Yellow color
CYAN='\033[0;36m'

# Cyan color
GREEN='\033[0;32m'

# Green color
NC='\033[0m'

# Prompt the user to enter Plugin Name (required)
read -rp "$(echo -e "${YELLOW}Plugin Name (required):${NC} ")" plugin_name

while [[ -z "$plugin_name" ]]; do
    read -rp "$(echo -e "${YELLOW}Plugin Name (required):${NC} ")" plugin_name
done

# Sanitize user input
plugin_name=$(echo "$plugin_name" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin Name is set to: [$plugin_name]${NC}"

# Prompt the user to enter Plugin URI (optional)
read -rp "Plugin URI (The home page of the plugin. Leave blank if not applicable): " plugin_uri

# Sanitize user input
plugin_uri=$(echo "$plugin_uri" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin URI is set to:  [$plugin_uri]${NC}"

# Prompt the user to enter Description (required)
read -rp "$(echo -e "${YELLOW}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description

# Sanitize user input
plugin_description=$(echo "$plugin_description" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin Descritpion is set to: [$plugin_description]${NC}"

while [[ -z "$plugin_description" ]]; do
    read -rp "$(echo -e "${YELLOW}Description (A short description of the plugin. Keep this description to fewer than 140 characters.) (required):${NC} ")" plugin_description
    plugin_description=$(echo "$plugin_description" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")

    # Sanitize user input
    echo -e "${CYAN}Plugin Descritpion is set to: [$plugin_description]${NC}"
done

# Prompt the user to enter Version with a default value of 0.0.1
read -rp "$(echo -e "${GREEN}Version (The current version number of the plugin) [default: 0.0.1]:${NC} ")" plugin_version

# Default Value
plugin_version=${plugin_version:-0.0.1}

# Sanitize user input
plugin_version=$(echo "$plugin_version" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin Version is set to: [$plugin_version]${NC}"

# Prompt the user to enter Requires at least with a default value of 6.0
read -rp "$(echo -e "${GREEN}Requires at least (The lowest WordPress version that the plugin will work on) [default: 6.0]:${NC} ")" requires_wordpress

# Default Value
requires_wordpress=${requires_wordpress:-6.0}

# Sanitize user input
requires_wordpress=$(echo "$requires_wordpress" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Requires Wordpress is set to: [$requires_wordpress]${NC}"

# Prompt the user to enter Requires PHP with a default value of 8.1
read -rp "$(echo -e "${GREEN}Requires PHP (The minimum required PHP version) [default: 8.1]:${NC} ")" requires_php

# Default Value
requires_php=${requires_php:-8.1}

# Sanitize user input
requires_php=$(echo "$requires_php" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Requires PHP is set to: [$requires_php]${NC}"

# Prompt the user to enter Author (optional)
read -rp "Author (The name of the plugin author. Leave blank if not applicable): " plugin_author

# Sanitize user input
plugin_author=$(echo "$plugin_author" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin author is set to: [$plugin_author]${NC}"

# Prompt the user to enter Author URI (optional)
read -rp "Author URI (The author's website or profile on another website. Leave blank if not applicable): " plugin_author_uri

# Sanitize user input
plugin_author_uri=$(echo "$plugin_author_uri" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin Author URI is set to: [$plugin_author_uri]${NC}"

# Prompt the user to enter License with a default value of GPL-3.0+
read -rp "$(echo -e "${GREEN}License (The short name (slug) of the plugin's license (e.g. GPLv2).) [default: GPL-3.0+]:${NC} ")" plugin_license

# Default Value
plugin_license=${plugin_license:-GPL-3.0+}

# Sanitize user input
plugin_license=$(echo "$plugin_license" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin License is set to: [$plugin_license]${NC}"

# Prompt the user to enter License URI (optional)
read -rp "License URI (A link to the full text of the license. Leave blank if not applicable): " license_uri

# Sanitize user input
license_uri=$(echo "$license_uri" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}License URI is set to: [$license_uri]${NC}"

# Prompt the user to enter Text Domain (required)
read -rp "$(echo -e "${YELLOW}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain

# Sanitize user input
plugin_textdomain=$(echo "$plugin_textdomain" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
echo -e "${CYAN}Plugin Text Domain is set to: [$plugin_textdomain]${NC}"

while [[ -z "$plugin_textdomain" ]]; do
    read -rp "$(echo -e "${YELLOW}Text Domain (The gettext text domain of the plugin.) (required):${NC} ")" plugin_textdomain

    # Sanitize user input
    plugin_textdomain=$(echo "$plugin_textdomain" | sed "s,\x1B\[[0-9;]*[a-zA-Z],,g")
    echo -e "${CYAN}Plugin Text Domain is set to: [$plugin_textdomain]${NC}"
done

# Prompt the user for input to replace WPPB with PHP namespace
read -rp "$(echo -e "${YELLOW}Enter PHP namespace (required): ${NC} ")" php_namespace

# Default Value
php_namespace=$(printf '%q\n' "$php_namespace")
echo -e "${CYAN}PHP namespace is set to: [$php_namespace]${NC}"

while [[ -z "$php_namespace" ]]; do
    read -rp "$(echo -e "${YELLOW}Enter PHP namespace (required): ${NC} ")" php_namespace

    php_namespace=$(printf '%q\n' "$php_namespace")
    echo -e "${CYAN}PHP namespace is set to: [$php_namespace]${NC}"
done

# Prompt the user for input plugin prefix
read -rp "$(echo -e "${YELLOW}Enter Plugin Prefix (No space, dashes or underscores) (required): ${NC} ")" plugin_prefix

# Default Value
plugin_prefix=$(echo "$plugin_prefix" | sed 's/^[_-]*//;s/[_-]*$//')
echo -e "${CYAN}PHP Prefix is set to: [$plugin_prefix]${NC}"

while [[ -z "$plugin_prefix" ]]; do
    read -rp "$(echo -e "${YELLOW}Enter Plugin Prefix (required): ${NC} ")" plugin_prefix
    plugin_prefix=$(echo "$plugin_prefix" | sed 's/^[_-]*//;s/[_-]*$//')
    echo -e "${CYAN}PHP Prefix is set to: [$plugin_prefix]${NC}"
done
