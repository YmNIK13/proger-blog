## Context

Проєкт `Proger.Click` побудований на **Bedrock** (WordPress 6.9.4, PHP 8.4) — стандартизоване розміщення коду WP у `web/wp/` та вмісту в `web/app/` (plugins, mu-plugins, themes, uploads). Встановлені активно-використовувані плагіни: `autoptimize`, `wordpress-seo`, `wp-super-cache`, `wp-optimize`, `cyr-and-lat`, `cyrlitera`. Дефолтна активна тема — `twentytwentyfive` (сучасна WP block theme від Automattic). Дизайн-токени для нового бренду надходять із **MCP-сервера stitch** (колірна палітра, типографіка, spacing, shadows, radii, breakpoints). Stitch — єдине джерело правди дизайну: ручне вигадування кольорів заборонене.

WP 6.9 повністю підтримує **block themes** (Site Editor), `theme.json` v3, **Interactivity API** v2 (`@wordpress/interactivity`), **viewScriptModule** у `block.json`, та `render.php` для server-side rendering блоків. Ці можливості потрібно використати, щоб тема була forward-compatible та добре інтегрувалася з Gutenberg.

Контентна модель: стандартні WP post types — пости (статті), категорії (для лівого sidebar), теги (для Hero/ArticleCard), сторінки (Home як статична або як `home.html`). Спеціальних CPT не створюємо.

## Goals / Non-Goals

**Goals:**
- Створити production-ready **гібридну block theme** `proger-blog`, яка використовує Site Editor-friendly `theme.json`, block templates у `templates/` і template parts у `parts/`, але також підключає SCSS-компільовану таблицю стилів через `functions.php` для тонкого контролю над компонентами, яких немає у блоках ядра (CodeBlock, SidebarTOC, SidebarCategories, ArticleCard).
- Тримати дизайн-токени **синхронізованими зі stitch** через відтворюваний скрипт `scripts/sync-tokens.mjs`, який генерує `theme.json` та `src/styles/_tokens.scss` із одного JSON-файла токенів.
- Реалізувати інтерактивну поведінку (TOC scroll-spy, mobile nav, copy-to-clipboard) на **Interactivity API** без React на фронтенді, щоб мінімізувати бандл.
- Забезпечити повну **доступність** (ARIA-landmarks, `aria-current`, клавіатурна навігація в меню, focus-visible стани) і **responsive layout** із breakpoints зі stitch.
- Забезпечити **performance**: lazyload зображень, `defer`/`module` для JS, критичний inline CSS для above-the-fold (через `wp_add_inline_style`), відсутність важких runtime-залежностей.

**Non-Goals:**
- Не створюємо headless-архітектуру (React SPA + REST API). Тема залишається класичною SSR-рендерною, з локальними інтерактивними острівцями.
- Не переписуємо плагіни (SEO/caching) — працюємо з існуючими.
- Не підтримуємо IE / legacy-браузери (baseline — усі evergreen браузери з підтримкою CSS nesting native).
- Не робимо multilang на рівні теми — інтеграцію з перекладачами залишаємо окремим change.
- Не пишемо E2E-тести в межах цього change (тільки unit-тести на TOC-парсер та інтеграційний smoke-тест активації теми).

## Decisions

### D1. Architecture type: hybrid block theme
**Рішення**: `proger-blog` — block theme (`theme.json` + HTML block templates), доповнена класичним `functions.php` для реєстрації custom blocks, assets, Customizer-налаштувань.

**Чому так**: block theme дає безкоштовно Site Editor, `theme.json`-токени, керування стилями ядра, глобальні presets кольорів/typography/spacing. Класичні хуки потрібні для Interactivity-stores, реєстрації `proger/code` тощо — суто block-theme підхід тут недостатній.

**Альтернативи**:
- Суто класична тема (`index.php` + template hierarchy): простіша для початку, але втрачаємо Site Editor, block pattern UX, дизайн-токени ядра; поганий forward-compat.
- Суто block theme без PHP: неможливо реєструвати custom blocks і Interactivity-stores елегантно.

### D2. Design tokens: stitch → theme.json + Tailwind config через generator
**Рішення**: канонічний `design-tokens.json` (експортований зі Stitch MCP-проєкту `projects/3838541024378760534` "The Illuminated Terminal") — джерело правди. Скрипт `scripts/sync-tokens.mjs` генерує:
- `theme.json` (секції `settings.color.palette`, `settings.typography.*`, `settings.spacing`, `styles.*`) для Site Editor;
- `src/styles/tokens-root.css` з `:root { --proger-color-*: ...; }` для runtime-доступу з блокових SCSS і inline-styles;
- `tailwind.config.js` — **не генерується**, читає `design-tokens.json` напряму через `require()`, завдяки чому Tailwind автоматично знає про нові токени без додаткового build-step.

**Чому так**: уникаємо дрейфу токенів між theme.json і Tailwind/CSS; Stitch — єдине джерело; розробник не переписує `theme.json` вручну. Tailwind отримує кольори (`extend.colors`), font-families, radii, shadows, breakpoints, transition timing через єдиний import.

**Альтернативи**:
- Ручне ведення `theme.json` + `tailwind.config.js`: гарантує drift через кілька місяців.
- Style Dictionary: надлишкова залежність для 6 категорій токенів.

### D3. Build pipeline: `@wordpress/scripts` + Tailwind CSS (локально, не CDN)
**Рішення**: `@wordpress/scripts` як build-пакет; `Tailwind CSS 3.4` інтегрований через `postcss.config.js` (plugins: `tailwindcss`, `autoprefixer`). Entry `src/styles/main.css` містить `@tailwind base/components/utilities` + `@layer components` для власних composite-класів (`.side-link`, `.card-matter`, `.prose-matter`). Tailwind content-paths — `templates/`, `parts/`, `patterns/`, `inc/`, `src/**/*.js`, `functions.php`.

**Чому так**: Stitch HTML-шаблон побудований на Tailwind — найшвидший шлях до exact visual match. Native Tailwind CLI (не CDN) тримає production-bundle маленьким (JIT + purge). Складні dynamic-state стилі (scroll-spy `is-active` з брендовим лівим border, copy-state feedback) залишаються у `.scss` файлах блоків через CSS custom properties з `tokens-root.css`.

**Альтернативи**:
- Pure SCSS + BEM: 3-4× більше коду для відтворення шаблона, drift від дизайну.
- Tailwind CDN: швидко, але production-забанений (Tailwind docs прямо це забороняє) і не purge'ується.
- Vanilla CSS + custom properties: ще більше ручного коду.

### D4. Interactivity API v2 для клієнтського стану
**Рішення**: Всю клієнтську інтерактивність пишемо через `@wordpress/interactivity`:
- store `proger/nav` — mobile dropdown toggle, scroll-sticky-state header;
- store `proger/toc` — scroll-spy (IntersectionObserver), active section, collapsible на mobile;
- store `proger/code` — copy-to-clipboard, toast про успіх.

Блоки, що мають інтерактивність (`proger/code`, `proger/toc`), реєструються з `"viewScriptModule": "file:./view.js"` у `block.json`. Template parts із інтерактивністю (Header) отримують `data-wp-interactive="proger/nav"` і пов'язані директиви у розмітці.

**Чому так**: Interactivity API — офіційний шлях WordPress для client-side behavior у block themes (2024+). Бандл маленький (~3 KB runtime), директиви `data-wp-*` SSR-дружні, hydration без React.

**Альтернативи**:
- Ванільний JS + IIFE: більше boilerplate, складніше уникати дублювання логіки.
- Alpine.js: зайва runtime-залежність; конфліктує з Interactivity-директивами на одному елементі.

### D5. Syntax highlighting: Prism.js із ручним вибором мов
**Рішення**: використовуємо `prismjs` через npm, імпортуємо **тільки** потрібні мови (`js`, `ts`, `php`, `bash`, `json`, `css`, `scss`, `html`, `sql`, `yaml`, `diff`) і плагіни (`line-numbers`, `toolbar` — опційно). Бандл додається лише на сторінках, де є блок `proger/code` (через `block.json` → `viewScriptModule`).

**Чому так**: Prism — невеликий (~5 KB gzip з обраними мовами), працює як на сервері (через `@wordpress/scripts`), так і на клієнті; Shiki крутіший, але ~200 KB і ASM-worker, що важкувато для блогу.

**Альтернативи**:
- Shiki: VS Code-якість, але розмір бандла непропорційний.
- Highlight.js: менш сучасний; гірший autoload мов.
- Server-side highlight у PHP через `scrivo/highlight.php`: рантайм безкоштовний, але додає PHP-залежність у Bedrock — не на часі.

### D6. Custom blocks vs template parts
- `Header` → **template part** `parts/header.html` (GutenbergSite Navigation block + HTML Button + Interactivity-директиви). Користувач редагує в Site Editor.
- `Footer` → **template part** `parts/footer.html`.
- `SidebarCategories` → **pattern** `patterns/sidebar-categories.php`, що серверно рендерить список категорій (PHP-query `get_categories()`) з активним станом за `is_category()`. Вставляється в `templates/*.html` як block-reference `<!-- wp:pattern {"slug":"proger-blog/sidebar-categories"} /-->`.
- `SidebarTOC` → **custom block** `proger/toc` із `render.php` (парсить контент поточного поста на h1–h4, будує список) + `view.js` (Interactivity store: scroll-spy, smooth scroll).
- `ArticleCard` → **pattern** `patterns/article-card.php` (рендерить title, excerpt, featured image, опційний snippet коду; теги виводить стандартним `core/post-terms` з `term="post_tag"`, стилізованим через SCSS).
- `CodeBlock` → **custom block** `proger/code` із `edit.js` (CodeMirror textarea в редакторі), `save.js` або `render.php` (вивід `<pre><code class="language-...">`), `view.js` (Prism-хайлайт + copy button через Interactivity).

### D7. Layout і CSS Grid
**Рішення**: layout реалізуємо через CSS Grid у `:root .site-layout`: колонки `sidebar | main | toc`; на tablet — `main | toc` (ліву категорію рухаємо в off-canvas); на mobile — одна колонка. Max-width контенту — `--proger-content-max: 800px` (змінна з tokens). Breakpoints зі stitch.

**Чому так**: Grid дає найгнучкіший контроль для 3-колонкової верстки з опційним правим sidebar (TOC показується тільки на ArticlePage — решта шаблонів отримує 2-колонкову версію через CSS-клас `has-no-toc`).

### D8. TOC-парсер
**Рішення**: парсинг виконується **серверно** у `render.php` блоку `proger/toc` за допомогою `WP_HTML_Tag_Processor` (ядро WP 6.2+), щоб мати стабільний DOM і додавати `id`-анкор до кожного заголовка у контенті через фільтр `the_content`. Клієнтський `view.js` лише встановлює `aria-current` і smooth-scroll.

**Чому так**: SSR TOC індексується пошуковиками, доступний без JS, не залежить від hydration-порядку.

### D9. Customizer vs theme.json styles
**Рішення**: користувацькі налаштування (logo, background image, enable TOC, enable sidebar) виставляємо через **Customizer API** (`add_theme_support('custom-logo')`, `get_theme_mod()`), а не через `theme.json` — бо це runtime-перемикачі, а не дизайн-токени. Результат використовуємо у `parts/header.html` через динамічний блок `core/site-logo` та CSS-класи-модифікатори `has-toc` / `has-sidebar`.

### D10. Темна тема + контрастність
**Рішення**: базовий фон — темний, контент — світлий (за вимогою). Використовуємо токени `--proger-color-bg`, `--proger-color-surface`, `--proger-color-text-primary`. `theme.json` виставляє `"appearanceTools": true`, але **не** вмикаємо авто-світлу тему — конкретна палітра приходить зі stitch.

## Risks / Trade-offs

- **[Risk]** Дрейф токенів між stitch і закомітованим `theme.json` → **Mitigation**: CI-перевірка, що `sync-tokens.mjs --check` не має diff; PR, що змінює токени, зобов'язаний перегенерувати обидва файли.
- **[Risk]** Interactivity API на деяких плагінах кешування (`wp-super-cache`) може не виконуватися через mangling HTML → **Mitigation**: тестуємо з активним super-cache; за потреби додаємо exclusion для сторінок із блоками `proger/toc`/`proger/code`.
- **[Risk]** Prism-мови, що не імпортовані, не підсвічуються → **Mitigation**: у `edit.js` показуємо список доступних мов (dropdown) замість вільного вводу.
- **[Risk]** Conflict із `autoptimize` (об'єднує JS) для `viewScriptModule` (ES modules) → **Mitigation**: у `functions.php` додаємо `autoptimize_filter_js_exclude` для `proger-blog/*/view.js`.
- **[Risk]** Site Editor дозволяє користувачу зламати layout, перетягуючи template parts → **Mitigation**: критичні template parts позначаємо `"area": "header"`/`"footer"` та лочимо патернами через `lock: {"move": true, "remove": true}` у `block.json`-patterns.
- **[Trade-off]** Відмова від Tailwind → більше ручної роботи у SCSS, але відповідає вимогам і дає чистіший CSS-вивід під контролем токенів.
- **[Trade-off]** Гібридний підхід (не pure block theme) → трохи складніший onboarding для контриб'юторів, але дає гнучкість, якої бракує pure-block підходу.

## Migration Plan

1. Додаємо теку `web/app/themes/proger-blog/` з повною структурою (див. `tasks.md`).
2. Встановлюємо Node-залежності (`npm install` у теці теми).
3. Синхронізуємо токени: `npm run sync:tokens`.
4. Білдимо: `npm run build`.
5. Активуємо тему локально через WP-CLI: `wp theme activate proger-blog --path=web/wp`.
6. Проходимо smoke-чеклист: HomePage/CategoryPage/ArticlePage рендеряться, TOC, CodeBlock copy працюють, mobile menu відкривається.
7. Деплой у staging → QA → prod.
8. **Rollback**: `wp theme activate twentytwentyfive` — повертає попередню тему без втрат даних (WP зберігає активну тему в `wp_options`).

## Resolved Scope Notes

- **Prism-мови**: фінальний baseline — список із D5 (javascript, typescript, php, bash, json, css, scss, html, sql, yaml, diff, markdown, plaintext). Додаткові мови не вносимо у цей change.
- **Schema.org TableOfContents**: входить у scope. Блок `proger/toc` додатково виводить JSON-LD `<script type="application/ld+json">` зі структурою `{ "@context": "https://schema.org", "@type": "TableOfContents", "itemListElement": [...] }` для покращеної індексації та сумісності з `wordpress-seo` (див. specs/theme-toc).
- **Теги у `ArticleCard`**: без власних бейджів/tooltip — використовуємо стандартний `core/post-terms` (taxonomy `post_tag`) із стилізацією через SCSS. Жодної кастомної PHP-розмітки для тегів у картці.
- **Редактор `proger/code`**: без CodeMirror — звичайний `TextareaControl` + `SelectControl` для мови.
