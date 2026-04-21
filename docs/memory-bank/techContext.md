# Tech Context

## Stack
- PHP `>=8.4`
- WordPress `6.9.4`
- Bedrock
- WP-CLI
- `@wordpress/scripts`
- Tailwind CSS
- PostCSS
- Sass
- Stylelint
- Prism.js
- Pest
- Pint

## Structure
- environment configuration: `.env`, `.env.example`, `config/application.php`
- content root: `web/app`
- custom theme: `web/app/themes/proger-blog`
- deep technical doc: `docs/technical/architecture.md`

## Commands
- repository:
  - `composer install`
  - `composer lint`
  - `composer test`
- theme:
  - `npm ci`
  - `npm run build`
  - `npm run start`
  - `npm run sync:tokens`
  - `npm run sync:tokens:check`
  - `npm run lint:js`
  - `npm run lint:css`

## Constraints
- the theme requires compiled `build/` assets
- `theme.json` and `src/styles/tokens-root.css` are generated from tokens
- runtime presentation depends partly on external services for fonts and Prism highlighting

## Integrations
- installed plugins include `autoptimize`, `cyrlitera`, `wordpress-seo`, `wp-optimize`, and `wp-super-cache`
- theme code explicitly integrates with `autoptimize` exclusions
- design tokens originate from a Stitch export workflow at development time
