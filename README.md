# Proger.Click

This repository powers [https://proger.click/](https://proger.click/), my personal blog for publishing notes about software development, server setup, environment configuration, infrastructure, and practical engineering workflows.

The site is built on **WordPress** with **Bedrock**. The main custom artifact in the repository is the `proger-blog` theme under `web/app/themes/proger-blog`.

## Project Purpose

The project is a personal knowledge base with a public frontend. It is used to publish:

- development guides
- server and environment setup notes
- infrastructure and operations instructions
- technical explanations and reference material

The repository therefore combines:

- platform and environment configuration
- a custom content theme
- supporting documentation for future maintenance

## Stack

- **Bedrock**
- **WordPress** `6.9.4`
- **PHP** `>=8.4`
- **Node.js** `>=20` for theme builds
- **Tailwind CSS**, **PostCSS**, `@wordpress/scripts`
- **Pest** and **Pint**

## Main Repository Areas

- `config/` — Bedrock and environment configuration
- `web/app/themes/proger-blog/` — custom theme and most project-specific code
- `web/app/plugins/` — installed plugins
- `docs/memory-bank/` — durable project context for future sessions
- `docs/technical/architecture.md` — technical architecture for the `proger-blog` theme
- `openspec/` — planning artifacts for larger scoped changes

## Getting Started

1. Prepare environment variables:

   ```bash
   cp .env.example .env
   ```

   Then fill in database credentials, site URLs, and WordPress salts.

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Build the custom theme:

   ```bash
   cd web/app/themes/proger-blog
   npm ci
   npm run build
   ```

4. Activate the theme if needed:

   ```bash
   wp theme activate proger-blog --path=web/wp
   ```

## Useful Commands

### Repository level

```bash
composer lint
composer test
```

### Theme level

```bash
cd web/app/themes/proger-blog

npm run start
npm run build
npm run sync:tokens
npm run sync:tokens:check
npm run lint:js
npm run lint:css
```

## Theme Note

The `proger-blog` theme is the main custom product artifact in this repository. It is documented separately so it can evolve over time, including future custom blocks:

- [web/app/themes/proger-blog/README.md](web/app/themes/proger-blog/README.md)
- [docs/technical/architecture.md](docs/technical/architecture.md)

## Documentation

- `docs/memory-bank/` is the short-form project context layer
- `docs/technical/architecture.md` is the detailed technical map of the theme
- `openspec/` holds planning artifacts rather than runtime documentation
