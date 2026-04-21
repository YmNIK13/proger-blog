# System Patterns

## Architecture Overview
- The repository is a **Bedrock** WordPress site.
- The main custom behavior lives in the `proger-blog` theme.
- The theme combines classic PHP templates, `theme.json`, custom blocks, and compiled assets.

## Key Components
- `functions.php` bootstraps theme modules
- `inc/` contains reusable PHP support modules
- `src/blocks/` contains source for custom Gutenberg blocks
- `src/styles/` contains the global styling entrypoint and generated token variables
- `src/tokens/design-tokens.json` is the canonical design token source
- `build/` contains compiled runtime assets and built block metadata

## Important Flows
- archive rendering flows through shared article card helpers
- single-post rendering flows through the TOC block and heading anchor injection
- token changes flow from `design-tokens.json` through `sync-tokens.mjs`
- block source changes flow from `src/blocks/*` to `build/blocks/*`

## Invariants
- custom blocks are registered from `build/blocks/*`
- token-driven files are generated, not hand-maintained
- the block editor intentionally does not load the full frontend theme style layer
- TOC heading slug logic must stay aligned between anchor injection and TOC rendering
