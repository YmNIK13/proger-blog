## ADDED Requirements

### Requirement: Глобальний каркас сторінки
Тема SHALL надавати глобальний layout з наступних семантичних регіонів: `header` (site header), `nav` (первинна навігація, частина header), лівий `aside` (sidebar категорій), `main` (контент сторінки), правий `aside` (TOC — опційно), `footer` (site footer). Layout MUST бути побудований на CSS Grid.

#### Scenario: Користувач відкриває ArticlePage на desktop
- **WHEN** viewport ≥ `$bp-lg` і поточний шаблон — `single.html`
- **THEN** розмітка містить три колонки: `sidebar | main | toc`; `main` має `max-inline-size: var(--proger-content-max)` (≈ 800 px); `sidebar` і `toc` — sticky у межах viewport

#### Scenario: Користувач відкриває будь-яку сторінку без TOC
- **WHEN** сторінка не має блоку `proger/toc` (або `theme_mod('proger_enable_toc')` = false)
- **THEN** layout перемикається у двоколонкову конфігурацію `sidebar | main`, і `main` розтягується до `max-inline-size: var(--proger-content-max)`

### Requirement: Responsive-поведінка
Layout SHALL адаптуватися до viewport:
- `≥ $bp-lg`: три колонки (`sidebar | main | toc`).
- `$bp-md..$bp-lg`: дві колонки (`main | toc`), ліва категорія — off-canvas за кнопкою.
- `< $bp-md`: одна колонка, sidebar і TOC стають collapsible (toggle-кнопки у хедері контенту).

#### Scenario: Перехід із desktop на mobile
- **WHEN** viewport змінюється з ≥ `$bp-lg` на < `$bp-md`
- **THEN** ліва категорія ховається за toggle-кнопкою у header; TOC стає collapsed accordion над `main`; контент отримує `padding-inline: var(--proger-space-16)`

### Requirement: Max-width контенту
Контент у `main` SHALL мати максимальну ширину `--proger-content-max` (значення береться з токенів, за замовчуванням `800px`). Блоки `wp:image`, `wp:video`, `wp:embed` з alignwide/alignfull MAY перевищувати цю ширину через стандартні класи WP.

#### Scenario: Довгий абзац
- **WHEN** користувач читає статтю з довгим абзацом тексту
- **THEN** рядок не перевищує `--proger-content-max`, вирівнюється зліва, межа колонки контенту сумісна з центром `main`-колонки

### Requirement: Семантичні landmarks і accessibility
Регіони SHALL мати коректні ARIA-ролі/теги: `<header role="banner">`, `<nav aria-label="Primary">`, `<aside aria-label="Categories">`, `<main id="main">`, `<aside aria-label="Table of contents">`, `<footer role="contentinfo">`. Skip-link `a.skip-link[href="#main"]` MUST бути першим елементом DOM.

#### Scenario: Читач з екранним читачем переходить на skip-link
- **WHEN** фокус потрапляє на skip-link натисканням Tab
- **THEN** skip-link стає візуально видимим (`:focus-visible`); Enter переводить фокус на `<main id="main">`

### Requirement: Layout керується настройками теми
Наявність лівого sidebar і правого TOC SHALL залежати від theme mods `proger_enable_sidebar` та `proger_enable_toc`. При вимкненні будь-якого — відповідна колонка видаляється з Grid, `main` розтягується.

#### Scenario: Адмін вимикає лівий sidebar у Customizer
- **WHEN** `proger_enable_sidebar` встановлено в `false`
- **THEN** на всіх шаблонах ліва колонка не рендериться, `main` займає її простір
