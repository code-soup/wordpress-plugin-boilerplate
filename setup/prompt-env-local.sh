#!/bin/bash

read -p "Enter DEV_URL: " dev_url
read -p "Enter DEV_PROXY_URL: " dev_proxy_url
read -p "Enter DEV_PROXY_PORT: " dev_proxy_port

# Create a new .env.local file and add the variables as constants
cat > .env.local <<EOF
DEV_URL=$dev_url
DEV_PROXY_URL=$dev_proxy_url
DEV_PROXY_PORT=$dev_proxy_port
EOF
