# Proger Blog — WordPress Theme

Гібридна block-theme для [Proger.Click](https://proger.click/). Побудована на дизайн-системі **"The Illuminated Terminal"** зі Stitch MCP. Темний void-фон, білі content-картки, `JetBrains Mono` code-блоки з Prism-підсвічуванням, автоматичний Table of Contents, SCSS-токени.

---

## Мінімальні вимоги

- **WordPress** ≥ 6.9 (для Interactivity API v2 та Script Modules)
- **PHP** ≥ 8.4
- **Node.js** ≥ 20 (тільки для білду — не потрібен у продакшні)
- Опційно: `wp-cli` для швидкої активації/налаштування

---

## 1. Збірка теми (обов'язково перед активацією)

Тема комітиться з вихідним кодом у `src/`, але WordPress рендерить з `build/`. Без білду тема не запрацює.

```bash
cd web/app/themes/proger-blog

# Встановити залежності (разово)
npm install

# Зібрати продакшн-артефакти у build/
npm run build
```

Після цього з'являється тека `build/` з `main.css`, `interactivity-nav.js` та блоковими артефактами у `build/blocks/code/`, `build/blocks/toc/`.

> **Не коміть** `node_modules/` та `build/` — обидві вже у `.gitignore`. На CI/деплої виконуй `npm ci && npm run build`.

---

## 2. Активація теми

### Через адмінку
`Appearance` → `Themes` → знайти **Proger Blog** → `Activate`.

### Через WP-CLI (швидше)
```bash
wp theme activate proger-blog --path=web/wp
```

### Відкат на попередню тему
```bash
wp theme activate twentytwentyfive --path=web/wp
```

---

## 3. Налаштувати Reading settings

Блог має показувати **список постів** на головній.

`Settings` → `Reading`:
- **Your homepage displays:** `Your latest posts` (важливо, не "A static page")
- **Blog pages show at most:** `10`
- **Search Engine Visibility:** зняти галочку для продакшну

WP-CLI варіант:
```bash
wp option update show_on_front posts --path=web/wp
wp option update posts_per_page 10 --path=web/wp
```

---

## 4. Категорії та навігація

Тема показує дерево категорій у лівому sidebar. Без категорій sidebar буде порожній.

### Створити базові категорії
`Posts` → `Categories`. Створіть хоча б 3-5 категорій (наприклад: `DevOps`, `Frontend`, `WordPress`, `Tutorials`, `Nodes`).

WP-CLI варіант:
```bash
wp term create category "DevOps" --slug=devops --path=web/wp
wp term create category "Frontend" --slug=frontend --path=web/wp
wp term create category "WordPress" --slug=wordpress --path=web/wp
```

### Налаштувати permalinks (SEO-URL для категорій)
`Settings` → `Permalinks` → обрати `Post name` (`/%postname%/`).

WP-CLI:
```bash
wp rewrite structure '/%postname%/' --path=web/wp
wp rewrite flush --path=web/wp
```

Тепер категорії доступні за `/category/devops/`, статті — за `/nazva-statti/`.

---

## 5. Меню (Header navigation)

Блок `core/navigation` у `parts/header.html` підтягує меню з `ref:0` (порожнє за замовчуванням).

`Appearance` → `Editor` → `Navigation`:
1. Натиснути **Add new menu**
2. Додати пункти:
   - Home → `/`
   - All categories → `/category/` (опційно)
   - About → сторінку "About" (створи як `Pages` → `Add New`)
   - RSS → `/feed/`
3. Зберегти.

Альтернативно — додати пункти меню через Site Editor прямо у header template part.

---

## 6. Customizer налаштування

`Appearance` → `Customize` → **Proger Blog** (наша кастомна секція):

| Контрол | Що робить |
|---------|-----------|
| **Background image** | Опціональне фонове зображення сайту (PNG/JPG/SVG/WebP) |
| **Enable Table of Contents** | Увімкнути правий TOC на сторінках статей |
| **Enable left categories sidebar** | Увімкнути лівий sidebar категорій |

Логотип налаштовується у стандартній секції `Site Identity` → `Logo` (до 240×64).

Site title + tagline — там же (показуються у header, якщо немає логотипа).

---

## 7. Створити першу статтю з усіма фічами

`Posts` → `Add New`.

### Заголовок і категорія
- Title: `Привіт, Proger.Click!`
- У панелі справа: `Categories` → обрати `DevOps` (або будь-яку)
- `Tags` → додати 2-3 теги (`wordpress`, `block-theme`, `tutorial`)
- `Featured image` → завантажити обкладинку (буде ≥ 640×360)

### Контент
Додати кілька `Heading` блоків (H2, H3) — вони з'являться у TOC автоматично:
```
## Огляд
### Встановлення
### Налаштування
## Підсумок
```

Додати блок **"Proger Code"**: `+` → пошук `Proger Code`. Обрати мову, вставити код:
```json
{
  "title": "Hello, World"
}
```

Додати блок **"Proger TOC"**: `+` → `Proger TOC`. Зазвичай ставиться автоматично у правому sidebar через `templates/single.html`, але можна вставити вручну у пост для тесту.

Опублікувати. Відкрити на фронтенді — побачиш:
- Sticky header з backdrop-blur
- Ліворуч — дерево категорій із активним станом
- Головна колонка: meta, featured image, контент зі стилізованою типографікою
- Праворуч — sticky TOC, що підсвічує поточний розділ при скролі
- Code-блок із syntax highlight (Prism) і кнопкою `Copy`

---

## 8. Тестова перевірка сторінок

Переконайся, що кожен тип сторінки рендериться без помилок:

| URL | Шаблон | Що перевірити |
|-----|--------|---------------|
| `/` | `home.html` | Hero, search, featured post (якщо є sticky), список `ArticleCard`, tags-cloud |
| `/category/devops/` | `category.html` | Заголовок категорії, відфільтровані пости, активний лінк у sidebar |
| `/nazva-statti/` | `single.html` | TOC, code-блоки, типографіка |
| `/?s=test` | `search.html` | Результати пошуку або повідомлення "не знайдено" |
| `/nonexistent` | `404.html` | 404-повідомлення, пошук, кнопка на головну |

---

## 9. Інтеграція з існуючими плагінами

У Bedrock-проєкті вже активні:

- **autoptimize** — excluded `proger-blog/*/view.js` та `interactivity-nav.js` через фільтр у `inc/assets.php`, щоб не ламати ES modules.
- **wp-super-cache** — якщо бачиш стару верстку після активації, очисти кеш: `wp super-cache flush --path=web/wp`.
- **wordpress-seo (Yoast)** — сумісний; блок `proger/toc` виводить окремий JSON-LD `schema.org/TableOfContents`, що не конфліктує з `@graph` Yoast.
- **cyr-and-lat** + **cyrlitera** — транслітерація slug-ів постів (кирилиця → латиниця) вже працює з нашим TOC-slug-генератором.

Очистити всі кеші разом:
```bash
wp cache flush --path=web/wp
wp super-cache flush --path=web/wp
wp rewrite flush --path=web/wp
```

---

## 10. Щоденна розробка

```bash
# Watch-режим: webpack слідкує за src/, пересобирає на льоту
npm run start

# Lint перед комітом
npm run lint:css
npm run lint:js

# Після редагування src/tokens/design-tokens.json
npm run sync:tokens
```

CI повинен робити:
```bash
npm ci && npm run sync:tokens:check && npm run lint:css && npm run lint:js && npm run build
```

### Оновлення дизайн-токенів (зі Stitch)

1. Змінити `src/tokens/design-tokens.json` (стягнуто зі Stitch-проєкту `projects/3838541024378760534`).
2. `npm run sync:tokens` — перегенерує `theme.json` та `src/styles/_tokens.scss`.
3. Закомітити обидва згенеровані файли разом із `design-tokens.json`.
4. **Не редагуй `theme.json` вручну** — його перезапише наступний запуск генератора.

---

## 11. Troubleshooting

| Симптом | Причина / Фікс |
|---------|-----------------|
| `Часть шаблона была удалена: header` | Не виконано `npm run build` або `build/` відсутня. Зібрати тему. |
| `WP_Script_Modules::register ... wp-interactivity not registered` | Post-build скрипт `fix-module-deps.mjs` не відпрацював. Перезапустити `npm run build`. |
| Prism не підсвічує код | Мова не в списку підтримуваних (див. `src/blocks/code/edit.js`). Або заблокував autoptimize — перевір exclusion у `inc/assets.php`. |
| TOC порожній на сторінці статті | У статті немає заголовків `h2`–`h4`, або вимкнено у Customizer. |
| Стилі не застосовуються після активації | Очистити кеш браузера + `wp cache flush`. Перевірити, що `build/main.css` існує. |
| Меню у header порожнє | Створити Navigation menu через Site Editor (див. крок 5). |
| Sidebar категорій порожній | У базі немає постів. Створи 1-2 тестові пости з категоріями. |

Xdebug/Query Monitor:
- Увімкнути `WP_DEBUG=true` у `.env` Bedrock → перевірити `web/app/debug.log` на PHP-notices.
- Встановити плагін **Query Monitor** для перевірки hooks, SQL, HTTP-викликів (рекомендовано на dev, не на prod).

---

## 12. Структура теки

```
proger-blog/
├─ build/                 # ← генерується npm run build, не комітимо
├─ inc/                   # PHP helpers (theme supports, assets, customizer, TOC)
├─ parts/                 # Block template parts (header.html, footer.html)
├─ patterns/              # PHP-рендеровані patterns (sidebar-categories, article-card)
├─ src/
│  ├─ blocks/
│  │  ├─ code/            # proger-blog/code (block.json + edit/view/render/style)
│  │  └─ toc/             # proger-blog/toc
│  ├─ scripts/interactivity/nav.js   # Interactivity store (sticky header, mobile menu)
│  ├─ styles/             # SCSS: _tokens (auto-gen), _base, _layout, components/
│  └─ tokens/design-tokens.json      # Канонічне джерело токенів зі Stitch
├─ scripts/
│  ├─ sync-tokens.mjs     # Регенерація theme.json + _tokens.scss
│  └─ fix-module-deps.mjs # Post-build: wp-interactivity → @wordpress/interactivity
├─ templates/             # Block templates (home, category, single, 404, search)
├─ theme.json             # ← AUTO-GENERATED, не редагувати вручну
├─ functions.php          # Entry point: require_once інших inc/*
├─ style.css              # WP theme header
├─ package.json
├─ webpack.config.js
└─ README.md              # цей файл
```
