## ADDED Requirements

### Requirement: HomePage з Hero, списком статей і тегами
Тема SHALL надавати шаблон `templates/home.html` (та `templates/front-page.html` якщо на сайті налаштована статична головна), що складається з секцій: Hero (великий заголовок сайту + підзаголовок + блок пошуку `core/search`), опційна Featured-стаття (один найсвіжіший пост із `sticky=true`), список `ArticleCard` (до 10 за replacement, `core/query-loop` з pagination), cloud of tags (TOP-20 тегів як бейджі).

#### Scenario: Користувач відкриває головну
- **WHEN** користувач переходить на `/`
- **THEN** рендериться: `<section class="hero">` із site title + search; якщо є `sticky_posts` — секція `featured` з однією великою карткою; потім `<section class="articles">` із grid-списком `ArticleCard`; у футері секції — pagination; далі `<section class="tags-cloud">` з топ-20 тегами

#### Scenario: Немає sticky-постів
- **WHEN** у базі немає постів із `sticky=true`
- **THEN** featured-секція не рендериться; список статей стартує одразу після hero

### Requirement: CategoryPage
Тема SHALL надавати шаблон `templates/category.html`, що рендерить заголовок категорії (name + description), список `ArticleCard` з пагінацією (query-loop, відфільтрований за поточною категорією через `inherit` у `core/query`), активує відповідний пункт у лівому sidebar (через `is-active` клас за `is_category()`), зберігає SEO-friendly URL вигляду `/category/<slug>/` (стандартна WP rewrite rule).

#### Scenario: Читач відкриває /category/dev/
- **WHEN** URL — `/category/dev/`, категорія з slug `dev` існує
- **THEN** шаблон рендерить заголовок категорії "Dev"; список `ArticleCard` показує тільки пости з цією категорією; sidebar має активним лінк `Dev`; title-тег сторінки — `Dev — Proger.Click` (через `wp_title` або SEO-плагін)

#### Scenario: Категорія порожня
- **WHEN** категорія існує, але не має постів
- **THEN** сторінка показує `<p class="no-posts">Ще немає статей у цій категорії</p>`; layout залишається з лівим sidebar

### Requirement: ArticlePage з типографікою, CodeBlock і TOC
Тема SHALL надавати шаблон `templates/single.html`, що рендерить: breadcrumbs (опційно, через SEO-плагін), `<h1>` заголовок, meta-рядок (дата публікації, автор, час читання, категорії, теги), featured image, контент поста (`core/post-content`), правий sidebar із блоком `proger/toc`. Стилізована типографіка (заголовки h1–h4, абзаци, цитати `blockquote`, списки, `code` inline, `pre`/`proger-code` блоки, таблиці, зображення з caption).

#### Scenario: Користувач читає статтю
- **WHEN** користувач на `single.html` з постом, що містить h2, h3, `proger/code` та зображення
- **THEN** заголовки мають anchor-id (додані фільтром `the_content`); inline-код (`<code>` всередині абзаців) отримує фон `--proger-color-code-bg-inline` і padding; блоки `proger/code` рендеряться як описано у specs/theme-code-block; TOC у правому sidebar synchronizes зі scroll

#### Scenario: Короткий пост без заголовків
- **WHEN** пост не має h1–h4
- **THEN** блок `proger/toc` рендерить порожнечу; layout перетворюється на двоколонковий (sidebar | main), клас `has-no-toc` додається на body

### Requirement: 404 та search
Тема SHALL надавати шаблони `templates/404.html` (ілюстрація + пошукова форма + лінк на HomePage) та `templates/search.html` (список `ArticleCard` за `core/query` з inherit).

#### Scenario: Неіснуючий URL
- **WHEN** користувач переходить на `/nonexistent/`
- **THEN** сервер повертає HTTP 404, рендериться `404.html` з повідомленням "Сторінку не знайдено", пошук, лінк на `/`

### Requirement: Pagination
Архіви (`home.html`, `category.html`, `search.html`) SHALL використовувати `core/query-pagination` з кнопками `Попередня` / `1 2 … N` / `Наступна`. Активна сторінка — `aria-current="page"`.

#### Scenario: Друга сторінка категорії
- **WHEN** користувач переходить на `/category/dev/page/2/`
- **THEN** рендеряться пости 11..20; пагінація показує `1 [2] 3 …`; title не змінюється (SEO-плагін керує canonical)
