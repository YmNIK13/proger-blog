## 1. Scaffolding теми та toolchain

- [ ] 1.1 Створити теку `web/app/themes/proger-blog/` із базовими файлами: `style.css` (з правильним WP-header: Theme Name, Version, Text Domain, Requires at least 6.9, Requires PHP 8.4, Template: `''`), `theme.json` (порожній каркас v3), `functions.php`, `index.php` (fallback), `screenshot.png` (заглушка 1200×900).
- [ ] 1.2 Створити підтеки: `templates/`, `parts/`, `patterns/`, `blocks/`, `src/` (`src/styles/`, `src/scripts/`), `assets/` (шрифти, SVG), `inc/` (PHP-помічники), `scripts/` (Node-скрипти).
- [ ] 1.3 Ініціалізувати `package.json` у теці теми з залежностями: `@wordpress/scripts`, `@wordpress/interactivity`, `sass`, `prismjs`, `stylelint`, `stylelint-config-standard-scss`, `stylelint-scss`; скриптами `start`, `build`, `lint:js`, `lint:css`, `sync:tokens`.
- [ ] 1.4 Додати `.stylelintrc.json` з правилом `color-no-hex` та дозволом hex лише у `src/styles/_tokens.css`/`_tokens.scss`.
- [ ] 1.5 Додати `.editorconfig`, `.gitignore` (ігнорувати `node_modules/`, `build/`).

## 2. Дизайн-токени зі stitch

- [ ] 2.1 Експортувати токени зі stitch MCP у файл `design-tokens.json` (колірна палітра, font families/sizes, spacing, radii, shadows, breakpoints). Зберегти у `web/app/themes/proger-blog/src/tokens/design-tokens.json`.
- [ ] 2.2 Написати `scripts/sync-tokens.mjs`: читає `design-tokens.json`, генерує `theme.json` (вливає в існуючий каркас, зберігаючи структуру), `src/styles/_tokens.scss`, `src/styles/_tokens.css`; підтримує прапорець `--check` для CI (виходить з 1, якщо diff).
- [ ] 2.3 Запустити `npm run sync:tokens`, перевірити валідність `theme.json` (`npx wp-scripts lint-json theme.json` або `openspec validate`).
- [ ] 2.4 Додати документування процесу у `README.md` теми: "Як оновити токени".

## 3. Build-pipeline

- [ ] 3.1 Налаштувати `wp-scripts build`/`start` для компіляції `src/scripts/**/*.js` і `src/styles/main.scss` у `build/`. За потреби — власний `webpack.config.js` розширенням дефолту `@wordpress/scripts` для SCSS preset.
- [ ] 3.2 Підключити `build/style-main.css` та `build/script-main.js` у `functions.php` через `wp_enqueue_style` / `wp_enqueue_script_module` (для Interactivity). Версії — з `asset.php`, згенерованих `wp-scripts`.
- [ ] 3.3 Налаштувати `autoptimize_filter_js_exclude` (додати exclusion `proger-blog/*/view.js`), щоб не ламати ES modules.
- [ ] 3.4 Переконатися, що dev-режим (`npm run start`) працює з HMR для стилів (через `@wordpress/scripts` default).

## 4. Базові стилі та layout

- [ ] 4.1 Створити `src/styles/_base.scss`: reset (modern CSS reset), типографіка base, body (фон темний з токенів), focus-visible стани.
- [ ] 4.2 Створити `src/styles/_layout.scss`: grid-template для `.site-layout`, 3 brakepoint-блоки (desktop/tablet/mobile), класи `.has-no-toc`, `.has-no-sidebar`.
- [ ] 4.3 Створити `src/styles/main.scss`, що імпортує `_tokens`, `_base`, `_layout`, і всі компонентні стилі.
- [ ] 4.4 Додати skip-link `<a class="skip-link" href="#main">…</a>` у patterns/header.
- [ ] 4.5 Перевірити, що layout відображає правильну сітку у трьох viewport у DevTools.

## 5. Template parts та patterns

- [ ] 5.1 Створити `parts/header.html` із `core/site-logo` + `core/navigation` (multi-level) + mobile toggle (кнопка з `data-wp-interactive="proger/nav"`).
- [ ] 5.2 Створити `parts/footer.html` з copyright, соц-лінками (як `core/social-links`) і site-info.
- [ ] 5.3 Створити `patterns/sidebar-categories.php` (PHP pattern): реєструється через `register_block_pattern`, рендерить `<aside class="sidebar-categories">` з деревом `get_categories()`; додає `is-active`/`aria-current` за `is_category()`.
- [ ] 5.4 Створити `patterns/article-card.php`: рендерить картку статті (title, excerpt, featured image, optional code snippet); теги виводити стандартним `core/post-terms` з `term="post_tag"` без власної PHP-розмітки; використовуватись у query-loop контексті.
- [ ] 5.5 Зареєструвати патерни в `functions.php` через `register_block_pattern_category('proger-blog', [...])` + `register_block_pattern(...)`.

## 6. Block templates (сторінки)

- [ ] 6.1 Створити `templates/index.html` — generic fallback (`core/post-template` + sidebar + article-card).
- [ ] 6.2 Створити `templates/home.html`: hero (site title + search), опційна featured-секція з sticky-post, query-loop з `article-card` pattern, tags-cloud (`core/tag-cloud` з лімітом 20), pagination.
- [ ] 6.3 Створити `templates/front-page.html`, що інгерить `home.html` (або копія — залежно від налаштувань сайту).
- [ ] 6.4 Створити `templates/category.html`: заголовок архіву (`core/post-terms` + `core/term-description`), query-loop з `inherit`, pagination.
- [ ] 6.5 Створити `templates/single.html`: breadcrumbs (опційно), post-title, post-meta (дата, автор, категорії, теги, час читання), featured image, post-content, блок `proger/toc` у правій колонці.
- [ ] 6.6 Створити `templates/404.html` і `templates/search.html`.

## 7. Інтерактивні custom blocks

- [ ] 7.1 Створити `blocks/code/block.json` (name `proger/code`, attributes `content`, `language`, `filename`, `showLineNumbers`; `viewScriptModule: file:./view.js`; `render: file:./render.php`; категорія `text`).
- [ ] 7.2 Створити `blocks/code/edit.js`: UI із TextareaControl для `content`, SelectControl для `language` (перелік зі специфікації), TextControl для `filename`, ToggleControl для `showLineNumbers`.
- [ ] 7.3 Створити `blocks/code/render.php`: вивід `<pre class="proger-code" data-language="...">` + header-рядок (лейбл мови, filename, button Copy); ескейп через `esc_html`.
- [ ] 7.4 Створити `blocks/code/view.js`: Interactivity store `proger/code` з action `copy`; динамічний імпорт Prism + потрібних мов; ініціалізація підсвічування; підтримка `prefers-reduced-motion` (немає анімації copy-feedback).
- [ ] 7.5 Створити `blocks/code/style.scss` (редактор + фронт): контейнер, header, кнопка copy, horizontal scroll, line-numbers через counter-reset.
- [ ] 7.6 Зареєструвати блок у `functions.php` через `register_block_type_from_metadata(__DIR__ . '/blocks/code')`.

- [ ] 7.7 Створити `blocks/toc/block.json` (name `proger/toc`, attributes `maxLevel` (default 4, enum 2|3|4), `title` (default "Зміст"); `render: file:./render.php`; `viewScriptModule: file:./view.js`; supports `{ html: false, multiple: false }`).
- [ ] 7.8 Створити `blocks/toc/render.php`: отримати `get_the_content()`, пройти через `WP_HTML_Tag_Processor`, зібрати h1–h4 до `$maxLevel`, згенерувати slug-id (транслітерація через `sanitize_title_with_dashes` + unique-suffix), побудувати вкладене `<nav class="toc"><ol>…</ol></nav>`; повернути порожній string, якщо заголовків немає або `get_theme_mod('proger_enable_toc', true) === false`.
- [ ] 7.9 Додати фільтр `the_content` у `inc/toc-anchors.php`, що вставляє `id` у заголовки контенту тим самим slug-алгоритмом (єдине джерело — helper-функція).
- [ ] 7.10 Створити `blocks/toc/view.js`: Interactivity store `proger/toc` з IntersectionObserver для scroll-spy, smooth-scroll handler (respect `prefers-reduced-motion`), keyboard focus target heading.
- [ ] 7.11 Створити `blocks/toc/style.scss`: sticky desktop, `<details>` collapsible mobile, стан `is-active` і `aria-current`.
- [ ] 7.12 У `blocks/toc/render.php` додати вивід JSON-LD `<script type="application/ld+json">` зі структурою `schema.org/TableOfContents` (поля `@context`, `@type`, `name`, `itemListElement`); гарантувати одноразовий вивід за запит (статична змінна або hook-once).

## 8. Sidebar і Header Interactivity

- [ ] 8.1 Створити `src/scripts/interactivity/nav.js`: store `proger/nav` із state `menuOpen`, `scrolled`; actions `toggleMenu`, `closeMenu`; callback `onScroll` (throttle 100ms).
- [ ] 8.2 Зареєструвати store у `functions.php` через `wp_interactivity_state` (початковий стан) і підключити `viewScriptModule` на усіх сторінках (через `core/navigation` контейнер).
- [ ] 8.3 Додати у `parts/header.html` директиви `data-wp-interactive="proger/nav"`, `data-wp-class--is-scrolled="state.scrolled"`, `data-wp-on--click="actions.toggleMenu"`, `data-wp-bind--aria-expanded="state.menuOpen"`.
- [ ] 8.4 Стилізувати sticky header (`src/styles/components/_header.scss`), mobile off-canvas, hover для submenus.

## 9. Стилізація компонентів

- [ ] 9.1 `src/styles/components/_article-card.scss` — дизайн картки (grid, hover transform+shadow, теги-бейджі, snippet-превью).
- [ ] 9.2 `src/styles/components/_sidebar-categories.scss` — дерево категорій, активний стан, відступи, responsive.
- [ ] 9.3 `src/styles/components/_hero.scss` — HomePage hero (великий заголовок, search-форма зі custom-стилями, фонова ілюстрація з token).
- [ ] 9.4 `src/styles/components/_typography.scss` — стилізована типографіка для ArticlePage (h1–h4, blockquote, inline code, table, ol/ul, figure with caption).
- [ ] 9.5 `src/styles/components/_tags.scss` — стилізація стандартних ядрових класів `.wp-block-post-terms`, `.wp-block-tag-cloud` (padding, radius, колір з токенів accent, hover); без власної HTML-розмітки.
- [ ] 9.6 `src/styles/components/_pagination.scss` — стилізація `core/query-pagination`.
- [ ] 9.7 `src/styles/components/_footer.scss` — компактний стилізований футер.
- [ ] 9.8 Перевірити усі стилі на відповідність `stylelint color-no-hex` (жодних hex поза `_tokens.*`).

## 10. Customizer settings

- [ ] 10.1 Створити `inc/customizer.php`: зареєструвати секцію `proger_theme_options` і контроли `proger_logo`, `proger_background_image`, `proger_enable_toc`, `proger_enable_sidebar` (усі з sanitize_callback).
- [ ] 10.2 У `functions.php` / `inc/theme-support.php` викликати `add_theme_support('custom-logo', [...])`, `add_theme_support('post-thumbnails')`, `add_theme_support('editor-styles')`, `add_editor_style('build/style-editor.css')`.
- [ ] 10.3 Додати `inc/theme-mods-output.php`, що рендерить inline-стилі з `proger_background_image` через `wp_add_inline_style`; додавати клас `has-no-toc`/`has-no-sidebar` на `<body>` через фільтр `body_class`.
- [ ] 10.4 Зареєструвати додатковий image-size `proger-card` (640×360) через `add_image_size` для `ArticleCard` featured.
- [ ] 10.5 Додати i18n: `load_theme_textdomain('proger-blog', get_template_directory() . '/languages')`; усі користувацькі рядки у PHP/JS обгорнути в `__()`/`_x()`/`__('…','proger-blog')`.

## 11. Accessibility та performance

- [ ] 11.1 Перевірити всі ARIA-ролі/атрибути (`role`, `aria-label`, `aria-current`, `aria-expanded`, `aria-controls`) у header, sidebar, TOC, cards, pagination через DevTools Accessibility panel.
- [ ] 11.2 Прогнати сторінки через Lighthouse (HomePage, CategoryPage, ArticlePage): цільова оцінка A11y ≥ 95, Performance ≥ 90 на 4G desktop/mobile.
- [ ] 11.3 Перевірити, що Interactivity `viewScriptModule` завантажується тільки на сторінках, де є відповідні блоки (Prism — тільки зі `proger/code`).
- [ ] 11.4 Додати `fetchpriority="high"` на першому featured image у HomePage/CategoryPage (через `proger-blog/*-loop` render-фільтр).
- [ ] 11.5 Перевірити CLS (кумулятивне зміщення лейауту) при appearing sticky header — виставити `contain-intrinsic-size` / фіксовану висоту header.

## 12. Testing та QA

- [ ] 12.1 Написати Pest unit-тест для `inc/toc-anchors.php::generate_slug()` (ASCII, кирилиця, колізії).
- [ ] 12.2 Написати Pest integration-тест: активуємо `proger-blog` на тестовому WP, викликаємо `wp_head()` на різних шаблонах — жодних PHP notices/warnings.
- [ ] 12.3 Додати у CI (`.github/workflows/theme.yml` або в існуючий): `composer lint`, `composer test`, `cd web/app/themes/proger-blog && npm ci && npm run lint:css && npm run lint:js && npm run sync:tokens -- --check && npm run build`.
- [ ] 12.4 Smoke-тест вручну через WP-CLI: `wp theme activate proger-blog --path=web/wp`, перевірити `/`, `/category/dev/`, `/?p=<post_id>` — зображення, TOC, CodeBlock copy, mobile menu.

## 13. Активація, документація, release

- [ ] 13.1 Оновити `composer.json` (або `post-install` скрипт): за потреби додати step для `npm ci && npm run build` у `web/app/themes/proger-blog`.
- [ ] 13.2 Додати `README.md` у теку теми: призначення, toolchain, команди (start/build/sync:tokens), how-to-update-tokens, rollback-план.
- [ ] 13.3 Прибрати з `.gitignore` `web/app/themes/` (якщо ignored) або додати whitelisting `!web/app/themes/proger-blog/` та ігнор `web/app/themes/proger-blog/node_modules/`, `web/app/themes/proger-blog/build/` (build артефакти не комітимо — budlies у CI).
- [ ] 13.4 Активувати `proger-blog` у staging, пройти QA-чеклист (всі сторінки, Customizer-контроли).
- [ ] 13.5 Створити PR, запросити код-рев'ю, задеплоїти у prod.
- [ ] 13.6 Після активації у prod — архівувати change у OpenSpec: `/opsx:archive proger-blog-theme`.
