#!/bin/bash

# Prompt the user for input to generate the WordPress plugin header info
read -p "Enter plugin name: " plugin_name
read -p "Enter plugin URI: " plugin_uri
read -p "Enter plugin description: " plugin_description
read -p "Enter plugin version: " plugin_version
read -p "Enter plugin author: " plugin_author
read -p "Enter plugin author URI: " plugin_author_uri
read -p "Enter plugin license: " plugin_license

# Create a new .php file with the plugin header info and namespace
read -p "Enter plugin namespace: " plugin_namespace

cat > index.php <<EOF
<?php
/**
 * Plugin Name: $plugin_name
 * Plugin URI: $plugin_uri
 * Description: $plugin_description
 * Version: $plugin_version
 * Author: $plugin_author
 * Author URI: $plugin_author_uri
 * License: $plugin_license
 *
 * @package $plugin_namespace
 */

EOF

# Append the contents of setup.php file to the plugin file starting from line 27
if [ -f setup.php ]; then
  echo "Appending contents of setup.php (starting from line 27) to index.php."
  tail -n +27 setup.php >> index.php
else
  echo "No setup.php file found."
fi

# Display a message to indicate that the file has been created
echo "Created index.php file with the following contents:"
cat index.php

# Prompt the user for input to generate the .env.local file
read -p "Enter DEV_URL: " dev_url
read -p "Enter DEV_PROXY_URL: " dev_proxy_url
read -p "Enter DEV_PROXY_PORT: " dev_proxy_port

# Create a new .env.local file and add the variables as constants
cat > .env.local <<EOF
DEV_URL=$dev_url
DEV_PROXY_URL=$dev_proxy_url
DEV_PROXY_PORT=$dev_proxy_port
EOF

# Display a message to indicate that the file has been created
echo "Created .env.local file with the following contents:"
cat .env.local

# Replace "WPPB" with the plugin namespace in all .php files in the current directory, templates, and includes directories and all their subdirectories
find . -type f \( -name "*.php" -o -name "*.phtml" \) -exec sed -i "s/WPPB/$plugin_namespace/g" {} +

# Display a message to indicate that the string has been replaced
echo "Replaced WPPB with $plugin_namespace in all .php and .phtml files in the current directory, templates, and includes directories and all their subdirectories."
