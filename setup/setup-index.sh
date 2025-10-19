#!/bin/bash

# This script updates the default plugin header in index.php with user-provided values.
# It's intended to be run after prompt-header.sh, which sets the required environment variables.

# Use a temporary file to avoid issues with in-place editing.
TMP_FILE=$(mktemp)

# Read index.php line by line and write the updated content to the temporary file.
while IFS= read -r line; do
    case "$line" in
        *\**" @package "*)
            # Ensure single backslashes for PHP namespace - should always be "MyName\Space"
            php_namespace_clean=$(echo "$php_namespace" | sed 's/\\\\/\\/g')
            echo " * @package $php_namespace_clean" >>"$TMP_FILE"
            ;;
        "namespace "*)
            # Ensure single backslashes for PHP namespace - should always be "MyName\Space"
            php_namespace_clean=$(echo "$php_namespace" | sed 's/\\\\/\\/g')
            echo "namespace $php_namespace_clean;" >>"$TMP_FILE"
            ;;
        *"Plugin Name:"*)
            echo " * Plugin Name:       $plugin_name" >>"$TMP_FILE"
            ;;
        *"Plugin URI:"*)
            if [[ -n "$plugin_uri" ]]; then
                echo " * Plugin URI:        $plugin_uri" >>"$TMP_FILE"
            fi
            ;;
        *"Description:"*)
            echo " * Description:       $plugin_description" >>"$TMP_FILE"
            ;;
        *"Version:"*)
            echo " * Version:           $plugin_version" >>"$TMP_FILE"
            ;;
        *"Requires at least:"*)
            echo " * Requires at least: $requires_wordpress" >>"$TMP_FILE"
            ;;
        *"Requires PHP:"*)
            echo " * Requires PHP:      $requires_php" >>"$TMP_FILE"
            ;;
        *"Author:"*)
            if [[ -n "$plugin_author" ]]; then
                echo " * Author:            $plugin_author" >>"$TMP_FILE"
            fi
            ;;
        *"Author URI:"*)
            if [[ -n "$plugin_author_uri" ]]; then
                echo " * Author URI:        $plugin_author_uri" >>"$TMP_FILE"
            fi
            ;;
        *"License:"*)
            echo " * License:           $plugin_license" >>"$TMP_FILE"
            ;;
        *"License URI:"*)
            if [[ -n "$license_uri" ]]; then
                echo " * License URI:       $license_uri" >>"$TMP_FILE"
            fi
            ;;
        *"Text Domain:"*)
            echo " * Text Domain:       $plugin_textdomain" >>"$TMP_FILE"
            ;;
        *)
            # Replace any occurrence of WPPB with namespace
            # Use printf to properly handle backslashes in replacement
            if [[ "$line" == *"WPPB"* ]]; then
                updated_line=$(printf '%s\n' "$line" | sed "s/WPPB/$php_namespace/g")
                echo "$updated_line" >>"$TMP_FILE"
            else
                echo "$line" >>"$TMP_FILE"
            fi
            ;;
    esac
done <index.php

# Overwrite the original file with the updated content and clean up.
mv "$TMP_FILE" index.php

echo "Plugin header setup completed. index.php has been updated."
