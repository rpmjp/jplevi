#!/usr/bin/env bash
# Deploys the blog on Hostinger shared hosting.
#
# The SSH shell defaults to PHP 8.2 while the web server runs 8.3, so the
# interpreter is named explicitly rather than left to the PATH.
set -euo pipefail

PHP=/opt/alt/php83/usr/bin/php
APP=~/domains/jplevi.com/blog-app

cd "$APP"

git pull --ff-only
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction

$PHP artisan down --render="errors::503" || true
$PHP artisan migrate --force
$PHP artisan storage:link || true

# Config and routes are cached for speed; views are compiled ahead of time.
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

# Assets change filename on every build, and the page cache holds HTML that
# names the old one. Skipping this serves a completely unstyled site.
$PHP artisan cache:clear

$PHP artisan up
echo "Deployed."
