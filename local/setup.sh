#!/usr/bin/env bash
# Sets up the local WordPress from scratch, or finishes a half-done setup.
# Safe to run again: every step checks before it changes anything.
set -euo pipefail

cd "$(dirname "$0")/.."
PORT="${WP_PORT:-8090}"
URL="http://localhost:${PORT}"

touch .env
set_env() {
  grep -q "^$1=" .env || { echo "$1=$2" >> .env; echo "Added $1 to .env"; }
}
set_env DB_PASSWORD "$(openssl rand -hex 16)"
set_env WP_ADMIN_USER admin
set_env WP_ADMIN_PASSWORD "$(openssl rand -hex 12)"
set_env WP_PORT "$PORT"
for key in AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT; do
  set_env "WORDPRESS_$key" "$(openssl rand -hex 32)"
done
set -a; . ./.env; set +a

# --renew-anon-volumes: WordPress core is copied into the container when it
# starts, so a rebuilt image must not reuse the previous container's copy.
docker compose up -d --build --wait --renew-anon-volumes db wordpress

wp() { docker compose exec -T -u www-data wordpress wp "$@"; }

if ! wp core is-installed 2>/dev/null; then
  wp core install --url="$URL" --title="ReelMetrics (local)" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="admin@example.test" --skip-email
fi
wp rewrite structure '/%postname%/' >/dev/null
wp option update blog_public 0 >/dev/null

if [ "$(wp post list --post_type=reelcast,reelhot --format=count)" = "0" ]; then
  if [ -f backups/database.sql.gz ] && [ -f backups/files.tar.gz ]; then
    local/import-backup.sh
  else
    echo "No content yet: put a backup of the Elementor Cloud site in backups/ and run local/import-backup.sh"
  fi
fi

if ! wp user application-password exists "$WP_ADMIN_USER" "astro-local" 2>/dev/null; then
  pass=$(wp user application-password create "$WP_ADMIN_USER" astro-local --porcelain)
  sed -i.bak '/^WP_APP_PASSWORD=/d' .env && rm -f .env.bak
  echo "WP_APP_PASSWORD=$pass" >> .env
fi

echo
echo "WordPress: $URL  (admin: $URL/wp-admin, credentials in .env)"
