# Local WordPress

A WordPress on http://localhost:8090 for building the headless setup (content model, preview, publish webhook) without touching the Elementor Cloud site that Nick edits.

It runs the production image (`Dockerfile`, see `docker/README.md`): no Elementor, the `rm-headless` plugin, the `rm-headless-theme` theme and Secure Custom Fields, the wordpress.org fork of ACF with the Pro features (repeaters, options pages) that staging has from ACF Pro. The plugin and theme folders are mounted over the image's copies, so edits show on reload; anything else needs a rebuild (`local/setup.sh` rebuilds).

## Start

Docker Desktop must be running.

```
local/setup.sh
```

The first run adds random local passwords and keys to `.env` (gitignored), builds the image, starts MariaDB and WordPress, installs WordPress and imports the content from `backups/` (below). Running it again only does what is missing, and rebuilds the image.

The admin login and an application password named `astro-local` (for the Astro site and the REST API) are in `.env`.

## Content from a full backup

The content comes from a backup of the Elementor Cloud site in `backups/` (`database.sql.gz` and `files.tar.gz`, gitignored: the database has the users and their password hashes). The first `local/setup.sh` imports it. To replace the local content with a newer backup later:

```
local/import-backup.sh            # or: local/import-backup.sh path/to/backup-folder
```

It loads the dump next to the local tables, copies a selection with the original IDs, drops the backup tables and copies the uploads:

- Kept: users, pages, posts, the plugin's content types (ReelCast, ReelHot, texts, buying guide, team, notifications, comparison) in every status except trash, the media library and its files, their categories and tags, the ReelCast and ReelHot default images and the RM Auth (Okta) settings. The local admin from `.env` is added again with a new application password.
- Left out: Elementor data and templates, the help pane, `cfa_*` and `console_examples` types (moving to rm-rails Content Admin), revisions, menus, comments and other plugins' tables.

This is the same selection the production migration will use.

## Daily use

```
docker compose up -d --renew-anon-volumes    # start
docker compose stop                           # stop, keeps the data
docker compose exec -u www-data wordpress wp plugin list   # any WP-CLI command
docker compose down -v                        # delete everything, then local/setup.sh again
```

## Not included

- Links inside the content keep pointing at wordpress.reelmetrics.com; the media library itself is served locally.
- The Elementor layouts of the pages. Pages come with their title and text only; the layouts live in the Astro site.

## With the Astro site

After an import, fill the fixed pages' fields once from the site's snapshot (in `rm-public-pages/site`):

```
WP_URL=http://localhost:8090 WP_USER=admin WP_APP_PASSWORD=<from .env> npm run seed:wp
```

Then `npm run dev:local` there runs the site on http://localhost:4401 with every page read from this WordPress. Saved changes show on reload, and Preview on an episode or a fixed page opens the Astro preview (`RM_PREVIEW_URL` in `docker-compose.yml`).
