#!/bin/bash
# Setup PHP 8.3, Composer, Nginx, MySQL for Laravel on Ubuntu 24.04
set -e
export DEBIAN_FRONTEND=noninteractive

# Add PHP PPA for 8.3
add-apt-repository -y ppa:ondrej/php 2>/dev/null || true
apt-get update -y

# Install PHP 8.3 and Laravel-required extensions
apt-get install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-curl \
  php8.3-mbstring php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl php8.3-opcache

# Install Composer
if ! command -v composer &>/dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Install Nginx and MySQL
apt-get install -y nginx mysql-server

# Enable and start services
systemctl enable nginx php8.3-fpm mysql
systemctl start php8.3-fpm mysql
systemctl start nginx

echo "Server stack installed."
