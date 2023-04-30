#!/bin/bash

# Prompt the user for input to generate the WordPress plugin header info
read -p "Enter plugin name: " plugin_name
read -p "Enter plugin URI: " plugin_uri
read -p "Enter plugin description: " plugin_description
read -p "Enter plugin version: " plugin_version
read -p "Enter plugin author: " plugin_author
read -p "Enter plugin author URI: " plugin_author_uri
read -p "Enter plugin license: " plugin_license

# Create a new .php file with the plugin header info
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
 */
EOF

# Remove the PHP namespace from the plugin file
sed -i '' '/^namespace/d' index.php

# Display a message to indicate that the file has been created
echo "Plugin setup completed, data saved to: index.php"

# Append the contents of setup.php file to the plugin file starting from line 27
if [ -f setup.php ]; then
  tail -n +27 setup.php >> index.php
else
  echo "No setup.php file found."
fi

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

# Prompt the user for input to replace WPPB with PHP namespace
read -p "Enter PHP namespace: " php_namespace

# Replace the old PHP namespace with the new one in all .php and .phtml files in the current directory, templates, and includes directories and all their subdirectories
# Define the directories to scan
directories=("./templates"
             "./includes")

# Loop over the directories and scan for .php files
for dir in "${directories[@]}"; do
  find "$dir" -name "*.php" ! -path "./vendor/*" | while read filename; do
    sed -i '' "s/WPPB/$php_namespace/g" "$filename"
  done
done

# Run replacement in run.php file in current directory
sed -i '' "s/WPPB/$php_namespace/g" "./run.php"

echo "Setup completed."
