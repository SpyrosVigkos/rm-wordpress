#!/usr/bin/env bash
# Replaces the local content with a selection from a full backup of the
# Elementor Cloud site (backups/database.sql.gz and backups/files.tar.gz).
#
# Kept, with their original IDs: users, pages, posts, the content types of the
# rm-headless plugin, the media library and its files, their terms, the ReelCast
# and ReelHot default images, and the RM Auth (Okta) settings.
# Left out: Elementor data and templates, the help pane, cfa_* and
# console_examples types (moving to rm-rails Content Admin), revisions, menus,
# comments and the tables of other plugins.
#
#   local/import-backup.sh [backup folder, default backups]
set -euo pipefail

cd "$(dirname "$0")/.."
BACKUP="${1:-backups}"
DB_DUMP="$BACKUP/database.sql.gz"
FILES="$BACKUP/files.tar.gz"
[ -f "$DB_DUMP" ] && [ -f "$FILES" ] || { echo "Need $DB_DUMP and $FILES" >&2; exit 1; }
set -a; . ./.env; set +a

# usermeta is a core table no plugin shares a suffix with, so it gives the prefix.
SRC=$(gzip -dc "$DB_DUMP" | sed -nE 's/^CREATE TABLE `([A-Za-z0-9_]+)usermeta`.*/\1/p')
[ -n "$SRC" ] && [ "$SRC" != "wp_" ] || { echo "Could not find a table prefix other than wp_ in $DB_DUMP" >&2; exit 1; }
echo "Backup tables use the prefix $SRC"

sql() { docker compose exec -T db mariadb -u wordpress -p"$DB_PASSWORD" wordpress "$@"; }
wp() { docker compose run --rm -T cli wp "$@"; }

echo "Loading the backup next to the local tables"
# DEFINER clauses need privileges the local user does not have; they only
# belong to views of plugins we do not keep.
gzip -dc "$DB_DUMP" | sed '/DEFINER=/d' | sql

TYPES="'page','post','reelcast','reelhot','rm_text','buying_guide_item','rm_team_member','notification','comparison'"
STATUSES="'publish','draft','pending','private','future'"
TAXONOMIES="'category','post_tag','buying_guide_category','reelcast_category','reelcast_tag'"

echo "Copying the selected content"
sql <<SQL
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM wp_term_relationships; DELETE FROM wp_term_taxonomy; DELETE FROM wp_termmeta; DELETE FROM wp_terms;
DELETE FROM wp_postmeta; DELETE FROM wp_posts; DELETE FROM wp_comments; DELETE FROM wp_commentmeta;
DELETE FROM wp_usermeta; DELETE FROM wp_users;

INSERT INTO wp_users SELECT * FROM ${SRC}users;
INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
  SELECT user_id, IF(meta_key LIKE '${SRC}%', CONCAT('wp_', SUBSTRING(meta_key, LENGTH('${SRC}') + 1)), meta_key), meta_value
  FROM ${SRC}usermeta
  WHERE meta_key NOT LIKE 'session_tokens' AND meta_key NOT LIKE 'elementor%' AND meta_key NOT LIKE '%_elementor%';

CREATE TEMPORARY TABLE kept_posts (ID BIGINT UNSIGNED PRIMARY KEY);
INSERT INTO kept_posts SELECT ID FROM ${SRC}posts WHERE post_type IN (${TYPES}) AND post_status IN (${STATUSES});
INSERT INTO kept_posts SELECT ID FROM ${SRC}posts WHERE post_type = 'attachment';

INSERT INTO wp_posts SELECT p.* FROM ${SRC}posts p JOIN kept_posts k ON k.ID = p.ID;
INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
  SELECT m.post_id, m.meta_key, m.meta_value FROM ${SRC}postmeta m JOIN kept_posts k ON k.ID = m.post_id
  WHERE m.meta_key NOT LIKE '\_elementor%' AND m.meta_key NOT LIKE 'tp\_widgets%'
    AND m.meta_key NOT IN ('_edit_lock', '_edit_last')
    AND NOT (m.meta_key = '_wp_page_template' AND m.meta_value LIKE 'elementor%');

INSERT INTO wp_term_taxonomy SELECT * FROM ${SRC}term_taxonomy WHERE taxonomy IN (${TAXONOMIES});
INSERT INTO wp_terms SELECT t.* FROM ${SRC}terms t WHERE t.term_id IN (SELECT term_id FROM wp_term_taxonomy);
INSERT INTO wp_termmeta (term_id, meta_key, meta_value)
  SELECT term_id, meta_key, meta_value FROM ${SRC}termmeta WHERE term_id IN (SELECT term_id FROM wp_terms);
INSERT INTO wp_term_relationships SELECT r.* FROM ${SRC}term_relationships r
  JOIN kept_posts k ON k.ID = r.object_id WHERE r.term_taxonomy_id IN (SELECT term_taxonomy_id FROM wp_term_taxonomy);

DELETE FROM wp_options WHERE option_name IN ('options_reelcast_default_image', '_options_reelcast_default_image',
  'options_reelhot_default_image', '_options_reelhot_default_image', 'rm_okta_auth');
INSERT INTO wp_options (option_name, option_value, autoload)
  SELECT option_name, option_value, autoload FROM ${SRC}options
  WHERE option_name IN ('options_reelcast_default_image', '_options_reelcast_default_image',
    'options_reelhot_default_image', '_options_reelhot_default_image', 'rm_okta_auth');
SET FOREIGN_KEY_CHECKS = 1;
SQL

echo "Removing the backup tables"
sql -N -e "SELECT CONCAT('DROP VIEW \`', table_name, '\`;') FROM information_schema.views WHERE table_schema = DATABASE() AND table_name LIKE '${SRC//_/\\_}%';
           SELECT CONCAT('DROP TABLE \`', table_name, '\`;') FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE' AND table_name LIKE '${SRC//_/\\_}%';" | sql

echo "Copying the uploads"
docker compose exec -T -u 0 wordpress sh -c 'rm -rf /var/www/html/wp-content/uploads && mkdir -p /var/www/html/wp-content/uploads'
gzip -dc "$FILES" | docker compose exec -T -u 0 wordpress \
  tar -x -C /var/www/html/wp-content/uploads --strip-components=3 --wildcards 'files/wp-content/uploads/*'
docker compose exec -T -u 0 wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads

if ! wp user get "$WP_ADMIN_USER" >/dev/null 2>&1; then
  wp user create "$WP_ADMIN_USER" admin@example.test --role=administrator --user_pass="$WP_ADMIN_PASSWORD" >/dev/null
fi
sed -i.bak '/^WP_APP_PASSWORD=/d' .env && rm -f .env.bak
echo "WP_APP_PASSWORD=$(wp user application-password create "$WP_ADMIN_USER" astro-local --porcelain)" >> .env

wp term recount category post_tag buying_guide_category >/dev/null
wp rewrite flush >/dev/null
wp cache flush >/dev/null

echo
echo "Imported:"
for type in page post reelcast reelhot rm_text buying_guide_item rm_team_member notification comparison attachment; do
  printf '  %-18s %s\n' "$type" "$(wp post list --post_type="$type" --post_status=any --format=count)"
done
printf '  %-18s %s\n' users "$(wp user list --format=count)"
