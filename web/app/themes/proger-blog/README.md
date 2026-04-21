# Proger Blog Theme

`proger-blog` is the custom WordPress theme that powers the public presentation layer of [https://proger.click/](https://proger.click/).

Treat this theme as a standalone artifact inside the larger Bedrock site. It is the main place where future UI work and future custom blocks should be added.

## Responsibilities

The theme currently owns:

- the frontend page shell
- archive, search, category, and single-post presentation
- the custom code block
- the custom table of contents block
- design token integration
- frontend asset compilation
- theme-level customizer options

## Theme Model

This is not a pure block theme. The current model is:

- classic PHP templates for page rendering
- `theme.json` for design-system and editor settings
- dynamic custom blocks for reusable content features
- compiled assets under `build/`

That split is intentional and is the baseline for future work.

## Important Directories

```text
proger-blog/
├─ build/                      # compiled runtime assets
├─ inc/                        # PHP support modules
├─ src/blocks/                 # custom Gutenberg blocks
├─ src/scripts/interactivity/  # frontend JS modules
├─ src/styles/                 # global stylesheet entry and generated token vars
├─ src/tokens/                 # canonical design token source
├─ scripts/                    # Node helper scripts
├─ functions.php               # theme bootstrap
├─ header.php
├─ footer.php
├─ index.php
├─ single.php
├─ category.php
├─ search.php
├─ 404.php
└─ theme.json
```

## Commands

```bash
cd web/app/themes/proger-blog

npm ci
npm run build
npm run start
npm run sync:tokens
npm run sync:tokens:check
npm run lint:js
npm run lint:css
```

## Extension Points

If you want to add a new custom block, follow the existing block pattern:

1. Create `src/blocks/<name>/`
2. Add `block.json`
3. Add `edit.js`
4. Add `render.php` for dynamic rendering if needed
5. Add `view.js` for frontend behavior if needed
6. Add `style.scss` and editor styles when needed
7. Build the theme
8. Confirm the block exists under `build/blocks/<name>/`
9. Register it from `inc/blocks.php` if needed

Use the existing `code` and `toc` blocks as the reference pattern.

## Important Invariants

- `theme.json` and `src/styles/tokens-root.css` are generated from `src/tokens/design-tokens.json`
- custom blocks are registered from `build/blocks/*`, not directly from `src/blocks/*`
- the theme requires `build/` artifacts to run correctly
- the block editor intentionally does not load full theme/global styles
- code highlighting currently lazy-loads Prism from `jsDelivr`

## Further Reading

- [docs/technical/architecture.md](../../../../docs/technical/architecture.md)
