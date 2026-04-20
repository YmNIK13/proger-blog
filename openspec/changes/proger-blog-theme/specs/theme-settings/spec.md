## ADDED Requirements

### Requirement: Customizer-панель теми
Тема SHALL реєструвати Customizer-секцію `proger_theme_options` з контролами: `proger_logo` (image/SVG через `WP_Customize_Image_Control`), `proger_background_image` (image), `proger_enable_toc` (checkbox, default `true`), `proger_enable_sidebar` (checkbox, default `true`). Значення SHALL зберігатися як theme_mods і доступні через `get_theme_mod()`.

#### Scenario: Адмін завантажує логотип
- **WHEN** адмін у Customizer завантажує SVG-файл у контрол `Logo`
- **THEN** theme_mod `proger_logo` зберігає attachment ID (або URL для зовнішніх); `core/site-logo` у header відображає вибране зображення; fallback — site title, якщо логотип не заданий

#### Scenario: Адмін вимикає TOC
- **WHEN** адмін знімає чекбокс `Enable Table of Contents` у Customizer
- **THEN** theme_mod `proger_enable_toc` стає `false`; усі ArticlePage рендеряться без правого sidebar; клас `has-no-toc` додається на `body`

### Requirement: Підтримка custom-logo стандартом WP
Тема SHALL викликати `add_theme_support('custom-logo', [ 'height' => 64, 'width' => 240, 'flex-height' => true, 'flex-width' => true, 'unlink-homepage-logo' => false ])` у `after_setup_theme`, щоб Customizer і Site Editor бачили логотип у стандартному блоці `core/site-logo`.

#### Scenario: Редактор відкриває Site Editor
- **WHEN** редактор відкриває Header template part у Site Editor
- **THEN** блок `core/site-logo` відображає завантажений логотип; властивості (width/height) успадковуються з theme support

### Requirement: Background image через CSS-змінну
Якщо `proger_background_image` заданий, тема SHALL додати inline-style у `<head>` (через `wp_add_inline_style`), що встановлює `--proger-body-bg-image: url(<url>)`. CSS `body { background-image: var(--proger-body-bg-image, none); }` застосовує його.

#### Scenario: Адмін задає фонове зображення
- **WHEN** `proger_background_image` має URL
- **THEN** у `<head>` з'являється `<style id='proger-blog-vars'>:root{--proger-body-bg-image:url("...");}</style>`; body отримує фон через CSS-змінну; відсутність значення — без inline-style

### Requirement: Безпека — nonces та санітизація Customizer-контролів
Усі контролі Customizer SHALL використовувати sanitize_callback (`sanitize_text_field`, `esc_url_raw`, `absint`, `rest_sanitize_boolean` відповідно). Поля, що приймають зображення, SHALL валідувати attachment MIME (image/png, image/jpeg, image/svg+xml, image/webp).

#### Scenario: Спроба зберегти недозволений MIME
- **WHEN** адмін намагається зберегти фонове зображення з MIME `application/x-msdownload`
- **THEN** sanitize_callback повертає `''`, значення не зберігається; у Customizer показується нотифікація про невалідний формат

### Requirement: Settings не впливають на performance негативно
Якщо жоден налаштованих опцій не ввімкнено (дефолти), тема NOT MUST генерувати додатковий inline CSS або JS. Customizer-значення MUST кешуватись за WP-стандартом (theme_mods autoload).

#### Scenario: Стандартна інсталяція без кастомізацій
- **WHEN** тема щойно активована, адмін не змінював налаштування
- **THEN** сторінка не містить inline `<style id='proger-blog-vars'>` (окрім базового `:root`-визначення з `_tokens.css`); JS-бандл включає тільки Interactivity-runtime без додаткових модулів
