## Context

`Proger.Click` runs on **Bedrock** with WordPress `6.9.4` and PHP `8.4`. The site is a personal engineering blog that publishes notes on software development, server setup, infrastructure, and related technical workflows.

The main custom artifact is the `proger-blog` theme. It is the frontend layer for public content and the primary target for future custom block work.

The theme combines:

- classic PHP templates for page shells
- `theme.json` for theme settings and editor-facing token exposure
- custom Gutenberg blocks for reusable content features
- a compiled asset pipeline under `build/`

## Goals / Non-Goals

### Goals

- Provide a custom reading experience for long-form technical content.
- Keep the theme extensible so future custom blocks can be added without reworking the entire site shell.
- Centralize design tokens in one source and generate downstream theme assets from them.
- Keep page rendering predictable by using classic PHP templates for the main frontend shell.

### Non-Goals

- Do not convert the theme into a pure block theme with `templates/*.html` and Site Editor template parts.
- Do not rewrite the site as a headless or SPA architecture.
- Do not require full editor/frontend visual parity.
- Do not move the current interaction layer to Interactivity API stores unless a future change explicitly does so.

## Decisions

### D1. Frontend shell uses classic PHP templates

**Decision**: The shipped theme renders frontend pages through classic PHP templates such as `index.php`, `single.php`, `category.php`, `search.php`, `404.php`, `header.php`, and `footer.php`.

**Why**: This matches the current code, keeps rendering behavior explicit, and avoids forcing the site shell into block-template constraints.

**Consequence**: Future page-shell work should start from PHP templates first. Gutenberg blocks remain the extension mechanism for reusable content features.

### D2. The theme is token-driven from `src/tokens/design-tokens.json`

**Decision**: `src/tokens/design-tokens.json` is the canonical source for theme tokens.

`scripts/sync-tokens.mjs` generates:

- `theme.json`
- `src/styles/tokens-root.css`

`tailwind.config.js` reads the same token JSON directly.

**Why**: This keeps `theme.json`, runtime CSS variables, and Tailwind theme extensions aligned from one source.

**Consequence**: Generated files should not be maintained by hand.

### D3. The asset pipeline uses `@wordpress/scripts` plus Tailwind/PostCSS

**Decision**: The theme build is based on `@wordpress/scripts` with a custom `webpack.config.js`, Tailwind CSS, PostCSS, and Sass-backed block styles.

The build emits:

- `build/main.css`
- `build/interactivity-nav.js`
- `build/blocks/code/*`
- `build/blocks/toc/*`

**Why**: This provides a WordPress-native block build flow while still allowing a custom global stylesheet and explicit block asset outputs.

**Consequence**: `build/` is a required runtime artifact and must exist for the theme to work correctly.

### D4. Reusable content behavior lives in custom blocks

**Decision**: Reusable technical-content features are implemented as custom blocks rather than hard-coded template fragments.

Current blocks:

- `proger-blog/code`
- `proger-blog/toc`

**Why**: This gives editors reusable content units while keeping page-shell rendering in classic templates.

**Consequence**: Future feature additions that belong inside post content should generally follow the existing block pattern.

### D5. The current interaction layer is plain DOM scripting

**Decision**: The shipped theme uses plain DOM-based JavaScript for header, mobile menu, TOC behavior, and code copy interactions.

This includes:

- `src/scripts/interactivity/nav.js`
- `src/blocks/code/view.js`
- `src/blocks/toc/view.js`

The package `@wordpress/interactivity` is installed, and the nav asset is registered as a script module, but the current implementation does not use directive-driven stores.

**Why**: This matches the current code and keeps the shipped behavior simple.

**Consequence**: OpenSpec and future work should not assume Interactivity API stores are already in use.

### D6. TOC behavior depends on a shared slug pipeline

**Decision**: Heading anchors and TOC links share one slug-generation path through `inc/toc-anchors.php`.

The flow is:

1. `the_content` injects heading IDs
2. the TOC block reuses shared heading extraction and slug logic
3. the TOC view script scrolls to those same generated IDs

**Why**: This keeps TOC links stable and avoids heading-link drift.

**Consequence**: Changes to TOC behavior must keep `inc/toc-anchors.php` and `src/blocks/toc/render.php` aligned.

### D7. Theme settings stay in the Customizer

**Decision**: Theme-level runtime toggles live in the WordPress Customizer.

Current toggles:

- background image
- enable TOC
- enable sidebar

Logo support uses the standard WordPress `custom-logo` theme support.

**Why**: These are site-level runtime display settings rather than content-level block attributes.

**Consequence**: Layout behavior and body classes must continue to respect theme mods.

## Architecture Summary

### PHP Layer

- `functions.php` bootstraps the theme
- `inc/` holds reusable PHP behavior
- classic templates render frontend shells

### Block Layer

- `src/blocks/code/` implements code presentation
- `src/blocks/toc/` implements heading navigation
- `inc/blocks.php` registers built block metadata from `build/blocks/*`

### Styling Layer

- `src/styles/main.css` is the global stylesheet entrypoint
- block-specific styles live beside block sources
- `src/styles/tokens-root.css` provides generated runtime CSS variables

### Build Layer

- `scripts/sync-tokens.mjs` manages token outputs
- `scripts/fix-module-deps.mjs` adjusts generated asset dependencies for script-module compatibility
- `webpack.config.js` defines extra entries and CSS output behavior

## Risks / Trade-offs

- The theme can be mistaken for a pure block theme because it uses `theme.json`, but its frontend shell is still classic PHP.
- Built blocks are registered from `build/`, so stale or missing build output can break runtime behavior.
- Prism highlighting depends on external CDN delivery.
- The block editor intentionally differs from the frontend shell because theme/global styles are filtered out of editor settings.

## Extension Guidance

When adding future capabilities:

- use PHP templates for page-shell changes
- use `inc/` for shared theme behavior
- use custom blocks for reusable editor-facing content features
- use the token pipeline for design-system changes

That is the intended long-term shape of the theme.
