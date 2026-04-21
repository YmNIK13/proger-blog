## ADDED Requirements

### Requirement: Custom block `proger-blog/code`
The theme SHALL provide a custom dynamic block named `proger-blog/code`.

The block metadata SHALL define:

- `content`
- `language`
- `filename`
- `showLineNumbers`

The block SHALL be registered from built metadata under `build/blocks/code`.

#### Scenario: Editor inserts a code block
- **WHEN** an editor inserts `proger-blog/code` in the block editor
- **THEN** the editor UI exposes a textarea for code content, a language selector, an optional filename field, and a line-number toggle

### Requirement: Server-rendered code block markup
The frontend SHALL render the block through `render.php` as a `<figure>` wrapper with a header row, language label, optional filename, copy button, and `<pre><code>` body.

#### Scenario: Code block with filename
- **WHEN** the block has `language="typescript"` and `filename="src/index.ts"`
- **THEN** the rendered block contains a language label, the filename, a copy button, and a `<code class="language-typescript">` element

### Requirement: Frontend highlighting and copy behavior
The block SHALL provide frontend behavior through `view.js`.

That behavior SHALL:

- lazy-load Prism from `jsDelivr` when needed
- lazy-load supported language components as required
- skip highlighting for `plaintext`
- support copy-to-clipboard
- fall back to a temporary textarea copy flow when Clipboard API is unavailable

#### Scenario: Supported language highlighting
- **WHEN** a rendered block uses a supported language such as `php` or `javascript`
- **THEN** Prism is loaded on demand and the code element is highlighted after the language component is available

#### Scenario: Clipboard API is unavailable
- **WHEN** `navigator.clipboard` is not available
- **THEN** the copy action falls back to a textarea-based `document.execCommand('copy')` flow

### Requirement: Optional line-number presentation mode
If `showLineNumbers` is enabled, the rendered wrapper SHALL include a `proger-code--line-numbers` modifier class so the stylesheet can apply line-number styling.

#### Scenario: Line numbers enabled
- **WHEN** `showLineNumbers` is `true`
- **THEN** the block wrapper includes the line-number modifier class and the code block renders in the alternate styled mode
