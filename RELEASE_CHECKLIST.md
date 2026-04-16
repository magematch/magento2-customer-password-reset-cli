# Release Checklist

## Code Quality

- Validate `composer.json` with strict mode.
- Run PHP lint across module files.
- Verify XML files are well-formed.

## Functional Checks

- `php bin/magento password:reset --email <email>` works.
- `php bin/magento password:reset --customer_id <id>` works.
- `php bin/magento password:reset` works.
- Confirm log output in `var/log/passwordreset.log`.

## Release Prep

- Update `CHANGELOG.md`.
- Commit changes.
- Push annotated tag (example: `v1.0.0`).
- Publish GitHub release.
- Trigger Packagist update.

## Install Verification Matrix

- Stable install: `composer require arjundhi/magento2-customer-password-reset-cli:^1.0`
- Dev install: `composer require arjundhi/magento2-customer-password-reset-cli:"dev-main@dev"`
