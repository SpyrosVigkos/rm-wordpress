#!/usr/bin/env bash
# Sets up the local WordPress from scratch, or finishes a half-done setup.
# Safe to run again: every step checks before it changes anything.
#   local/setup.sh [path to rm-public-pages]
set -euo pipefail

cd "$(dirname "$0")/.."
PUBLIC_PAGES="${1:-../rm-public-pages}"
PORT="${WP_PORT:-8090}"
URL="http://localhost:${PORT}"

if [ ! -f .env ]; then
  {
    echo "DB_PASSWORD=$(openssl rand -hex 16)"
    echo "WP_ADMIN_USER=admin"
    echo "WP_ADMIN_PASSWORD=$(openssl rand -hex 12)"
    echo "WP_PORT=${PORT}"
  } > .env
  echo "Created .env with local-only passwords"
fi
set -a; . ./.env; set +a

mkdir -p local/import
for f in reelcast-wxr.xml reelcast-wxr-s04e08-s05e10.xml reelhot-wxr.xml; do
  if [ -f "$PUBLIC_PAGES/$f" ]; then cp "$PUBLIC_PAGES/$f" local/import/; fi
done
for f in migration-scripts/*-wxr.xml; do cp "$f" local/import/; done

docker compose up -d --wait db wordpress

wp() { docker compose run --rm -T cli wp "$@"; }

until wp db check >/dev/null 2>&1; do sleep 2; done

if ! wp core is-installed 2>/dev/null; then
  wp core install --url="$URL" --title="ReelMetrics (local)" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="admin@example.test" --skip-email
fi

# Secure Custom Fields: the wordpress.org fork of ACF with the Pro features
# (repeaters, options pages) that staging gets from ACF Pro.
wp plugin install secure-custom-fields wordpress-importer --activate
wp plugin activate rm-headless
wp theme activate rm-headless-theme
wp rewrite structure '/%postname%/' --hard
wp option update blog_public 0

if [ "$(wp post list --post_type=reelcast,reelhot --format=count)" = "0" ]; then
  for f in local/import/*.xml; do
    echo "Importing $(basename "$f")"
    wp import "/import/$(basename "$f")" --authors=create --skip=image_resize
  done
fi

if ! wp user application-password exists "$WP_ADMIN_USER" "astro-local" 2>/dev/null; then
  pass=$(wp user application-password create "$WP_ADMIN_USER" astro-local --porcelain)
  sed -i.bak '/^WP_APP_PASSWORD=/d' .env && rm -f .env.bak
  echo "WP_APP_PASSWORD=$pass" >> .env
fi

echo
echo "WordPress: $URL  (admin: $URL/wp-admin, credentials in .env)"
wp post list --post_type=reelcast,reelhot,rm_text,buying_guide_item --format=count | xargs echo "Imported posts:"
