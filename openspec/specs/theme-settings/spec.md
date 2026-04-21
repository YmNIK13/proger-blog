# theme-settings Specification

## Purpose
TBD - created by archiving change proger-blog-theme. Update Purpose after archive.
## Requirements
### Requirement: Theme-level Customizer section
The theme SHALL register a Customizer section named `proger_theme_options`.

That section SHALL manage:

- `proger_background_image`
- `proger_enable_toc`
- `proger_enable_sidebar`

#### Scenario: TOC is disabled in the Customizer
- **WHEN** `proger_enable_toc` is set to `false`
- **THEN** the theme omits TOC-driven layout affordances and adds the matching body class

#### Scenario: Sidebar is disabled in the Customizer
- **WHEN** `proger_enable_sidebar` is set to `false`
- **THEN** the theme does not render the sidebar and removes the sidebar layout offset

### Requirement: Standard WordPress logo support
The theme SHALL use standard WordPress `custom-logo` support rather than a custom logo setting.

#### Scenario: Custom logo is configured
- **WHEN** a custom logo exists
- **THEN** the header renders the logo through the standard theme support flow

#### Scenario: No custom logo is configured
- **WHEN** no custom logo exists
- **THEN** the header falls back to the site name link

### Requirement: Sanitized background image handling
The background image setting SHALL accept only allowed image MIME types and SHALL be injected as a CSS variable through the theme stylesheet handle.

#### Scenario: Valid image URL is saved
- **WHEN** the background image value passes MIME validation
- **THEN** the theme injects `--proger-body-bg-image` through `wp_add_inline_style()`

#### Scenario: Invalid MIME is provided
- **WHEN** the configured value does not resolve to an allowed image MIME type
- **THEN** the sanitized setting resolves to an empty value

### Requirement: Theme mods drive body classes
The theme SHALL add body classes reflecting TOC and sidebar visibility.

#### Scenario: TOC and sidebar are both disabled
- **WHEN** both toggles are disabled
- **THEN** the body class list includes both `has-no-toc` and `has-no-sidebar`

