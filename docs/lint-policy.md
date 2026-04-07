# Lint Policy (Targeted, Incremental)

This repository has legacy WordPress Coding Standards (WPCS) debt. To keep new work moving without lowering quality, use targeted linting during refactors.

The project keeps its existing class file naming convention (`*.class.php`). The PHPCS config in `phpcs.xml.dist` enforces WPCS while allowing that naming convention.

## Commands

- Full legacy-aware lint (expected to fail until full remediation):
  - `composer lint`
- Changed-files lint (policy gate for active work):
  - `composer lint:changed`

## What `lint:changed` does

- Finds changed tracked files (`git diff --name-only HEAD`) and untracked files.
- Filters to existing `*.php` files.
- Runs PHPCS (`WordPress` standard) only on those files.

## Optional legacy baseline

If a changed file contains pre-existing legacy violations you are not fixing in the current PR, you may temporarily add that file to:

- `.phpcs-changed-ignore`

Rules:

- One relative path per line.
- Keep list short and temporary.
- Remove entries when the file is remediated.
- Do not use ignores to hide regressions in newly-added code.

## Team recommendation

- Treat `composer lint:changed` as required for feature/refactor PRs.
- Run `composer lint` periodically as debt-burn work, not as a blocker for scoped migrations.
