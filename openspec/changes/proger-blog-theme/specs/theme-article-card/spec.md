## ADDED Requirements

### Requirement: Картка статті для списків
Тема SHALL надавати pattern `patterns/article-card.php`, що рендерить картку допису з наступними елементами: заголовок (як `<h2><a>` на permalink посту), короткий превью (excerpt або перші 160 символів `the_content()` з `…`), список тегів (через стандартний блок `core/post-terms` з `term="post_tag"`), опційний snippet-превью коду (перший блок `proger/code` у пості, обмежений 6 рядками, без Prism, монохромний).

#### Scenario: Відображення картки в HomePage-списку
- **WHEN** pattern рендериться в `home.html` або `front-page.html` всередині `core/query-loop`
- **THEN** картка має розмітку `<article class="article-card">` з `<h2>`, `<p class="article-card__excerpt">`, рендер `core/post-terms` (`term="post_tag"`, клас-обгортка `article-card__tags`), опційним `<pre class="article-card__snippet">`; картка клікабельна (весь блок — клікабельна зона на заголовок), з hover-станом (transform + shadow зі токенів, 150–200 ms)

#### Scenario: Пост без тегів
- **WHEN** пост не має приписаних тегів
- **THEN** `core/post-terms` повертає порожню розмітку (стандартна поведінка ядра); картка не залишає порожнього простору

### Requirement: Превью коду як опційний snippet
Якщо пост містить хоча б один блок `proger/code`, pattern SHALL витягнути контент першого такого блоку (обмеження: 6 рядків, обрізання трикрапкою, `language` як атрибут `data-language`) і відрендерити усередині картки як `<pre class="article-card__snippet" data-language="<lang>">`. Snippet MUST NOT вмикати Prism-highlight на картці, щоб уникнути зайвого JS у списках. Якщо пост не має блоку `proger/code`, pattern MUST пропустити цю секцію.

#### Scenario: Пост із блоком JavaScript-коду
- **WHEN** пост має блок `proger/code` з `language="javascript"` і 20 рядками коду
- **THEN** у картці snippet показує перші 6 рядків + `…`; `data-language="javascript"`; моноширинний шрифт із токенів; межі — `--proger-color-border`

#### Scenario: Пост без блоків коду
- **WHEN** у пості немає `proger/code` блоку
- **THEN** картка рендериться без `<pre class="article-card__snippet">`; excerpt і теги залишаються як є

### Requirement: Теги через стандартний core/post-terms
Теги у картці SHALL виводитись стандартним блоком `core/post-terms` (`term="post_tag"`, `separator=" "`, `prefix=""`, `suffix=""`) без кастомної PHP-розмітки. Стилізація (відступи, колір, розмір шрифта з токенів) виконується виключно через SCSS, орієнтуючись на ядрові класи `wp-block-post-terms` та `wp-block-post-terms__separator`. Ліміт кількості тегів NOT застосовується на рівні pattern — порядок і склад формує WP ядро.

#### Scenario: Пост має теги
- **WHEN** pattern `article-card` рендериться для поста з тегами `#wordpress #bedrock #scss`
- **THEN** у картці з'являється `.wp-block-post-terms` з трьома `<a>`-посиланнями на архіви тегів; SCSS задає padding, radius `--proger-radius-sm`, колір акценту з токенів; hover-стан — 150–200 ms

#### Scenario: Пост без тегів
- **WHEN** пост не має тегів
- **THEN** `core/post-terms` рендерить порожнечу згідно зі стандартною поведінкою ядра; жодної додаткової PHP-логіки у pattern

### Requirement: Accessibility картки
Картка SHALL мати один головний лінк (у `<h2>`), не обгортати всю картку в `<a>`. Hover-ефект картки реалізується через `:focus-within`/`:has(h2 a:hover)` у SCSS.

#### Scenario: Клавіатурна навігація по списку карток
- **WHEN** користувач переходить Tab по списку карток
- **THEN** фокус отримує саме лінк у `<h2>` (не вся картка); `:focus-visible` стиль — outline від accent-токена

### Requirement: Lazy-loading зображень у картках
Якщо картка має featured image, вона SHALL рендеритись через `wp_get_attachment_image()` з `loading="lazy"` та `decoding="async"`. Розміри зображення — через `add_image_size('proger-card', 640, 360, true)`.

#### Scenario: Список карток у CategoryPage
- **WHEN** CategoryPage містить 10 карток з featured images
- **THEN** усі `<img>`-теги мають `loading="lazy"` крім першого (за патерном LCP-optimization — перший image отримує `fetchpriority="high"`); розмір зображень — 640×360
