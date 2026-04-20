## ADDED Requirements

### Requirement: Автогенерований Table of Contents для ArticlePage
Тема SHALL надавати custom block `proger/toc`, який автоматично будує зміст статті. Блок MUST парсити контент поточного поста на рівнях заголовків `h1, h2, h3, h4`, зберігати ієрархію (вкладені `<ol>`), генерувати anchor-id для кожного заголовка (slug із тексту, транслітерований латиницею, унікалізований суфіксом `-2`, `-3`... при колізіях).

#### Scenario: Стаття містить заголовки h2 і h3
- **WHEN** редактор публікує статтю з послідовністю `h2 "Огляд"`, `h3 "Вступ"`, `h3 "Деталі"`, `h2 "Висновок"`
- **THEN** блок `proger/toc` рендерить вкладений список: `Огляд` (з піднесеним списком `Вступ`, `Деталі`), `Висновок`; кожен заголовок у контенті отримує `id="ohliad"`, `id="vstup"`, `id="detali"`, `id="vysnovok"`

#### Scenario: Кілька заголовків з однаковим текстом
- **WHEN** стаття має два `h2` з текстом `"Приклад"`
- **THEN** перший отримує `id="pryklad"`, другий — `id="pryklad-2"`; TOC посилається на відповідні anchors

### Requirement: Scroll-spy активного розділу
У клієнті блок `proger/toc` SHALL використовувати IntersectionObserver для визначення, який заголовок наразі видно у viewport. Пункт TOC, що відповідає активному заголовку, MUST отримувати клас `is-active` і атрибут `aria-current="location"`. Якщо у viewport потрапляє кілька заголовків — активним вважається найвищий (top-most).

#### Scenario: Користувач прокручує статтю
- **WHEN** заголовок `<h2 id="pryklad">` перетинає верхню межу viewport (offset 80 px для sticky header)
- **THEN** відповідний пункт у TOC отримує `is-active`; попередній активний пункт втрачає клас

### Requirement: Smooth scroll із клавіатурною підтримкою
Клік по пункту TOC SHALL виконувати smooth scroll до цільового заголовка із урахуванням sticky-header offset. Фокус MUST переходити на заголовок, щоб screen reader оголосив його.

#### Scenario: Клік по пункту TOC
- **WHEN** користувач клікає `a[href="#pryklad"]`
- **THEN** сторінка прокручується плавно до `#pryklad` (respecting `prefers-reduced-motion: reduce` — instant jump); фокус отримує `<h2 id="pryklad" tabindex="-1">`; URL оновлюється на `#pryklad` без перезавантаження

#### Scenario: Користувач має prefers-reduced-motion: reduce
- **WHEN** медіазапит `prefers-reduced-motion: reduce` активний і користувач клікає пункт TOC
- **THEN** scroll відбувається миттєво (`scrollTo({ behavior: 'auto' })`), без анімації

### Requirement: Sticky TOC на desktop, collapsible на mobile
TOC SHALL бути `position: sticky` у правій колонці на viewport ≥ `$bp-lg`. На менших viewport — перетворюється на collapsible accordion перед контентом статті, згорнутий за замовчуванням.

#### Scenario: Mobile перегляд статті
- **WHEN** viewport < `$bp-md` і сторінка — ArticlePage
- **THEN** TOC відображається як `<details>` із `<summary>"Зміст"</summary>`; стан згорнуто за замовчуванням; натискання на summary перемикає `open`

### Requirement: Вимкнення TOC через Customizer
Якщо `get_theme_mod('proger_enable_toc', true)` повертає false ABO у статті немає жодного заголовка h1–h4 — блок `proger/toc` SHALL рендерити порожній вивід (жодного DOM), а layout MUST перейти у двоколонкову конфігурацію.

#### Scenario: Адмін вимкнув TOC глобально
- **WHEN** `proger_enable_toc === false`
- **THEN** блок не рендериться, layout — `sidebar | main`, клас `has-no-toc` на `body`

### Requirement: Schema.org TableOfContents JSON-LD для SEO
Якщо блок `proger/toc` рендерить непорожній зміст, він SHALL додатково виводити JSON-LD `<script type="application/ld+json">` зі структурою `schema.org/TableOfContents`: `@context`, `@type: "TableOfContents"`, `name` (значення атрибута `title`), `itemListElement` — плаский масив `ListItem` з полями `position`, `name` (текст заголовка), `url` (permalink поста + `#<anchor-id>`). Вивід MUST бути ескейпленим через `wp_json_encode()` з прапорцем `JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES`. Вивід MUST відбуватись лише один раз на сторінку (навіть якщо блок випадково дубльовано).

#### Scenario: Стаття з заголовками генерує JSON-LD
- **WHEN** ArticlePage містить блок `proger/toc` із непорожнім списком заголовків
- **THEN** у HTML-виводі блоку присутній `<script type="application/ld+json">`, який валідується як `TableOfContents`; `itemListElement[0].url` == `get_permalink() . '#' . <slug-першого-заголовка>`; `position` починається з 1 і монотонно зростає

#### Scenario: Стаття без заголовків
- **WHEN** стаття не має жодного h1–h4 і TOC-розмітка не рендериться
- **THEN** JSON-LD блок також не виводиться; жодного порожнього `<script>` у DOM

#### Scenario: Сумісність із wordpress-seo
- **WHEN** на сайті активний плагін `wordpress-seo` (Yoast), який виводить власний `@graph` у JSON-LD
- **THEN** JSON-LD від `proger/toc` виводиться окремим `<script>`-блоком (не модифікуючи Yoast-graph); інструменти валідації structured data (Google Rich Results Test) не повідомляють про конфлікти
