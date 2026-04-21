## ADDED Requirements

### Requirement: Custom block `proger-blog/toc`
The theme SHALL provide a custom dynamic block named `proger-blog/toc`.

The block SHALL support:

- `maxLevel`
- `title`

The block SHALL render nothing when TOC output is disabled or when no headings are found.

#### Scenario: TOC disabled by theme setting
- **WHEN** `get_theme_mod('proger_enable_toc', true)` is `false`
- **THEN** the block returns no markup

#### Scenario: Post contains no matching headings
- **WHEN** no headings up to the configured `maxLevel` are found
- **THEN** the block returns no markup

### Requirement: TOC uses shared heading extraction and anchor logic
The TOC system SHALL use shared helper logic from `inc/toc-anchors.php`.

That shared behavior SHALL:

- generate deterministic slugs
- support Cyrillic and duplicate-heading disambiguation
- inject heading IDs into rendered post content
- extract headings for TOC rendering

#### Scenario: Duplicate headings exist in a post
- **WHEN** two headings normalize to the same slug
- **THEN** the later heading receives a numeric suffix and the TOC links to the suffixed anchor

### Requirement: TOC markup includes nested navigation and JSON-LD
The block SHALL render nested ordered-list navigation and SHALL emit a `TableOfContents` JSON-LD script once per request when headings exist.

#### Scenario: TOC is rendered more than once on a page
- **WHEN** the block appears multiple times during rendering
- **THEN** the JSON-LD script is emitted only once for the request

### Requirement: Frontend TOC behavior
The block SHALL provide frontend behavior through `view.js`.

That behavior SHALL:

- support click-to-scroll with header offset
- respect `prefers-reduced-motion`
- focus the target heading after navigation
- maintain active-link state through `IntersectionObserver`
- close floating TOC panels on explicit close actions or `Escape`

#### Scenario: Reader clicks a TOC entry
- **WHEN** a TOC link is activated
- **THEN** the page scrolls to the matching heading, the URL hash is updated, and focus is moved to the heading

#### Scenario: Reader scrolls through the article
- **WHEN** a tracked heading becomes the active reading position
- **THEN** the matching TOC link receives the active-state class and `aria-current="location"`
