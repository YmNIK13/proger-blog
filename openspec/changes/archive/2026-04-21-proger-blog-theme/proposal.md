## Why

`Proger.Click` needed a custom frontend theme for a personal engineering blog. The default WordPress theme did not provide the required reading experience for long technical posts, category browsing, code presentation, and table-of-contents navigation.

The shipped implementation centers that behavior in a dedicated theme, `web/app/themes/proger-blog`, so future UI work and future custom blocks can evolve in one place.

## What Changes

- Add the `proger-blog` theme under `web/app/themes/proger-blog/` as the main custom frontend artifact.
- Implement a classic PHP frontend shell with `theme.json`, a token sync workflow, and compiled assets under `build/`.
- Add two custom Gutenberg blocks:
  - `proger-blog/code`
  - `proger-blog/toc`
- Add shared theme helpers for archive cards, sidebar navigation, fallback menus, and TOC anchor generation.
- Add theme-level customizer options for background image, TOC visibility, and sidebar visibility.
- Add a build workflow using `@wordpress/scripts`, Tailwind CSS, PostCSS, and supporting Node scripts.
- Add TOC-focused Pest tests for slug generation and heading extraction logic.

## Capabilities

### New Capabilities

- `theme-design-tokens`: token-driven theme settings and generated runtime CSS variables
- `theme-layout`: classic PHP-rendered page shells for archives, search, single posts, and 404 pages
- `theme-navigation`: sticky header navigation, sidebar navigation, fallback menus, and mobile menu behavior
- `theme-article-card`: shared helper-rendered archive card presentation
- `theme-code-block`: custom code block with copy-to-clipboard and lazy Prism highlighting
- `theme-toc`: custom table of contents block with heading anchors, scroll-spy, and JSON-LD output
- `theme-pages`: themed archive, search, category, single-post, and 404 experiences
- `theme-settings`: customizer-driven frontend toggles and optional background image

### Modified Capabilities

- None. This change introduces a new custom theme surface rather than modifying an existing custom capability set.

## Impact

- **Code**: the main custom application layer now lives in `web/app/themes/proger-blog/`
- **Dependencies**: the theme adds a Node-based asset toolchain around `@wordpress/scripts`, Tailwind CSS, PostCSS, Sass, Stylelint, and Prism.js
- **Runtime**: the site frontend depends on compiled theme assets under `build/`
- **Compatibility**: WordPress `>= 6.9`, PHP `>= 8.4`, Node.js `>= 20` for local builds
- **Testing**: Pest covers TOC slug and heading extraction behavior
- **Rollback**: switch back to another installed theme, for example `twentytwentyfive`
