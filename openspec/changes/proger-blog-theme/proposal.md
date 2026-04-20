## Why

Блог `Proger.Click` працює на Bedrock + WordPress 6.9 з дефолтною темою `twentytwentyfive`, яка не відповідає брендовому дизайну та не має ключових для технічного блогу елементів: лівого sidebar з категоріями, правого динамічного TOC на сторінці статті, стилізованих code-блоків з підсвічуванням і кнопкою копіювання, а також гнучких налаштувань Customizer. Потрібна власна production-ready тема `proger-blog`, у якій дизайн-токени беруться з MCP-сервера **stitch**, стилі пишуться на **SCSS** без Tailwind, а архітектура модульна та готова до масштабування.

## What Changes

- Додати нову WordPress-тему `proger-blog` у `web/app/themes/proger-blog/` у форматі **гібридної block theme** (block templates + `theme.json` + класичний `functions.php` для реєстрації assets, custom blocks та Customizer).
- Зафіксувати дизайн-токени (кольори, типографіка, spacing, shadows, radii) як **source of truth** з MCP `stitch` і згенерувати з них `theme.json`, `_tokens.scss` та CSS custom properties.
- Налаштувати build-pipeline на `@wordpress/scripts` (webpack + SCSS) + `node-sass`/`sass` з виходом у `build/` та підключенням через `wp_enqueue_style`/`wp_enqueue_script`.
- Реалізувати компоненти як **template parts** та **custom blocks** (де потрібна інтерактивність): `Header`, `Footer`, `Layout`, `SidebarCategories`, `SidebarTOC`, `ArticleCard`, `CodeBlock`.
- Реалізувати сторінки як block templates: `HomePage` (`front-page.html`/`home.html`), `CategoryPage` (`category.html`), `ArticlePage` (`single.html`).
- Додати клієнтську логіку на **Interactivity API** (`@wordpress/interactivity`) для sticky header з mobile dropdown, scroll-spy TOC, smooth scroll, copy-to-clipboard на `CodeBlock`.
- Додати syntax highlighting (Prism.js або Shiki — фінально визначається в design) для `CodeBlock`.
- Додати Customizer-налаштування теми: логотип (image/SVG), фонове зображення, вмикач TOC, вмикач лівого sidebar.
- Забезпечити responsive layout (breakpoints з stitch), доступність (семантичний HTML + ARIA), відсутність inline-стилів, max content width ≈ 800px, hover-стани, анімації 150–200 ms.

## Capabilities

### New Capabilities
- `theme-design-tokens`: синхронізація дизайн-токенів зі stitch у `theme.json`, `_tokens.scss` і CSS custom properties; єдине джерело правди для кольорів, типографіки, spacing, radii, shadows, breakpoints.
- `theme-layout`: глобальний layout (Header / Left Sidebar / Main / Right Sidebar / Footer) через block templates та template parts; responsive-правила; max-width контенту.
- `theme-navigation`: sticky header з багаторівневою навігацією та мобільним dropdown; sidebar категорій з активним станом і деревоподібною структурою.
- `theme-toc`: автогенерований TOC по заголовках `h1–h4` усередині контенту статті; anchor-лінки, scroll-spy з highlight активної секції, smooth scroll, collapsible на мобільних.
- `theme-code-block`: кастомний блок `proger/code` із syntax highlighting, міткою мови, horizontal scroll, кнопкою копіювання, дизайном зі stitch.
- `theme-article-card`: картка статті з назвою, коротким превью, тегами, опційним snippet-превью коду.
- `theme-pages`: шаблони `HomePage` (hero + search + список `ArticleCard` + теги + опційна featured-стаття), `CategoryPage` (фільтровані статті, SEO-дружні URL, активна категорія в sidebar), `ArticlePage` (типографіка, CodeBlock, правий TOC).
- `theme-settings`: Customizer-налаштування (logo, background image, enable/disable TOC, enable/disable sidebar) з пробросом у шаблони.

### Modified Capabilities
<!-- Поки специфікацій у openspec/specs/ ще немає — нічого не модифікуємо. -->

## Impact

- **Код**: нова директорія `web/app/themes/proger-blog/` з повною структурою block-theme (`theme.json`, `templates/`, `parts/`, `patterns/`, `blocks/`, `src/` для SCSS/JS, `build/` як output, `functions.php`, `style.css`). Активація теми замість `twentytwentyfive` у `wp_options` (через UI або WP-CLI).
- **Залежності**: додати `package.json` з `@wordpress/scripts`, `@wordpress/interactivity`, `sass`, `prismjs` (або альтернатива). PHP-залежностей не додаємо; Bedrock-composer не чіпаємо.
- **MCP**: активна залежність від `stitch` як джерела дизайн-токенів на етапі розробки (генерація `theme.json` + `_tokens.scss`). Після генерації токени комітяться у репо — рантайм від `stitch` не залежить.
- **SEO/URL**: без змін постійних посилань; використовуються стандартні WP-архіви категорій (`/category/<slug>/`).
- **Сумісність**: WordPress ≥ 6.9, PHP ≥ 8.4 (з composer.json). Block theme → потрібна підтримка Site Editor.
- **Тестування**: додати Pest-тест на активацію теми та відсутність PHP-помилок у `functions.php`, перевірка build-пайплайну через CI.
- **Відкати**: повернення на `twentytwentyfive` — одна команда `wp theme activate twentytwentyfive`.
