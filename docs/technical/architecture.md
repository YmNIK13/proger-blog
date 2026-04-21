# `proger-blog` Theme Architecture

## Purpose

`proger-blog` is the main custom artifact in this repository. It powers the frontend of [https://proger.click/](https://proger.click/), a personal blog for engineering notes, development guides, server setup instructions, and infrastructure-related content.

This document describes the theme as a standalone technical artifact so it can be extended safely over time, especially with new custom blocks.

## Role Inside The Repository

The repository is a **Bedrock**-based **WordPress** site, but most custom product behavior lives in:

- `web/app/themes/proger-blog`

Repository-level configuration still matters, but theme work is the primary path for UI, content presentation, and future editor-facing features.

## Runtime Model

The theme uses a mixed model:

- classic PHP templates for page rendering
- `theme.json` for token-driven editor and theme settings
- custom dynamic Gutenberg blocks for reusable content features
- compiled assets under `build/`

It is therefore not a pure block theme. For most frontend changes:

- page shell work starts in PHP templates
- reusable content features start as blocks

## Main Files And Directories

```text
web/app/themes/proger-blog/
├─ build/                      # compiled runtime assets and built blocks
├─ inc/                        # PHP support modules
├─ scripts/                    # token sync and build helper scripts
├─ src/
│  ├─ blocks/                  # custom Gutenberg block sources
│  ├─ scripts/interactivity/   # frontend JS modules
│  ├─ styles/                  # global stylesheet entry and generated token vars
│  └─ tokens/                  # canonical design token source
├─ functions.php               # theme bootstrap
├─ header.php
├─ footer.php
├─ index.php
├─ single.php
├─ category.php
├─ search.php
├─ 404.php
├─ sidebar.php
├─ theme.json
├─ style.css
└─ webpack.config.js
```

## Theme Bootstrap

`functions.php` is intentionally small. It defines theme constants and loads support modules from `inc/`:

- `theme-support.php`
- `assets.php`
- `helpers.php`
- `toc-anchors.php`
- `blocks.php`
- `customizer.php`
- `theme-mods-output.php`

This is the main internal architecture rule for PHP code: reusable logic belongs in `inc/`, not directly in template files.

## PHP Module Responsibilities

### `inc/theme-support.php`

Owns:

- theme supports
- textdomain loading
- image sizes
- menu registration
- excerpt defaults

### `inc/assets.php`

Owns:

- enqueueing `build/main.css`
- registering `build/interactivity-nav.js`
- reading generated asset metadata from `*.asset.php`
- `autoptimize` exclusions for module-like assets
- removal of theme/global styles from block editor settings

Important invariant:

- the editor intentionally does not try to mirror the full frontend shell

### `inc/helpers.php`

Owns:

- article card rendering
- archive card code previews
- sidebar icon mapping
- fallback menu rendering
- custom nav walkers

### `inc/toc-anchors.php`

Owns:

- heading slug generation
- heading extraction from rendered HTML
- injection of `id` attributes into `h1` to `h4` through `the_content`

This module is tightly coupled to the TOC block and should be treated as part of the same subsystem.

### `inc/blocks.php`

Owns block registration from:

- `build/blocks/code`
- `build/blocks/toc`

Important invariant:

- blocks are registered from `build/`, not from `src/`

### `inc/customizer.php` and `inc/theme-mods-output.php`

Own:

- theme-level settings
- sanitization
- body classes driven by theme mods
- inline CSS variables for the optional background image

Current runtime toggles:

- background image
- enable TOC
- enable sidebar

## Frontend Rendering Surfaces

### Archive-like templates

`index.php`, `category.php`, and `search.php` share the same pattern:

- header
- optional sidebar
- main content grid
- article cards
- pagination

Archive cards are rendered through `proger_blog_render_article_card()` so list presentation stays consistent across templates.

### Single post template

`single.php` adds the most theme-specific behavior:

- breadcrumbs
- post metadata
- featured image
- styled article content
- optional TOC rail
- mobile floating TOC panel

The TOC is injected with `do_blocks()`, which lets the theme use a block inside a classic template.

## Custom Blocks

The theme currently ships two custom blocks.

### `proger-blog/code`

Source:

- `src/blocks/code/block.json`
- `src/blocks/code/edit.js`
- `src/blocks/code/render.php`
- `src/blocks/code/view.js`
- `src/blocks/code/style.scss`
- `src/blocks/code/editor.scss`

Responsibilities:

- render code examples with language and filename metadata
- provide copy-to-clipboard behavior
- lazy-load Prism highlighting on the frontend

### `proger-blog/toc`

Source:

- `src/blocks/toc/block.json`
- `src/blocks/toc/edit.js`
- `src/blocks/toc/render.php`
- `src/blocks/toc/view.js`
- `src/blocks/toc/style.scss`

Responsibilities:

- build a table of contents from post headings
- keep links aligned with injected heading IDs
- manage scroll-spy state
- render JSON-LD `TableOfContents`

## Adding A New Block

Use the existing `code` and `toc` blocks as the reference pattern.

Recommended flow:

1. Create `src/blocks/<name>/`
2. Add `block.json`
3. Add `edit.js`
4. Add `render.php` if the block is dynamic
5. Add `view.js` if frontend behavior is needed
6. Add `style.scss` and editor styles when needed
7. Update `webpack.config.js` if explicit entries are required
8. Run `npm run build`
9. Confirm the block exists under `build/blocks/<name>/`
10. Register it from `inc/blocks.php` if needed

## Styling And Tokens

The canonical design token source is:

- `src/tokens/design-tokens.json`

From that file:

- `scripts/sync-tokens.mjs` generates `theme.json`
- `scripts/sync-tokens.mjs` generates `src/styles/tokens-root.css`
- `tailwind.config.js` reads the same token JSON directly

Important invariants:

- do not hand-edit `theme.json`
- do not hand-edit `src/styles/tokens-root.css`

The global stylesheet entrypoint is:

- `src/styles/main.css`

The stack here is:

- **Tailwind CSS**
- **PostCSS**
- generated CSS variables for cases where utility classes are not enough

## Build Pipeline

The build uses `@wordpress/scripts` with a custom `webpack.config.js`.

Key outputs:

- `build/main.css`
- `build/interactivity-nav.js`
- `build/blocks/code/*`
- `build/blocks/toc/*`

Important invariant:

- the theme requires `build/` artifacts to function correctly

Without a fresh build, blocks, styles, and frontend scripts may not load correctly.

## External Dependencies

At runtime the theme depends on external services for some presentation features:

- **Google Fonts** for `Inter` and `JetBrains Mono`
- **Material Symbols** for icons
- **jsDelivr** for Prism core and Prism language modules

The site still renders without them, but code highlighting and some visual polish degrade.

## High-Risk Invariants

- `inc/toc-anchors.php` and `src/blocks/toc/render.php` must stay aligned on heading slug behavior
- built blocks must exist under `build/blocks/*`
- token changes must flow through `src/tokens/design-tokens.json`
- `autoptimize` exclusions in `inc/assets.php` must stay aligned with module-style assets

## Practical Rule

Treat `proger-blog` as a layered theme artifact:

- PHP templates for page shells
- `inc/` modules for shared logic
- custom blocks for reusable editor-facing content
- token pipeline for visual system changes

That model is the safest base for adding future blocks without forcing a full theme rewrite.
