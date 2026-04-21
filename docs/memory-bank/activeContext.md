# Active Context

## Current Focus
- Documentation has been reframed in English.
- The theme is now documented as a standalone artifact that can host future custom blocks.
- The root project README is now positioned as repository-level documentation for the personal blog.
- The OpenSpec change `proger-blog-theme` has been synced to main specs and archived.

## Recent Changes
- `docs/technical/architecture.md` was rewritten to focus on the `proger-blog` theme rather than the full site.
- `README.md` was rewritten as project documentation for the personal blog at `https://proger.click/`.
- Theme-level documentation was rewritten around extension points and block growth.
- `openspec/changes/proger-blog-theme/` was rewritten to describe the shipped theme and then archived after syncing specs into `openspec/specs/`.

## Next Steps
- Keep theme documentation and main OpenSpec specs aligned when new blocks or new rendering surfaces are added.
- Add more deep docs only if a future change introduces meaningful new complexity.

## Open Questions
- Whether Prism should remain CDN-loaded or move into a local bundle
- Whether the current plain-JS interaction layer should eventually migrate to WordPress Interactivity API stores

## Active Decisions
- Treat `web/app/themes/proger-blog` as the primary custom artifact in the repository.
- Treat `docs/technical/architecture.md` as the deep technical reference for theme work.
