# Local WordPress

A WordPress on http://localhost:8090 for building the headless setup (content model, preview, publish webhook) without touching the Elementor Cloud site that Nick edits.

No Elementor: it runs `rm-headless-theme` (a placeholder theme, WordPress only holds content) and the `rm-headless` plugin, which registers the post types, ACF fields and REST fields. Both folders are mounted live, so edits show on reload. Field support comes from Secure Custom Fields, the wordpress.org fork of ACF with the Pro features (repeaters, options pages) that staging has from ACF Pro.

## Start

Docker Desktop must be running.

```
local/setup.sh
```

The first run creates `.env` (gitignored) with random local passwords, starts MariaDB and WordPress, installs WordPress, Secure Custom Fields and the importer, activates the theme and plugin, and imports the WXR exports: the ReelCast and ReelHot files from `../rm-public-pages` (or the path you pass) and `migration-scripts/*-wxr.xml`. Running it again only does what is missing.

The admin login and an application password named `astro-local` (for the Astro site and the REST API) are in `.env`.

## Content from a full backup

With a backup of the Elementor Cloud site in `backups/` (`database.sql.gz` and `files.tar.gz`, gitignored: the database has the users and their password hashes), the first `local/setup.sh` imports from it instead of the WXR files. To replace the local content with a newer backup later:

```
local/import-backup.sh            # or: local/import-backup.sh path/to/backup-folder
```

It loads the dump next to the local tables, copies a selection with the original IDs, drops the backup tables and copies the uploads:

- Kept: users, pages, posts, the plugin's content types (ReelCast, ReelHot, texts, buying guide, team, notifications, comparison) in every status except trash, the media library and its files, their categories and tags, the ReelCast and ReelHot default images and the RM Auth (Okta) settings. The local admin from `.env` is added again with a new application password.
- Left out: Elementor data and templates, the help pane, `cfa_*` and `console_examples` types (moving to rm-rails Content Admin), revisions, menus, comments and other plugins' tables.

This is the same selection the production migration will use.

## Daily use

```
docker compose up -d                          # start
docker compose stop                           # stop, keeps the data
docker compose run --rm cli wp plugin list    # any WP-CLI command
docker compose down -v                        # delete everything, then local/setup.sh again
```

## Not included

- Without a backup, most images: only the 13 attachments in `reelcast-wxr-s04e08-s05e10.xml` are imported.
- Links inside the content keep pointing at wordpress.reelmetrics.com; the media library itself is served locally.
- Pages built in Elementor on staging (home, services and the rest). They are not in the WXR files, and in the headless setup they become ACF-based pages.

## With the Astro site

In `rm-public-pages/site`, `npm run dev:local` runs the site on http://localhost:4401 against this WordPress. Saved changes show there on reload, and Preview in the episode editor opens the Astro preview (`RM_PREVIEW_URL` in `docker-compose.yml`).
