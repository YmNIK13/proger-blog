## ADDED Requirements

### Requirement: Blog index presentation through `index.php`
The theme SHALL provide the main blog archive through `index.php`.

The page SHALL include:

- a hero heading
- the site description or fallback intro text
- the shared search form
- a grid of article cards
- pagination when multiple pages exist
- a tag cloud section when tags are available

#### Scenario: Blog home has posts
- **WHEN** the main query returns posts
- **THEN** `index.php` renders the hero, article card grid, pagination if needed, and the tag cloud section

#### Scenario: Blog home has no posts
- **WHEN** the main query returns no posts
- **THEN** `index.php` renders the empty-state message instead of the card grid

### Requirement: Category and search templates
The theme SHALL provide dedicated templates for category archives and search results.

#### Scenario: Category archive request
- **WHEN** a category archive is requested
- **THEN** `category.php` renders the category title, optional description, article cards, pagination, and empty-state messaging when appropriate

#### Scenario: Search request
- **WHEN** a search results page is requested
- **THEN** `search.php` renders the query heading, shared search form, article cards, pagination, and empty-state messaging when appropriate

### Requirement: Single-post presentation
The theme SHALL provide a dedicated single-post presentation through `single.php`.

That template SHALL render:

- breadcrumbs
- post title
- author and date metadata
- derived reading time
- featured image when available
- post content
- tag links
- TOC affordances when TOC markup is available

#### Scenario: Single post with headings and TOC enabled
- **WHEN** the post content produces TOC markup and `proger_enable_toc` is enabled
- **THEN** `single.php` renders the TOC rail or floating TOC affordances alongside the article

### Requirement: 404 presentation
The theme SHALL provide a dedicated `404.php` template with:

- a 404 message
- a link back to the home page
- a search action

#### Scenario: Unknown URL
- **WHEN** WordPress resolves a request to the theme’s 404 template
- **THEN** the page renders the custom 404 presentation instead of a generic fallback
