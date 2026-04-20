## ADDED Requirements

### Requirement: Custom block proger/code для кодових прикладів
Тема SHALL надавати кастомний блок `proger/code` (namespace `proger-blog`), призначений для вставки кодових фрагментів. Блок MUST реєструватися через `register_block_type_from_metadata(__DIR__ . '/blocks/code')` з `block.json`, що визначає атрибути: `content` (string), `language` (string, enum із предвизначеного списку), `filename` (string, optional), `showLineNumbers` (boolean, default false).

#### Scenario: Редактор вставляє блок proger/code
- **WHEN** автор статті у Gutenberg-редакторі вибирає блок `proger/code` і задає `language="typescript"`, вставляє TS-сніпет
- **THEN** блок зберігається з атрибутами у post content; фронтенд рендерить `<pre class="proger-code" data-language="typescript"><code class="language-typescript">...</code></pre>` із header-рядком, що містить лейбл `TS`, назву файлу (якщо задано) і кнопку `Copy`

### Requirement: Syntax highlighting через Prism.js
Клієнтський скрипт блоку SHALL завантажувати Prism.js лише на сторінках, де присутній хоча б один блок `proger/code`. Підтримувані мови: `javascript`, `typescript`, `php`, `bash`, `json`, `css`, `scss`, `html`, `sql`, `yaml`, `diff`, `markdown`, `plaintext`.

#### Scenario: Сторінка містить блок PHP-коду
- **WHEN** читач відкриває ArticlePage із блоком `proger/code` з `language="php"`
- **THEN** Prism ініціалізується на клієнті, PHP-токени отримують класи `.token.keyword`, `.token.string` тощо, стилі беруть кольори з токенів stitch (`var(--proger-color-code-*)`)

#### Scenario: Мова не підтримується
- **WHEN** блок має `language="erlang"` (не в списку підтримуваних)
- **THEN** у редакторі selector `language` не містить цього варіанта; на фронті код рендериться без підсвічування, лейбл показує `PLAINTEXT`, помилок у консолі немає

### Requirement: Copy-to-clipboard кнопка
Кожен блок `proger/code` SHALL мати кнопку `button[aria-label="Копіювати код"]` у header-рядку блоку. Натискання SHALL копіювати оригінальний код (без HTML-тегів підсвічування) у clipboard через `navigator.clipboard.writeText`. Після успіху кнопка показує стан `is-copied` (текст `Скопійовано`) на 2000 ms.

#### Scenario: Користувач натискає Copy
- **WHEN** користувач клікає кнопку `Copy` у блоці
- **THEN** `navigator.clipboard.writeText(attributes.content)` викликається; кнопка змінює aria-label на `Скопійовано`, клас `is-copied` додається; через 2 с клас і текст повертаються до початкового стану

#### Scenario: Clipboard API недоступний (insecure context)
- **WHEN** сторінка відкрита по `http://` (не HTTPS) і `navigator.clipboard` — undefined
- **THEN** використовується fallback через `document.execCommand('copy')` із тимчасовим `<textarea>`; у разі повної невдачі кнопка показує `aria-label="Не вдалося скопіювати"` і клас `is-error` на 2 с

### Requirement: Лейбл мови та опційна назва файлу
Header блоку SHALL відображати мітку мови великими літерами (`TS`, `PHP`, `BASH`) зліва і опційну назву файлу `data-filename` — справа від мітки. Header MUST мати контрастний фон із токенів (`--proger-color-code-header-bg`).

#### Scenario: Блок із filename
- **WHEN** редактор вказав `filename="src/index.ts"`
- **THEN** header рендерить `<span class="proger-code__lang">TS</span>` і `<span class="proger-code__filename">src/index.ts</span>`; на mobile `filename` обрізається з ellipsis

### Requirement: Horizontal scroll замість обрізання
Контейнер `<pre>` SHALL мати `overflow-x: auto` і custom scrollbar-стилізацію (`scrollbar-width: thin`, `scrollbar-color` з токенів). Довгі рядки NOT wrap (за замовчуванням); `white-space: pre` жорстко.

#### Scenario: Рядок коду довший за контейнер
- **WHEN** блок містить рядок довжиною 200 символів у контейнері шириною 600 px
- **THEN** з'являється горизонтальний скроллбар (тонкий, стилізований); рядок не переноситься; highlight не ламається

### Requirement: Line numbers (опційно)
Атрибут `showLineNumbers` SHALL вмикати колонку номерів рядків зліва від коду, синхронізовану з копіюванням (номери не потрапляють у clipboard).

#### Scenario: Блок з showLineNumbers=true
- **WHEN** редактор увімкнув `showLineNumbers`
- **THEN** рендериться псевдоелемент із нумерацією через `counter-reset` + `counter-increment` у SCSS, без дублювання тексту у DOM; `Copy` копіює лише код без номерів
