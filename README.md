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

Install the Middleware at `App\Http\Kernel` in `$routeMiddleware` by adding:

```php
'checkRefreshToken' => Albet\SanctumRefresh\Middleware\CheckRefreshToken
```

Optionally, you can also register provided routes in `RouteServiceProvider`:

```php
SanctumRefresh::routes();
```

Customizing routes can be performed by putting an associative array arguments.
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

You can manually perform login or refresh using provided
`LoginRequest->auth()` method.

Above method support username or email as identifier. Simply provide one of those when hitting the API.

If you need to know when refresh token expires. Simply use Carbon:
```php
Carbon::parse($token->created_at)->addMinutes(config('sanctum-refresh.refresh_expiration'))
```

Alternatively, SanctumRefresh also provide an Helpers:

```php
use Albet\SanctumRefresh\Helpers\Calculate;

Calculate::estimateRefreshToken($token->created_at);
```

Which simply a wrapper around `Carbon::parse`.

> Not Advised

Alternatively you can wrap around `AuthController::login()` method with your own controllers.
For refresh, we highly recommend you to wrap `AuthController::refresh()` method.

> Advised

Rather than wrap around controller above, You can just use below services:

```php
Albet\SanctumRefresh\Services\IssueToken::class
```

Allows you to generate both token and refresh token. Example:

Generating Token

```php
use Albet\SanctumRefresh\Services\IssueToken;

$user = User::first(); // Tokenable Model

(new IssueToken())->issue($user); // return TokenIssuer
```

The method above will take `$user` as an model reference. This model must inherit `HasApiTokens`
trait. The token will then be generated with given expiration provided not from 
`sanctum.php` config file. But `sanctum-refresh.php`.

Refreshing Token

```php
use Albet\SanctumRefresh\Services\IssueToken;

(new IssueToken())->refreshToken(); // return TokenIssuer
```

The function above will return the new token for access and a brand new Refresh Token. It will also automatically
revoke old token. Function above use `Request` under the hood. When using the service above, your API must be hit
with `refresh-token` cookie.

> When creating your own manual implementation of this package. It's highly recommended to deliver the cookie to the 
> response. Example:
> ```php
> $tokenIssuer = $token->getToken()->toArray(); // $token is instance of TokenIssuer
> 
> response()->json([
>     'message' => 'Authed Successfully!'
> ])->withCookie($tokenIssuer['cookie']); // the collection already converted to array 
> // using `toArray()`.
> ```

TokenIssuer Instance

This class only a mapper for `IssueToken`. This class contains method:

`getToken()`: which return collection that contain:

- token > the primary access token
- expires_in > the primary access token expiry minutes
- refresh_token > the token to used to regain old access token.
- refresh_token_expires_in > the refresh token expiry minutes
- cookie > contain `cookie()` that contain `refresh-token`. Ready to be injected. 

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
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
