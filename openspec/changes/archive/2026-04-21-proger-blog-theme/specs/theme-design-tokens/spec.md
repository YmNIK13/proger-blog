## ADDED Requirements

### Requirement: Canonical token source drives generated theme outputs
The theme SHALL use `src/tokens/design-tokens.json` as the canonical source of design tokens.

`scripts/sync-tokens.mjs` SHALL generate:

- `theme.json`
- `src/styles/tokens-root.css`

The script SHALL also support a `--check` mode for drift detection.

#### Scenario: Token source changes
- **WHEN** `src/tokens/design-tokens.json` is updated and `npm run sync:tokens` is executed
- **THEN** the generated `theme.json` and `src/styles/tokens-root.css` outputs are refreshed from the new token values

#### Scenario: CI or local drift check
- **WHEN** `npm run sync:tokens:check` is executed
- **THEN** the command exits non-zero if generated token outputs do not match the canonical token source

### Requirement: Tailwind reads the token source directly
The theme SHALL configure `tailwind.config.js` to read `src/tokens/design-tokens.json` directly so Tailwind color, typography, spacing, radius, shadow, and screen values stay aligned with the canonical token source.

#### Scenario: Token-backed utility classes
- **WHEN** a template or stylesheet uses Tailwind classes such as `bg-surface-container` or `text-on-surface-variant`
- **THEN** the generated utility values come from the current token source through the Tailwind configuration

### Requirement: Generated token outputs are not the hand-maintained source
`theme.json` and `src/styles/tokens-root.css` SHALL be treated as generated outputs, not as the canonical place to edit token values.

#### Scenario: Theme-level visual system update
- **WHEN** a maintainer needs to adjust colors, spacing, or typography tokens
- **THEN** the change starts in `src/tokens/design-tokens.json` rather than direct edits to generated outputs
