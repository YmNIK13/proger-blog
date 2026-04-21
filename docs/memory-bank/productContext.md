# Product Context

## Problem
- A default WordPress theme does not fit a personal engineering blog that needs strong content readability, code presentation, category navigation, and long-form article structure.

## Users
- The site owner as the author and maintainer
- Readers looking for practical engineering notes and setup guides
- Future maintainers or agents working on the repository

## Desired Experience
- Readers should be able to browse categories, search content, and read long technical articles comfortably.
- Technical posts should support code samples and structured navigation through headings.
- Theme-level behavior should stay simple enough to extend without rewriting the site shell.

## Key Workflows
- Publish a new article through WordPress
- Browse archive and category pages
- Read a single article with TOC and code blocks
- Add new theme capabilities, including future custom blocks

## Product Tradeoffs
- The theme uses classic PHP templates for predictable frontend rendering.
- Gutenberg is used where it provides reusable content units, especially custom blocks.
- The editor experience is intentionally not a full visual copy of the frontend shell.
