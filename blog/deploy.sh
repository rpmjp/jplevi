#!/usr/bin/env bash
# Deploys the blog on Hostinger shared hosting.
#
# The SSH shell defaults to PHP 8.2 while the web server runs 8.3, so the
# interpreter is named explicitly rather than left to the PATH.
set -euo pipefail

PHP=/opt/alt/php83/usr/bin/php
# The repository holds the whole site; the Laravel app is the blog directory
# inside it. Both sit outside public_html, so only public/ is ever reachable.
REPO=~/domains/jplevi.com/app
APP="$REPO/blog"

cd "$REPO"
git pull --ff-only
cd "$APP"
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
