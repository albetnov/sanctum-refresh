# Sanctum Refresh

[![Latest Version on Packagist](https://img.shields.io/packagist/v/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)
[![Coverage](https://img.shields.io/badge/coverage-100%25-lime)](https://albetnov.github.io/sanctum-refresh/)

Minimal and flexible package to extend Sanctum to have refresh token as well.

## Installation

You can install the package via composer:

```bash
composer require albetnov/sanctum-refresh
```

Then you'll need to push and run the migration with:

```bash
php artisan vendor:publish --tag="sanctum-refresh-migrations"
php artisan migrate
```

You can also publish the config file with:

```bash
php artisan vendor:publish --tag="sanctum-refresh-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * Set the fallback expiration time of both tokens
     * Time in minutes.
     */
    'expiration' => [
        // set the fallback of access token expiration
        'access_token' => 2, // 2 minutes,
        // set the fallback of refresh token expiration
        'refresh_token' => 30, // 30 minutes
    ],
];
```

## Quick Start

In order to Sanctum-Refresh, you just need to acknowledge these API:

- Creating token

```php
<?php

namespace App\Http\Controllers;

use Albet\SanctumRefresh\TokenIssuer;

class TokenController {
    function newToken() {
        $token = TokenIssuer::issue($request->user(), guard: 'api');

        return response()->json([
            'message' => 'Token generated successfully!',
'data' => [
    'access_token' => $token->accessToken->plainTextToken,
]
        ]);
    }
}
```

## Testing

Run the tests:

```bash
composer test
```

Figure out the code coverage:

```bash
composer test-coverage
```

## Changelog

Please see [Changelog](CHANGELOG.md) for more information.


## Contributing

You are free to contribute to this project.

## Credits

- [Albet Novendo](https://github.com/albetnov)
- [Laravel Sanctum](https://github.com/laravel/sanctum)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
