## ADDED Requirements

### Requirement: Sticky site header з багаторівневим меню
Тема SHALL надавати site header як template part `parts/header.html` з компонентами: логотип (через `core/site-logo`, джерело — Customizer), багаторівнева навігація (через `core/navigation` у режимі multi-level з підтримкою вкладених `core/navigation-submenu`), кнопка мобільного меню, search toggle (опційно). Header MUST бути `position: sticky; top: 0` та отримувати клас `is-scrolled` після прокрутки > 8 px.

#### Scenario: Користувач прокручує сторінку вниз
- **WHEN** `window.scrollY > 8`
- **THEN** Interactivity-store `proger/nav` встановлює `state.scrolled = true`, елементу header додається клас `is-scrolled`, фонова непрозорість/тінь змінюються через CSS

#### Scenario: Підменю другого рівня
- **WHEN** користувач наводить мишею (`:hover`) або фокусує клавіатурою (`:focus-within`) елемент меню, що має дочірнє підменю
- **THEN** підменю стає видимим з анімацією (150–200 ms), керується `aria-expanded` на кнопці-відкривачі

### Requirement: Mobile dropdown
На viewport < `$bp-md` основне меню SHALL ховатися і замінюватися на кнопку `button[aria-label="Відкрити меню"][aria-expanded]`. Натискання кнопки перемикає off-canvas панель із повним деревом навігації.

#### Scenario: Натискання кнопки меню на мобільному
- **WHEN** користувач клікає `button[data-wp-on--click="actions.toggleMenu"]`
- **THEN** `state.menuOpen` перемикається, панелі додається клас `is-open`, body отримує `overflow: hidden`, `aria-expanded` оновлюється, фокус переходить на перший лінк у панелі

#### Scenario: Закриття меню клавішею Escape
- **WHEN** `state.menuOpen === true` і користувач натискає `Escape`
- **THEN** панель закривається, фокус повертається на кнопку-тригер

### Requirement: Sidebar категорій — дерево з активним станом
Тема SHALL надавати лівий sidebar як pattern `patterns/sidebar-categories.php`, який SSR-рендерить всі публічні категорії (`get_categories(['hide_empty' => true])`) як вкладене `<ul>`-дерево (враховує `parent`). Активна категорія (визначається через `is_category()` або `get_queried_object()`) MUST отримувати клас `is-active` та атрибут `aria-current="page"`.

#### Scenario: Користувач переходить на CategoryPage
- **WHEN** URL — `/category/<slug>/` і `is_category()` істинне
- **THEN** у sidebar лінк з `data-slug="<slug>"` отримує `is-active` + `aria-current="page"`; батьківські категорії (якщо є) розкриті (клас `is-expanded`)

#### Scenario: Клік по категорії
- **WHEN** користувач клікає лінк категорії
- **THEN** навігація виконується як звичайний HTTP-перехід (без SPA-хаків) на `/category/<slug>/`; жодних preventDefault

### Requirement: Клавіатурна доступність
Уся навігація (sidebar + header menu) SHALL бути повністю доступна з клавіатури: Tab — перехід між фокусованими елементами, Enter/Space — активація, Escape — закриття підменю/off-canvas, Arrow keys — рух між сусідніми пунктами меню у межах одного рівня.

#### Scenario: Перехід між пунктами верхнього меню стрілками
- **WHEN** фокус на пункті верхнього меню і користувач натискає `ArrowRight`
- **THEN** фокус переходить на наступний пункт того ж рівня; на останньому — на перший (циклічно)
