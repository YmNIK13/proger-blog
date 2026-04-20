---
name: wordpress-router
description: "Use when the user asks about WordPress codebases (plugins, themes, block themes, Gutenberg blocks, WP core checkouts) and you need to quickly classify the repo and route to the correct workflow/skill (blocks, theme.json, REST API, WP-CLI, performance, security, testing, release packaging)."
compatibility: "Targets WordPress 6.9+ (PHP 7.2.24+). Filesystem-based agent with bash + node. Some workflows require WP-CLI."
---

# WordPress Router

## When to use

Use this skill at the start of most WordPress tasks to:

- identify what kind of WordPress codebase this is (plugin vs theme vs block theme vs WP core checkout vs full site),
- pick the right workflow and guardrails,
- delegate to the most relevant domain skill(s).

## Inputs required

- Repo root (current working directory).
- The user’s intent (what they want changed) and any constraints (WP version targets, WP.com specifics, release requirements).

## Procedure

1. Inspect the repo layout first:
   - Bedrock/full site signals: `composer.json`, `config/application.php`, `web/app/`, `web/wp/`, `web/wp-config.php`, `wp-cli.yml`
   - Classic full site signals: root `wp-config.php`, `wp-content/`
   - Plugin/theme repo signals: plugin header, `style.css`, `block.json`, `theme.json`
2. Classify:
   - primary project kind(s),
   - whether the site layout is Bedrock or classic,
   - tooling available (PHP/Composer, Node, @wordpress/scripts),
   - tests present (PHPUnit, Playwright, wp-env),
   - any version hints.
3. Route to domain workflows based on user intent + repo kind:
   - For the decision tree, read: `.agents/skills/wordpress-router/references/decision-tree.md`.
4. Apply guardrails before making changes:
   - Confirm any version constraints if unclear.
   - Prefer the repo’s existing tooling and conventions for builds/tests.
   - In Bedrock repos, treat `web/app` as the content root, `web/wp` as Composer-managed core, and `config/application.php` plus `config/environments/*.php` as the real config surface.

## Verification

- Re-check the layout signals if you create or restructure significant files.
- Run the repo’s existing lint/test/build commands that match the detected tooling (if available).

## Failure modes / debugging

- If classification is still unclear, inspect:
  - root `composer.json`, `package.json`, `config/application.php`, `web/app/`, `web/wp/`, `web/wp-config.php`, `wp-cli.yml`, `style.css`, `block.json`, `theme.json`, `wp-content/`.
- If the repo is huge, consider narrowing scanning scope before classifying it.

## Escalation

- If routing is ambiguous, ask one question:
  - “Is this intended to be a WordPress plugin, a theme (classic/block), or a full site repo such as Bedrock?”
