#!/bin/bash
set -e

content_dir="/app/web/app"
runtime_root="/app/var/wp-content-root"
wpsc_dir="$content_dir/plugins/wp-super-cache"
wpsc_advanced_cache_dist="$wpsc_dir/advanced-cache.php"
wpsc_config_sample="$wpsc_dir/wp-cache-config-sample.php"

if [ -d "$content_dir" ]; then
    mkdir -p "$runtime_root"
    mkdir -p \
        "$content_dir/cache/autoptimize" \
        "$content_dir/uploads" \
        "$content_dir/languages" \
        "$content_dir/upgrade" \
        "$content_dir/upgrade-temp-backup/plugins" \
        "$content_dir/upgrade-temp-backup/themes"

    touch "$runtime_root/advanced-headers.php"

    if [ -f "$wpsc_advanced_cache_dist" ]; then
        if [ ! -s "$runtime_root/advanced-cache.php" ] || ! grep -Eq 'WP SUPER CACHE (0\.8\.9\.1|1\.2)' "$runtime_root/advanced-cache.php"; then
            cp "$wpsc_advanced_cache_dist" "$runtime_root/advanced-cache.php"
        fi
    else
        touch "$runtime_root/advanced-cache.php"
    fi

    if [ -f "$wpsc_config_sample" ]; then
        if [ ! -s "$runtime_root/wp-cache-config.php" ] || ! grep -q "WPCACHEHOME" "$runtime_root/wp-cache-config.php"; then
            cp "$wpsc_config_sample" "$runtime_root/wp-cache-config.php"
        fi
    else
        touch "$runtime_root/wp-cache-config.php"
    fi

    ln -sfn "$runtime_root/advanced-cache.php" "$content_dir/advanced-cache.php"
    ln -sfn "$runtime_root/advanced-headers.php" "$content_dir/advanced-headers.php"
    ln -sfn "$runtime_root/wp-cache-config.php" "$content_dir/wp-cache-config.php"

    chown www-data:www-data "$content_dir"
    chown www-data:www-data "$runtime_root"
    chown -R www-data:www-data \
        "$content_dir/cache" \
        "$content_dir/uploads" \
        "$content_dir/languages" \
        "$content_dir/upgrade" \
        "$content_dir/upgrade-temp-backup"
    chown www-data:www-data \
        "$runtime_root/advanced-cache.php" \
        "$runtime_root/advanced-headers.php" \
        "$runtime_root/wp-cache-config.php"
    chown www-data:www-data \
        "$content_dir/advanced-cache.php" \
        "$content_dir/advanced-headers.php" \
        "$content_dir/wp-cache-config.php"

    chmod 775 \
        "$content_dir" \
        "$runtime_root" \
        "$content_dir/cache" \
        "$content_dir/cache/autoptimize" \
        "$content_dir/uploads" \
        "$content_dir/languages" \
        "$content_dir/upgrade" \
        "$content_dir/upgrade-temp-backup" \
        "$content_dir/upgrade-temp-backup/plugins" \
        "$content_dir/upgrade-temp-backup/themes"
    chmod 664 \
        "$runtime_root/advanced-cache.php" \
        "$runtime_root/advanced-headers.php" \
        "$runtime_root/wp-cache-config.php"
fi

exec /usr/local/bin/entrypoint "$@"
