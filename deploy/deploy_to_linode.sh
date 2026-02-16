#!/usr/bin/env bash
set -euo pipefail

# Usage: sudo ./deploy_to_linode.sh <git_repo_url> <branch_or_tag>
# Example: sudo ./deploy_to_linode.sh git@github.com:you/temec.git main

REPO_URL=${1:-}
BRANCH=${2:-main}
APP_ROOT=/var/www/temec
WEB_USER=www-data
PHP_FPM_SOCK=/run/php/php8.1-fpm.sock

if [ -z "$REPO_URL" ]; then
  echo "Usage: sudo $0 <git_repo_url> [branch]"
  exit 1
fi

echo "Updating package lists..."
apt update

echo "Installing required system packages..."
apt install -y nginx git curl unzip zip php8.1 php8.1-fpm php8.1-mbstring php8.1-xml php8.1-zip php8.1-curl php8.1-gd php8.1-mysql php8.1-cli nodejs npm

# Install Composer if missing
if ! command -v composer >/dev/null 2>&1; then
  echo "Installing Composer..."
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
  chmod +x /usr/local/bin/composer
fi

# Clone or update project
if [ -d "$APP_ROOT" ]; then
  echo "Project exists, pulling latest..."
  cd "$APP_ROOT"
  git fetch --all
  git reset --hard "origin/$BRANCH"
  git checkout "$BRANCH"
else
  echo "Cloning project into $APP_ROOT"
  git clone --branch "$BRANCH" "$REPO_URL" "$APP_ROOT"
fi

cd "$APP_ROOT"

# Set up .env
if [ ! -f ".env" ]; then
  echo "Copying production example to .env"
  if [ -f ".env.production.example" ]; then
    cp .env.production.example .env
  else
    cp .env.example .env || true
  fi
fi

echo "Installing PHP dependencies (composer)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Installing Node dependencies and building assets (if package.json exists)..."
if [ -f package.json ]; then
  npm install --production
  npm run build || echo "npm run build failed or not defined"
fi

# Permissions
echo "Setting permissions..."
chown -R $WEB_USER:$WEB_USER "$APP_ROOT"
find "$APP_ROOT" -type f -exec chmod 644 {} \;
find "$APP_ROOT" -type d -exec chmod 755 {} \;
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
chown -R $WEB_USER:$WEB_USER storage bootstrap/cache

# Generate APP_KEY if missing
if ! grep -q "APP_KEY" .env || grep -q "APP_KEY=\s*$" .env; then
  php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force || echo "Migrations failed or none to run"

# Create storage link
php artisan storage:link || true

# Configure nginx site
NGINX_CONF=/etc/nginx/sites-available/temec
if [ ! -f "$NGINX_CONF" ]; then
  echo "Writing nginx configuration to $NGINX_CONF"
  cat > "$NGINX_CONF" <<'NGINX'
server {
    listen 8080 default_server;
    listen [::]:8080 default_server;
    server_name 139.162.155.251;
    root /var/www/temec/public;
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:$PHP_FPM_SOCK;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    client_max_body_size 50M;
}
NGINX
  ln -s "$NGINX_CONF" /etc/nginx/sites-enabled/temec
fi

# Test nginx and reload
nginx -t && systemctl reload nginx || { echo "Nginx reload failed"; nginx -t || true; }

# Ensure php-fpm running
systemctl restart php8.1-fpm || systemctl restart php-fpm || echo "php-fpm restart failed"

echo "Deploy script finished. Visit http://139.162.155.251:8080/ to verify."
