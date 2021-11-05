# Laravel Sync Database

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ldavidsp/laravel-sync.svg?style=flat-square)](https://packagist.org/packages/ldavidsp/laravel-sync)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/ldavidsp/laravel-sync.svg?style=flat-square)](https://packagist.org/packages/ldavidsp/laravel-sync)

This package contains some useful Artisan commands to work the synchronization of the local database with the production one.

## Requirements
This package requires Laravel 6 or newer.

## Installation

You can install the package via Composer:

``` bash
composer require ldavidsp/laravel-sync
```

Publish the config file with:

```bash
php artisan vendor:publish --tag=sync-config
```

Add the configuration for the production database to `database.php`:

```bash
'live-db' => [
  'driver' => env('DB_LIVE_CONNECTION', 'mysql'),
  'host' => env('DB_LIVE_HOST', 'your live server database host here'),
  'port' => env('DB_LIVE_PORT', '3306'),
  'database' => env('DB_LIVE_DATABASE', 'forge'),
  'username' => env('DB_LIVE_USERNAME', 'forge'),
  'password' => env('DB_LIVE_PASSWORD', ''),
],
```

## Usage

Synchronize database:
``` bash
php artisan sync:db
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
