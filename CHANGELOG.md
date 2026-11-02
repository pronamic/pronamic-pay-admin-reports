# Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](https://semver.org/) and [Keep a CHANGELOG](https://keepachangelog.com/).

## [Unreleased][unreleased]

## [1.1.1] - 2026-11-02

### Composer

- Changed `automattic/jetpack-autoloader` from `^3.0 || ^4.0 || ^5.0` to `^3.0 || ^4.0 || ^5.0 || ^6.0` to allow Jetpack Autoloader 6.x ([#2](https://github.com/pronamic/pronamic-pay-admin-reports/issues/2)). Jetpack Autoloader 6.0.0 only raises its minimum PHP version to 7.4. The lock file keeps `v5.0.21`. Release notes: https://github.com/Automattic/jetpack-autoloader/blob/v6.0.0/CHANGELOG.md

Full set of changes: [`1.1.0...1.1.1`][1.1.1]

[1.1.1]: https://github.com/pronamic/pronamic-pay-admin-reports/compare/v1.1.0...v1.1.1

## [1.1.0] - 2026-07-31

### Changed

- Widened the `woocommerce/action-scheduler` requirement to `^3.8 || ^4.0` to allow the Action Scheduler 4.0 series. [#1](https://github.com/pronamic/pronamic-pay-admin-reports/pull/1)
- Raised the minimum required WordPress version to 6.8. [#1](https://github.com/pronamic/pronamic-pay-admin-reports/pull/1)

### Dependencies

- Updated `woocommerce/action-scheduler` to [`3.9.3`](https://github.com/woocommerce/action-scheduler/releases/tag/3.9.3): adds WordPress 6.8 compatibility, implements `SKIP LOCKED` during action claiming, adds the `action_scheduler_ensure_recurring_actions` hook, and raises the minimum supported PHP version. (Action Scheduler stays on the 3.x line because `wp-pay/core` still requires `^3.8`.)

## [1.0.1] - 2024

Full set of changes: [`v1.0.0...v1.0.1`](https://github.com/pronamic/pronamic-pay-admin-reports/compare/v1.0.0...v1.0.1)

## [1.0.0] - 2024

First release.

[unreleased]: https://github.com/pronamic/pronamic-pay-admin-reports/compare/v1.1.1...HEAD
[1.1.0]: https://github.com/pronamic/pronamic-pay-admin-reports/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/pronamic/pronamic-pay-admin-reports/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/pronamic/pronamic-pay-admin-reports/releases/tag/v1.0.0
