# React Admin Migration Test Worksheet

Use this worksheet to validate feature parity while migrating the admin settings UI from PHP-rendered forms to React.

## Test Metadata

- [ ] Tester:
- [ ] Date:
- [ ] Environment (local/staging):
- [ ] WordPress version:
- [ ] PHP version:
- [ ] Plugin version/branch:

## Global Acceptance Criteria

- [ ] React UI loads inside existing plugin admin page without PHP warnings/errors.
- [ ] Current permissions model remains intact (`manage_options` gate still effective).
- [ ] Nonce/auth flow works for every REST action.
- [ ] Existing setting keys and action names are unchanged (`update_setting`, `manual_scan`, `discover_log`, `apply_debug`, `fetch_logs`, `test`).
- [ ] Toast/feedback messaging appears for success and failure states.
- [ ] No regressions in cron scheduling side-effects when relevant settings change.
- [ ] Styling remains consistent with current plugin admin UI.
- [ ] No new external runtime dependencies are introduced.

## Monitor Tab

- [ ] `monitor_enabled` toggle renders and reflects persisted value on load.
- [ ] Toggling `monitor_enabled` sends correct payload and persists.
- [ ] `scan_frequency_mins` displays current value on load.
- [ ] `scan_frequency_mins` enforces min/max constraints in UI.
- [ ] `scan_frequency_mins` persists valid values.
- [ ] Invalid `scan_frequency_mins` values show validation feedback.
- [ ] `log_retention_days` displays current value on load.
- [ ] `log_retention_days` updates persist correctly.
- [ ] `Run Scan Now` triggers `manual_scan`.
- [ ] Manual scan success/failure message is visible and accurate.
- [ ] Header metadata remains accurate after manual scan and page refresh.
- [ ] With monitoring disabled, scheduled scans are skipped and manual scan still works.

## Email Tab

- [ ] SMTP fields load current values: `username`, `password`, `host`, `port`, `from_email`, `to_email`.
- [ ] Field updates call `update_setting` with correct key/value.
- [ ] Settings persist after refresh.
- [ ] Invalid field responses mark field as invalid (e.g. `em-invalid`).
- [ ] Invalid state clears on user input.
- [ ] Test buttons stay disabled when settings are incomplete.
- [ ] Test buttons enable when settings are complete (matching current logic).
- [ ] `Test Connection` sends `test` with `{ type: "smtp" }`.
- [ ] SMTP test shows console output and toast feedback.
- [ ] SMTP test failure is handled without breaking UI.
- [ ] `Send Test Email` sends `test` with `{ type: "email" }`.
- [ ] Test email success/failure feedback is clear and accurate.
- [ ] No unexpected full form submission/navigation occurs.

## Log File Tab

- [ ] Status panel renders path, source, exists, readable values.
- [ ] `log_file_path` loads current saved value.
- [ ] Updating `log_file_path` persists correctly.
- [ ] `Auto Discover Log File` triggers `discover_log`.
- [ ] Auto-discover success updates saved path and visible status.
- [ ] `WP_DEBUG` toggle sends `apply_debug` with `wp_debug`.
- [ ] `WP_DEBUG_LOG` toggle sends `apply_debug` with `wp_debug_log`.
- [ ] `WP_DEBUG_DISPLAY` toggle sends `apply_debug` with `wp_debug_display`.
- [ ] Debug toggle actions show success/failure feedback.
- [ ] Non-writable `wp-config.php` failure is handled without UI desync.
- [ ] Existing backup/edit behavior of config updates remains unchanged.

## Logs Tab

- [ ] Initial log content renders on tab load.
- [ ] `Grouped View` triggers `fetch_logs` with `view=grouped`.
- [ ] `Raw View` triggers `fetch_logs` with `view=raw`.
- [ ] Returned log HTML replaces display container correctly.
- [ ] Empty-state message renders correctly when no logs exist.
- [ ] Large log output renders without JS errors.
- [ ] Repeated view switching does not duplicate or corrupt content.

## Cross-Tab Integration

- [ ] Tab switching does not cause unexpected state loss (or matches current behavior).
- [ ] Header indicators remain accurate across tab actions.
- [ ] REST errors (400/403/500) show clear user feedback.
- [ ] Capability restrictions prevent unauthorized action execution.
- [ ] Network/interruption failures do not leave controls permanently broken.
- [ ] Browser refresh reflects persisted backend state.

## Regression + Standards

- [ ] Existing endpoint/controller behavior remains functionally equivalent.
- [ ] JS build/lint passes (`@wordpress/scripts` workflow).
- [ ] PHP coding standards pass for all touched PHP files.
- [ ] Existing naming conventions and public identifiers are preserved.
- [ ] No obsolete PHP form bindings remain referenced after React cutover.

## Notes / Defects Found

- [ ] None
- [ ] Issue 1:
- [ ] Issue 2:
- [ ] Issue 3:

## Final Sign-off

- [ ] Ready for merge
- [ ] Follow-up fixes required
