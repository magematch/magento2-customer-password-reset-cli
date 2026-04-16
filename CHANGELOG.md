# Changelog

All notable changes to `arjundhi/magento2-customer-password-reset-cli` are documented here.

## [Unreleased]

## [1.0.0] - 2026-04-16

### Added
- Initial standardized public module packaging.
- CLI command support for reset by email, customer id threshold, and all customers.
- Logger support in `var/log/passwordreset.log`.
- CI workflow for composer/php/xml checks.

### Changed
- Namespace migrated from `Hudson\\PasswordReset` to `Rameera\\PasswordReset`.
- Module name migrated from `Hudson_PasswordReset` to `Rameera_PasswordReset`.
- `Reset` command refactored for stricter typing and safer search criteria handling.
- Composer metadata modernized for PHP 8.1–8.4 and Magento 2.4 compatibility.
