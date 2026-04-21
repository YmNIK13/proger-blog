## 1. Theme foundation and build

- [x] 1.1 Create the `web/app/themes/proger-blog/` theme structure with core theme files, package manifest, build config, helper scripts, and ignored generated directories.
- [x] 1.2 Set up the theme build workflow with `@wordpress/scripts`, custom webpack entries, Tailwind CSS, PostCSS, Sass support, and generated assets under `build/`.
- [x] 1.3 Add the token sync workflow from `src/tokens/design-tokens.json` to generated `theme.json` and `src/styles/tokens-root.css`.

## 2. Theme PHP layer

- [x] 2.1 Implement classic frontend templates for header, footer, blog index, single posts, category archives, search results, sidebar, and 404 pages.
- [x] 2.2 Add reusable PHP helper modules for article cards, fallback menus, sidebar link rendering, and TOC anchor generation.
- [x] 2.3 Add theme supports, menu registration, image sizes, textdomain loading, and theme-mod output handling.

## 3. Theme styling and layout

- [x] 3.1 Implement the global stylesheet entrypoint in `src/styles/main.css` using Tailwind CSS plus custom component layers.
- [x] 3.2 Implement archive and single-post layouts, including the optional sidebar offset and desktop/mobile TOC affordances.
- [x] 3.3 Add the skip-link and semantic frontend shell structure.

## 4. Custom blocks

- [x] 4.1 Implement the `proger-blog/code` block with editor UI, dynamic rendering, copy behavior, and lazy Prism highlighting.
- [x] 4.2 Implement the `proger-blog/toc` block with shared heading extraction, nested TOC markup, scroll-spy, and JSON-LD output.
- [x] 4.3 Register built custom blocks from `build/blocks/*`.

## 5. Interaction and settings

- [x] 5.1 Implement header and mobile-menu behavior through frontend JavaScript.
- [x] 5.2 Implement theme-level Customizer settings for background image, TOC visibility, and sidebar visibility.
- [x] 5.3 Add `autoptimize` exclusions and theme asset metadata handling for runtime compatibility.

## 6. Quality and documentation

- [x] 6.1 Add Pest coverage for TOC slug generation and heading extraction behavior.
- [x] 6.2 Add repository-level and theme-level documentation describing the project and the theme as separate artifacts.
- [x] 6.3 Align proposal, design, specs, and tasks with the shipped implementation of the theme.
