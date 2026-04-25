# Customer Password Reset CLI for Magento 2

> Free, open-source Magento 2 extension  
> by **Arjun Dhiman** — 
> [Adobe Commerce Certified Master](https://magematch.com/developers/arjun-dhiman)  
> Part of the [MageMatch](https://magematch.com) 
> developer ecosystem

# Rameera Password Reset CLI

`Rameera_PasswordReset` provides a Magento CLI command that generates password reset tokens and triggers reset emails for selected customers.

## Features

- CLI command: `password:reset`
- Reset by exact email (`--email`)
- Reset by customer id lower bound (`--customer_id`)
- Reset for all customers (no option)
- Dedicated module log file: `var/log/passwordreset.log`

## Compatibility

- Magento Open Source / Adobe Commerce `2.4.4+`
- PHP `8.1`, `8.2`, `8.3`, `8.4`

## Installation

> Important: choose one installation mode only.

### Install from `app/code`

Place module in:

`app/code/Rameera/PasswordReset`

Then run:

```bash
php bin/magento module:enable Rameera_PasswordReset
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### Install with Composer

```bash
composer require arjundhi/magento2-customer-password-reset-cli
php bin/magento module:enable Rameera_PasswordReset
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Usage

Reset for one email:

```bash
php bin/magento password:reset --email customer@example.com
```

Reset for customer IDs greater than or equal to a value:

```bash
php bin/magento password:reset --customer_id 1000
```

Reset all customers:

```bash
php bin/magento password:reset
```

## CI Matrix

Workflow file: `.github/workflows/ci.yml`

Runs validation on:

- PHP `8.2`
- PHP `8.4`

Checks:

- `composer validate --no-check-lock --strict`
- PHP syntax lint (`php -l`)
- XML well-formedness

## License

This project is licensed under the MIT License. See `LICENSE`.

---
## Installation
```bash
composer require magematch/magento2-customer-password-reset-cli
bin/magento module:enable MageMatch_CustomerPasswordResetCli
bin/magento setup:upgrade
bin/magento cache:clean
```

## Compatibility
- Magento Open Source 2.4.x
- Adobe Commerce 2.4.x
- PHP 8.1, 8.2, 8.3

## Support & Custom Development
Need custom Magento development?  
Find vetted Adobe Commerce developers at  
**[magematch.com](https://magematch.com)**

## License
MIT License — free to use commercially
