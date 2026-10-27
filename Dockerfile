# The ReelMetrics content WordPress: core, Secure Custom Fields, the
# rm-headless plugin and the headless theme, all fixed at build time.
# Settings come from environment variables (docker/wp-config.php); uploads
# are the only thing that needs storage. See docker/README.md.
FROM wordpress:6.8.3-php8.3-apache

ARG SCF_VERSION=6.9.7
ARG SCF_SHA256=5b10edab948f64b84d44d81a29fd5794a2573205e0d5e7ea9538024fc93ff52c
ARG WP_CLI_VERSION=2.12.0
ARG WP_CLI_SHA512=be928f6b8ca1e8dfb9d2f4b75a13aa4aee0896f8a9a0a1c45cd5d2c98605e6172e6d014dda2e27f88c98befc16c040cbb2bd1bfa121510ea5cdf5f6a30fe8832

# The official entrypoint copies /usr/src/wordpress into /var/www/html when a
# container starts, so everything WordPress runs is placed there.
WORKDIR /usr/src/wordpress

RUN set -eux; \
    rm -rf wp-content/plugins/akismet wp-content/plugins/hello.php wp-content/themes/twenty*; \
    curl -fsSL -o /tmp/scf.zip "https://downloads.wordpress.org/plugin/secure-custom-fields.${SCF_VERSION}.zip"; \
    echo "${SCF_SHA256}  /tmp/scf.zip" | sha256sum -c -; \
    php -r '$z = new ZipArchive(); exit($z->open("/tmp/scf.zip") === true && $z->extractTo("wp-content/plugins") ? 0 : 1);'; \
    rm /tmp/scf.zip; \
    curl -fsSL -o /usr/local/bin/wp "https://github.com/wp-cli/wp-cli/releases/download/v${WP_CLI_VERSION}/wp-cli-${WP_CLI_VERSION}.phar"; \
    echo "${WP_CLI_SHA512}  /usr/local/bin/wp" | sha512sum -c -; \
    chmod +x /usr/local/bin/wp

COPY docker/php.ini /usr/local/etc/php/conf.d/rm.ini
COPY docker/apache.conf /etc/apache2/conf-enabled/zz-rm.conf
COPY docker/wp-config.php docker/healthz.php docker/.htaccess ./
COPY docker/mu-plugins/ wp-content/mu-plugins/
COPY wp-content/plugins/rm-headless/ wp-content/plugins/rm-headless/
COPY wp-content/themes/rm-headless-theme/ wp-content/themes/rm-headless-theme/
RUN chown -R www-data:www-data /usr/src/wordpress

WORKDIR /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s \
    CMD curl -fsS -o /dev/null http://127.0.0.1/healthz.php || exit 1
