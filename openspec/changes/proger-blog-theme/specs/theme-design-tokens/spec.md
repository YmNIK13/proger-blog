## ADDED Requirements

### Requirement: Токени дизайну синхронізуються зі stitch у єдиному джерелі
Тема SHALL тримати канонічний файл токенів `design-tokens.json`, отриманий із MCP `stitch` (проєкт `projects/3838541024378760534` "The Illuminated Terminal"), як єдине джерело правди для кольорів, типографіки, spacing, radii, shadows та breakpoints. Зміни токенів MUST виконуватись через редагування `design-tokens.json` і запуск `npm run sync:tokens`. `tailwind.config.js` MUST читати `design-tokens.json` напряму через `require()`, забезпечуючи автоматичну синхронізацію класів `bg-*`, `text-*`, `border-*` без окремого build-step. Ручне редагування `theme.json` або `src/styles/tokens-root.css` заборонене.

#### Scenario: Розробник оновлює палітру кольорів
- **WHEN** розробник запускає `npm run sync:tokens` після експорту нових значень зі Stitch у `design-tokens.json`
- **THEN** скрипт перезаписує `theme.json` (секції `settings.color.palette` та `styles.color.*`) і `src/styles/tokens-root.css` (CSS custom properties `--proger-color-*`). Tailwind при наступному `npm run build` автоматично підхоплює нові значення через `require('./src/tokens/design-tokens.json')` у `tailwind.config.js`

#### Scenario: CI виявляє drift між токенами та згенерованими файлами
- **WHEN** PR змінює `design-tokens.json`, але не оновлює `theme.json`/`tokens-root.css`
- **THEN** команда `npm run sync:tokens:check` повертає ненульовий exit-код і CI блокує merge

### Requirement: Палітра кольорів, типографіка, spacing доступні у theme.json та Tailwind
Тема SHALL надавати мінімум такі групи токенів у `theme.json`, `tokens-root.css` та через Tailwind utility-класи: background, surface/surface-container-low/-high, canvas (білий), on-background, on-surface-variant, primary, primary-container, primary-fixed-dim, secondary-container, tertiary, outline, outline-variant, error, code-bg, code-text (Material-Design naming згідно Stitch source). Font families для body/headline (Inter) та mono (JetBrains Mono). Font sizes xs..xxl та display-sm/md/lg. Spacing 4/8/12/16/24/32/48/64. Radius sm/md/lg/xl/full. Shadow sm/md/lg/ambient. Breakpoints sm/md/lg/xl/2xl.

#### Scenario: Tailwind-клас використовує колір із токенів
- **WHEN** компонент розмітки містить `class="bg-surface-container text-on-surface-variant"`
- **THEN** Tailwind генерує CSS із кольорами зі `design-tokens.json` (`bg-surface-container` → `background-color: #0e222d`, `text-on-surface-variant` → `color: #c1cab0`); зміна hex у токенах без зміни розмітки оновлює ці кольори після наступного `npm run build`

#### Scenario: Блоковий SCSS використовує CSS custom property
- **WHEN** `blocks/code/style.scss` посилається на `var(--proger-color-code-keyword)`
- **THEN** у рантаймі значення береться з `:root`-визначення у `src/styles/tokens-root.css`, згенерованого з токенів, і працює навіть у контекстах де Tailwind-класи складно застосувати (pseudo-elements, dynamic state)

#### Scenario: Редактор блоків показує палітру зі stitch
- **WHEN** користувач редагує сторінку в Site Editor і відкриває селектор кольорів
- **THEN** відображається палітра з `theme.json` (ключі `slug`, `name`, `color` з токенів stitch), без системних fallback-кольорів WordPress

### Requirement: Заборона hardcoded-кольорів у розмітці та SCSS
Розмітка (HTML/PHP) MUST використовувати Tailwind-класи побудовані з токенів (`bg-<slug>`, `text-<slug>`). Блокові SCSS-файли MUST посилатись на `var(--proger-color-*)`, а не hex-літерали. Винятки: файли, згенеровані `sync-tokens.mjs` (`tokens-root.css`, `theme.json`), і `tailwind.config.js`, який читає токени напряму.

#### Scenario: Розробник пробує захардкодити hex у class
- **WHEN** розробник пише `<div class="bg-[#ff0000]">` замість `bg-error`
- **THEN** код-рев'ю блокує merge; наявні токени покривають потрібну семантику

### Requirement: Breakpoints централізовано через Tailwind theme
Значення breakpoints SHALL транслюватись у `tailwind.config.js` (`theme.extend.screens`) із `design-tokens.json → breakpoints`. Компоненти MUST використовувати Tailwind префікси `sm:`, `md:`, `lg:`, `xl:`, `2xl:`.

#### Scenario: Компонент перемикає layout на tablet
- **WHEN** розмітка використовує `class="flex-col md:flex-row"`
- **THEN** breakpoint `md` (≥ 768px) збігається з `design-tokens.json → breakpoints.md`
