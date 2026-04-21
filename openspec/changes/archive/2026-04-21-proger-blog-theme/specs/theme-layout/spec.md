## ADDED Requirements

### Requirement: Classic template-based frontend shell
The theme SHALL render the frontend shell through classic PHP templates rather than block-template HTML files.

The active shell SHALL be composed from:

- `header.php`
- `footer.php`
- `index.php`
- `single.php`
- `category.php`
- `search.php`
- `404.php`
- `sidebar.php`

#### Scenario: Frontend request resolves to theme output
- **WHEN** WordPress resolves a request handled by the theme
- **THEN** the page shell is assembled from the classic PHP template hierarchy rather than from `templates/*.html`

### Requirement: Shared archive layout with optional sidebar
Archive-like templates SHALL use a shared shell with:

- a sticky header
- an optional left sidebar
- a main content column
- article cards
- pagination when multiple pages exist

#### Scenario: Sidebar enabled on large viewports
- **WHEN** `proger_enable_sidebar` is enabled and the viewport reaches the large-screen layout
- **THEN** the sidebar is rendered and the main content receives the matching left offset

#### Scenario: Sidebar disabled
- **WHEN** `proger_enable_sidebar` is disabled
- **THEN** the sidebar is not rendered and the main content is displayed without the sidebar offset

### Requirement: Single-post layout with optional TOC rail
The single-post layout SHALL center the article content inside a light content surface on a dark site shell and SHALL optionally add TOC affordances.

#### Scenario: TOC available on very wide screens
- **WHEN** a single post has TOC content and the viewport is at the desktop TOC breakpoint
- **THEN** the page renders a sticky right-side TOC rail beside the article

#### Scenario: TOC available below the desktop TOC breakpoint
- **WHEN** a single post has TOC content but the viewport is below the desktop TOC rail breakpoint
- **THEN** the page renders floating TOC access controls instead of the desktop TOC rail

### Requirement: Semantic structure and skip-link
The theme SHALL include a skip-link targeting `#main` and SHALL render semantic landmarks for header, navigation, main content, sidebar, and footer surfaces.

#### Scenario: Keyboard user lands on the page
- **WHEN** the first focusable element receives focus
- **THEN** the skip-link becomes available and points to the main content target
