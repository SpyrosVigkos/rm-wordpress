# RM Headless

The ReelMetrics content model as a plugin, so it no longer depends on the Elementor theme. The Astro site reads this content through the REST API.

## What it registers

| File | Content |
| --- | --- |
| `includes/reelcast.php` | `reelcast` post type, `reelcast_category` and `reelcast_tag`, episode ACF fields, `episode_data` REST field, ReelCast settings page and default featured image |
| `includes/reelhot.php` | `reelhot` post type, ACF fields, ReelHot settings page and default featured image |
| `includes/texts.php` | `rm_text` post type (site text snippets) and its ACF fields |
| `includes/buying-guide.php` | `buying_guide_item`, `buying_guide_category`, their ACF fields and term ACF in REST |
| `includes/team-member.php` | `rm_team_member` post type and ACF fields |
| `includes/cpt-notification.php` | `notification` post type |
| `includes/cpt-comparison.php`, `acf-comparison.php` | `comparison` post type and its fields |
| `includes/admin-rm-auth.php`, `rm-auth-rest.php` | RM Auth settings page and `GET /wp-json/rm/v1/oidc-info` |
| `includes/preview.php`, `preview-editor.js` | Preview of ReelCast episodes on the Astro preview server (below) |

The files are copies of the theme code (`rm-elementor-theme/functions.php` and its `includes/`), with two changes:

- The ReelCast and ReelHot image helpers return an empty string as the last fallback instead of a theme image URL. The theme has no such image, so that URL was broken anyway.
- The ReelCast and ReelHot featured-image fallbacks read the stored `_thumbnail_id` before falling back. This is the fix that was uncommitted in `functions.php` on `init-wordpress-project` when the copy was made.

Not registered here: the help pane, `cfa_*` and `console_examples` types that CPT UI defines on staging. The help content moves to rm-rails Content Admin.

Left in the theme: the Elementor widgets and settings, the `[reelcast_player]` shortcode (front-end rendering), and the one-off notification migration page.

## Preview

Off unless `RM_PREVIEW_URL` is defined in `wp-config.php` (the local setup sets `http://localhost:4401`). When it is, pressing Preview on a ReelCast episode opens `RM_PREVIEW_URL/preview/reelcast/<id>?token=...` instead of the theme:

- The token is an HMAC of the post id and an expiry ten minutes ahead, keyed with the site's auth salt. `GET /wp-json/rm/v1/preview/<id>?token=...` checks it and answers with the latest autosave in the shape of a `wp/v2/reelcast` item, so the preview server needs no WordPress login.
- The block editor's autosave does not include ACF fields. `preview-editor.js` adds them to the autosave request, and they are saved onto the autosave revision only. The published post does not change until Update.
- The editor sometimes opens `?preview=true` on the post URL instead of the preview link (drafts, or a post without an autosave). WordPress checks the user may preview it and then redirects to the same preview link.

## With the Elementor theme

While `rm-elementor-theme` is active, the plugin does nothing and shows a notice: the theme already registers the same post types and field groups, and loading both would register them twice. So it is safe to install on staging next to the current theme; it only takes over once a theme without the content model is active (for example `rm-headless-theme`).

Until the Elementor theme is retired, a change to the content model has to be made in both places.

## Requirements

ACF Pro or Secure Custom Fields. Free ACF is not enough: the episode `topics` and `keywords` are repeater fields, and the settings pages are options pages. Staging has ACF Pro; the local setup uses Secure Custom Fields.

## Checked

On the local WordPress (`local/README.md`) the REST output was captured with the Elementor theme active, then with `rm-headless-theme` and this plugin. Post types, taxonomies, routes, all items of every type, terms and `rm/v1/oidc-info` are identical, apart from the Elementor post types and routes.

Against staging (GET only), every ACF value of the 47 episodes, 72 ReelHot entries and 92 texts matches, images aside (attachment ids differ). Secure Custom Fields also adds `<field>_source` metadata to the REST `acf` object, which ACF Pro on staging does not; readers should ignore those keys.
