# Sanctum Refresh

> This package is still under development. Awas meledak.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)

A Package to extend Sanctum to have refresh token as well.

## Installation

You can install the package via composer:

```bash
composer require albetnov/sanctum-refresh
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="sanctum-refresh-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="sanctum-refresh-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * Sanctum Token eexpiration in minutes.
     */
    'expiration' => 2, // 2 minutes

    /**
     * Sanctum Refresh Token expiration in minues.
     */
    'refresh_expiration' => 30, // 30 minutes
];
```

## Usage

Install the Middleware at `App\Http\Kernel` in `$routeMiddleware` add:

```php
'checkRefreshToken' => Albet\SanctumRefresh\Middleware\CheckRefreshToken
```

Optionally, you can register provided routes in `RouteServiceProvider`:

```php
SanctumRefresh::routes();
```

Customizing routes can be perfomed by putting associative array arguments.
Full config example:

```php
SanctumRefresh::routes([
    // set the authentication success message
    'authedMsg' => trans('auth.authed'),
    // disable login route.
    'refreshOnly' => true,
    // set login url
    'loginUrl' => '/login',
    // set refresh url
    'refreshUrl' => '/auth/refresh',
    // set login route middlewares
    'loginMiddleware' => 'admin',
    // set refresh route middlewares
    'refreshMiddleware' => ['can_refresh', 'is_mobile']
]);
```

## Going Manual

You can manually perform login nor refresh using provided
`LoginRequest->auth()` method.

If you need refresh token expires in. Simply use Carbon:
```php
Carbon::parse($token->created_at)->addMinutes(config('sanctum-refresh.refresh_expiration'))
```

Alternatively, SanctumRefresh provide an Helpers:

```php
use Albet\SanctumRefresh\Helpers\Calculate;

Calculate::estimateRefreshToken($token->created_at);
```

Which simply a wrapper around `Carbon::parse`.

Alternatively you can wrap around `AuthController::login()` method with your own controllers.
For refresh, we highly recommend you to wrap `AuthController::refresh()` method.

You can even have more control using Services provided by

```php
Albet\SanctumRefresh\Services\IssueToken::class
```

and

```php
Albet\SanctumRefresh\Services\Contracts\TokenIssuer::class
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

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Albet Novendo](https://github.com/albetnov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
