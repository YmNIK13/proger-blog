## ADDED Requirements

### Requirement: Токени дизайну синхронізуються зі stitch у єдиному джерелі
Тема SHALL тримати канонічний файл токенів `design-tokens.json`, отриманий із MCP `stitch`, як єдине джерело правди для кольорів, типографіки, spacing, radii, shadows та breakpoints. Кожна зміна токенів MUST виконуватись шляхом оновлення `design-tokens.json` і повторного запуску генератора; ручне редагування похідних файлів (`theme.json`, `_tokens.scss`, `_tokens.css`) заборонене.

#### Scenario: Розробник оновлює палітру кольорів
- **WHEN** розробник запускає скрипт `npm run sync:tokens` після експорту нових значень зі stitch у `design-tokens.json`
- **THEN** скрипт перезаписує `theme.json` (секції `settings.color.palette` та `styles.color.*`), `src/styles/_tokens.scss` (SCSS-змінні `$color-*`) і `src/styles/_tokens.css` (CSS custom properties `--proger-color-*`) так, що всі три файли відображають нові значення без ручних правок

#### Scenario: CI виявляє drift між токенами та згенерованими файлами
- **WHEN** PR змінює `design-tokens.json`, але не оновлює похідні артефакти
- **THEN** команда `npm run sync:tokens -- --check` повертає ненульовий exit-код і CI блокує merge

### Requirement: Палітра кольорів, типографіка, spacing доступні у theme.json та SCSS
Тема SHALL надавати мінімум такі групи токенів у `theme.json` та `_tokens.scss`: background, surface, text-primary, text-secondary, accent, border, code-bg, code-text (кольори); font families для body та mono; font sizes xs..xxl; spacing 4/8/12/16/24/32/48/64; radius sm/md/lg; shadow sm/md/lg; breakpoints sm/md/lg/xl.

#### Scenario: Компонент використовує колір через CSS custom property
- **WHEN** SCSS-файл компонента посилається на `var(--proger-color-accent)`
- **THEN** у рантаймі колір береться з `:root`-визначення, що згенероване з `design-tokens.json`, і зміна значення в токенах без зміни коду компонента оновлює його в браузері

#### Scenario: Редактор блоків показує палітру зі stitch
- **WHEN** користувач редагує сторінку в Site Editor і відкриває селектор кольорів
- **THEN** відображається палітра з `theme.json` (ключі `slug`, `name`, `color` з токенів stitch), без системних fallback-кольорів WordPress

### Requirement: Заборона hardcoded-кольорів у SCSS та theme.json
Стилі компонентів теми MUST NOT містити hex/rgb/hsl-літералів поза файлами `_tokens.scss` та `_tokens.css`. Будь-яке посилання на колір SHALL виконуватись через SCSS-змінну або CSS custom property.

#### Scenario: Lint-перевірка знаходить hardcoded-колір
- **WHEN** SCSS-файл компонента містить `color: #ffffff;`
- **THEN** lint-правило stylelint `color-no-hex` повертає помилку і build падає

### Requirement: Breakpoints централізовано
Значення breakpoints SHALL імпортуватись із `_tokens.scss` через SCSS-змінні `$bp-sm`, `$bp-md`, `$bp-lg`, `$bp-xl`. Кастомні медіа-запити у компонентах MUST використовувати ці змінні.

#### Scenario: Компонент перемикає layout на tablet
- **WHEN** SCSS-файл компонента використовує `@media (min-width: $bp-md)`
- **THEN** значення `$bp-md` збігається з `design-tokens.json → breakpoints.md`
