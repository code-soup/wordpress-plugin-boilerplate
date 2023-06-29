#!/bin/bash

read -rp "Enter DEV_URL [default: http://localhost]: " dev_url
dev_url=${dev_url:-http://localhost}

read -rp "Enter DEV_PROXY_URL [default: http://localhost]: " dev_proxy_url
dev_proxy_url=${dev_proxy_url:-http://localhost}

read -rp "Enter DEV_PROXY_PORT [default: 3000]: " dev_proxy_port
dev_proxy_port=${dev_proxy_port:-3000}

# Create a new .env.local file and add the variables as constants
cat > .env.local <<EOF
DEV_URL=$dev_url
DEV_PROXY_URL=$dev_proxy_url
DEV_PROXY_PORT=$dev_proxy_port
EOF
