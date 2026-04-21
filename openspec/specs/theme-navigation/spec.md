# theme-navigation Specification

## Purpose
TBD - created by archiving change proger-blog-theme. Update Purpose after archive.
## Requirements
### Requirement: Sticky header navigation
The theme SHALL provide a sticky header through `header.php` with:

- custom logo fallback to site name
- a primary navigation menu
- a toggleable search shell
- a mobile menu toggle

The primary menu SHALL use the registered `primary` menu location and SHALL fall back to category links if no menu is assigned.

#### Scenario: No primary menu is assigned
- **WHEN** `wp_nav_menu()` is called for the `primary` location and no custom menu is configured
- **THEN** the theme renders the fallback category-based menu provided by `proger_blog_fallback_menu()`

### Requirement: Sidebar navigation with menu and category fallback
The theme SHALL provide a separate `sidebar` menu location and SHALL render a fallback list when no sidebar menu is assigned.

The fallback SHALL include:

- a “Latest” link to the home page
- top-level categories
- icon selection derived from the item label

#### Scenario: Sidebar menu is not assigned
- **WHEN** the `sidebar` menu location has no assigned menu
- **THEN** the theme renders the fallback list from `proger_blog_sidebar_fallback_menu()`

#### Scenario: Category archive is active
- **WHEN** the current request is a category archive and the fallback sidebar is used
- **THEN** the matching category link receives active-state styling and `aria-current="page"`

### Requirement: Header interaction behavior
The theme SHALL provide JavaScript-driven header behavior through `src/scripts/interactivity/nav.js`.

That behavior SHALL:

- toggle the mobile menu panel
- toggle and focus the search shell
- add an `is-scrolled` class after the page scroll threshold is crossed
- close open header overlays on `Escape`

#### Scenario: Mobile menu toggle
- **WHEN** the mobile menu button is pressed
- **THEN** the mobile navigation panel is shown or hidden and `aria-expanded` is updated on the trigger

#### Scenario: Search shell toggle
- **WHEN** the search toggle is pressed
- **THEN** the header search shell expands and focus moves to the search input

