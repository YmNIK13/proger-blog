# Project Brief

## Overview
- This repository powers **Proger.Click**, a personal WordPress-based knowledge blog.
- The main custom code lives in `web/app/themes/proger-blog`.
- The site is used to publish practical notes on development, infrastructure, server setup, and environment configuration.

## Goals
- Maintain a stable publishing platform for technical content.
- Keep the custom theme extensible, especially for future custom blocks.
- Preserve enough documentation for future maintenance sessions without re-auditing the whole repository.

## Scope
- Bedrock and WordPress project setup
- the `proger-blog` theme
- supporting technical and project documentation
- planning artifacts under `openspec/`

## Constraints
- PHP `>=8.4`
- WordPress `6.9.4`
- Node.js `>=20` for theme builds
- `theme.json` and `src/styles/tokens-root.css` are generated from `src/tokens/design-tokens.json`
- the theme depends on built artifacts under `web/app/themes/proger-blog/build/`

## Success Criteria
- The repository remains easy to operate and extend.
- The theme can grow with additional blocks without architectural confusion.
- The main documentation stays aligned with the shipped implementation.
