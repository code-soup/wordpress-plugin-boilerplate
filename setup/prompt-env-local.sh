#!/bin/bash

# Prompt the user to enter WP_DEV_URL with a default value of "http://localhost"
read -rp "Your local development URL where WordPress is installed [default: http://localhost]: " wp_dev_url
wp_dev_url=${wp_dev_url:-http://localhost}

# Prompt the user to enter WP_CONTENT_PATH with a default value of "http://localhost"
read -rp "Path to WP plugins folder [default: /wp-content/plugins]: " wp_content_path
wp_content_path=${wp_content_path:-/wp-content/plugins}

# Prompt the user to enter DEV_PROXY_URL with a default value of "http://localhost"
read -rp "Enter DEV_PROXY_URL [default: http://localhost]: " dev_proxy_url
dev_proxy_url=${dev_proxy_url:-http://localhost}

# Prompt the user to enter DEV_PROXY_PORT with a default value of 3000
read -rp "Enter DEV_PROXY_PORT [default: 8080]: " dev_proxy_port
dev_proxy_port=${dev_proxy_port:-8080}

# Optional: override webpack publicPath (CDN, CI, custom asset host)
read -rp "WP_PUBLIC_PATH (optional, press Enter to skip): " wp_public_path

# Create a new .env.local file and add the variables as constants
{
	echo "WP_DEV_URL=$wp_dev_url"
	echo "WP_CONTENT_PATH=$wp_content_path"
	echo "DEV_PROXY_URL=$dev_proxy_url"
	echo "DEV_PROXY_PORT=$dev_proxy_port"
	if [[ -n "$wp_public_path" ]]; then
		echo "WP_PUBLIC_PATH=$wp_public_path"
	fi
} >.env.local
