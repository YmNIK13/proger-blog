# theme-article-card Specification

## Purpose
TBD - created by archiving change proger-blog-theme. Update Purpose after archive.
## Requirements
### Requirement: Helper-rendered article cards for archive views
The theme SHALL provide archive card rendering through `proger_blog_render_article_card(int $post_id)` in `inc/helpers.php`, rather than through a block pattern or template fragment.

Each rendered card SHALL output:

- `<article class="card-matter">`
- a primary category pill when a category exists
- up to two tag pills when tags exist
- the post date
- a linked title inside an `<h3>`
- a normalized excerpt trimmed for archive display

#### Scenario: Archive card on the blog index
- **WHEN** `index.php` loops through posts
- **THEN** each post is rendered through `proger_blog_render_article_card()` with a category badge, up to two tag badges, date, linked title, and excerpt text

### Requirement: Optional code preview or featured image fallback
If a post contains a `proger-blog/code` block, the article card SHALL render a short code preview extracted from the first such block. If no code block exists and the post has a featured image, the card SHALL render the featured image instead.

#### Scenario: Post contains a custom code block
- **WHEN** `proger_blog_extract_code_preview()` finds the first `proger-blog/code` block
- **THEN** the card renders a monospace preview with the detected language label and up to five lines of code

#### Scenario: Post has no custom code block but has a featured image
- **WHEN** no code preview is available and the post has a thumbnail
- **THEN** the card renders the `proger-card` image size with `loading="lazy"` and `decoding="async"`

### Requirement: Preview text normalization
Archive previews SHALL normalize imported or irregular whitespace before excerpt trimming.

#### Scenario: Imported text contains non-breaking or unusual spaces
- **WHEN** `proger_blog_normalize_preview_text()` processes the source text
- **THEN** the output collapses whitespace to regular wrapped prose before `wp_trim_words()` is applied

