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

The files are copies of the theme code (`rm-elementor-theme/functions.php` and its `includes/`), with two changes:

- The ReelCast and ReelHot image helpers return an empty string as the last fallback instead of a theme image URL. The theme has no such image, so that URL was broken anyway.
- The ReelCast and ReelHot featured-image fallbacks read the stored `_thumbnail_id` before falling back. This is the fix that was uncommitted in `functions.php` on `init-wordpress-project` when the copy was made.

Left in the theme: the Elementor widgets and settings, the `[reelcast_player]` shortcode (front-end rendering), and the one-off notification migration page.

## With the Elementor theme

While `rm-elementor-theme` is active, the plugin does nothing and shows a notice: the theme already registers the same post types and field groups, and loading both would register them twice. So it is safe to install on staging next to the current theme; it only takes over once a theme without the content model is active (for example `rm-headless-theme`).

Until the Elementor theme is retired, a change to the content model has to be made in both places.

## Requirements

ACF Pro or Secure Custom Fields. Free ACF is not enough: the episode `topics` and `keywords` are repeater fields, and the settings pages are options pages. Staging has ACF Pro; the local setup uses Secure Custom Fields.

## Checked

On the local WordPress (`local/README.md`) the REST output was captured with the Elementor theme active, then with `rm-headless-theme` and this plugin. Post types, taxonomies, routes, all items of every type, terms and `rm/v1/oidc-info` are identical, apart from the Elementor post types and routes.

Against staging (GET only), every ACF value of the 47 episodes, 72 ReelHot entries and 92 texts matches, images aside (attachment ids differ). Secure Custom Fields also adds `<field>_source` metadata to the REST `acf` object, which ACF Pro on staging does not; readers should ignore those keys.
