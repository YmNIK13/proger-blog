# Debugging quick routes

## Plugin doesn’t load / fatal errors

- Confirm correct plugin main file and header.
- Check PHP error logs and `WP_DEBUG_LOG`.
- If the repo is a site repo, confirm you edited the correct plugin under `web/app/plugins/` or `web/app/mu-plugins/` in Bedrock, or `wp-content/plugins/` in a classic layout.

## Activation hook surprises

- Hooks must be registered at top-level.
- Activation runs in a special context; avoid assuming other hooks already ran.

## Settings not saving

- Confirm `register_setting()` is called.
- Confirm the option group matches the form.
- Confirm capability checks and nonces.
