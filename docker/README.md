# WordPress image

One image for local and production: WordPress 6.8.3 (PHP 8.3, Apache), Secure Custom Fields 6.9.7, the `rm-headless` plugin, the `rm-headless-theme` theme and WP-CLI 2.12.0. Downloads are pinned and checked against their checksums.

```
make build push          # buildah, ECR repository "wordpress"
make deploy              # kubectl set image -n wordpress deployment/wordpress
```

## What is fixed in the image

- The code. `DISALLOW_FILE_MODS` is on, so nothing can be installed, updated or edited from wp-admin; a change is a new image.
- Which plugins and theme run (`mu-plugins/rm-platform.php`). A database copied from Elementor Cloud lists Elementor and the rest as active; only what the image ships is loaded, and the headless theme is always the theme.
- `.htaccess` (pretty URLs, `xmlrpc.php`, `wp-config.php`, `readme.html` and `license.txt` blocked), PHP limits (64 MB uploads, 256 MB memory) and Apache without version headers.
- XML-RPC, pings and comments are off.

The only state outside the database is `wp-content/uploads`, which needs a volume (or S3 later).

## Configuration

All from environment variables (`wp-config.php`). Each can also be given as `<NAME>_FILE`, the path to a file with the value, for mounted secrets.

| Variable | Required | Default | |
| --- | --- | --- | --- |
| `WORDPRESS_DB_PASSWORD` | yes | | |
| `WORDPRESS_DB_HOST`, `_NAME`, `_USER` | | `db`, `wordpress`, `wordpress` | |
| `WORDPRESS_AUTH_KEY` and the other 7 keys and salts | yes | | Changing them logs everyone out and invalidates preview links |
| `WORDPRESS_TABLE_PREFIX` | | `wp_` | |
| `WP_HOME` | | the `home` option | Public address, e.g. `https://cms.reelmetrics.com` |
| `WP_ENVIRONMENT_TYPE` | | `production` | |
| `RM_PREVIEW_URL` | | off | The Astro preview server |
| `WORDPRESS_DEBUG` | | off | `1` logs PHP notices to stderr |
| `WP_POST_REVISIONS` | | `20` | |

Without a required variable every page answers 503 and the log names the missing one.

## Health

`/healthz.php` answers 200 when the database is reachable and 503 when not, without loading WordPress. The Docker `HEALTHCHECK` uses it; Kubernetes should use it for readiness, and a TCP check on port 80 for liveness.

## Running WP-CLI

```
kubectl exec -n wordpress deploy/wordpress -- su -s /bin/sh www-data -c "wp post list --post_type=reelcast"
```

## Check with ops before the first deploy

The ECR repository `wordpress`, the namespace `wordpress` and a deployment named `wordpress` come from an earlier attempt on `main` (2024). They may already exist in the cluster, or need creating.
