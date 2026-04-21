# Progress

## Done
- The repository has a working custom theme with archive pages, search, single-post rendering, a custom code block, and a custom TOC block.
- Theme documentation now treats `proger-blog` as a separate maintainable artifact.
- Root documentation now describes the repository as a personal engineering blog project.
- The `proger-blog-theme` OpenSpec change now matches the shipped theme implementation, has been synced into `openspec/specs/`, and has been archived.

## In Progress
- Main specs under `openspec/specs/` are now the baseline planning layer for future theme work.
- The documentation base can be expanded further if future changes introduce more theme complexity.

## Remaining
- Add more technical docs only when new subsystems or new block patterns appear.
- Keep theme documentation synchronized when future blocks are introduced.

## Known Issues
- The theme depends on built assets and external runtime resources for some presentation features.
- The block editor intentionally differs from the frontend visual shell.

## Risks
- A future refactor could incorrectly assume this is a pure block theme.
- TOC behavior can break if heading slug generation drifts between modules.
- Build or deployment flow problems can leave the theme without required compiled assets.
