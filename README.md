# Sanctum Refresh

[![Latest Version on Packagist](https://img.shields.io/packagist/v/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/albetnov/sanctum-refresh/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/albetnov/sanctum-refresh/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/albetnov/sanctum-refresh.svg?style=flat-square)](https://packagist.org/packages/albetnov/sanctum-refresh)
[![Coverage](https://img.shields.io/badge/coverage-100%25-lime)](https://albetnov.github.io/sanctum-refresh/)

A Package to extend Sanctum to have refresh token as well.

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

    /**
     * Set the message to be used by the api response
     */
    'message' => [
        'authed' => 'Authentication success!',
        'invalid' => 'Refresh token is expired or invalid.',
    ],
];
```

## Quick Start

The easiest way to start with this package are using the provided scaffold.

First, install the provided Middleware at `App\Http\Kernel` in `$routeMiddleware` by adding:

```php
'checkRefreshToken' => Albet\SanctumRefresh\Middleware\CheckRefreshToken
```

Then register the routes by putting code above in any service providers. E.g. `AuthServiceProvider`:

```php
SanctumRefresh::routes();
```

After that the routes should be accessible with given config urls. E.g. `/login`, `/refresh`. Or you can just
look into it by performing:

```bash
php artisan route:list
```

The routes will also register under the name `login` and `refresh` assuming you put the routes not inside a 
grouping with name prefix.

The `login` routes accepts `username` or `email` as user identifier and takes `password` for the password.

In the other hand the `refresh` routes accepts neither `refresh_token` cookie or `refresh_token` json body.

> Both of the above urls are accessible with `POST` method.

## Going Manual

Sanctum Refresh make it easy and painless for you to for performing an manual integration with your project
and this package.

- Auth Scaffolding:
    
    Sanctum Refresh provide an auth scaffold. This scaffold can be used for your custom controllers.
    
    ```php
    Albet\SanctumRefresh\Requests\LoginRequest::class
    ```
    
    The `LoginRequest` provide `auth()` method to help you authenticate the user by either `username` or `email` and finally
    `password`. 

- CheckForRefreshToken:
    
    It is unnecessary for you to use `CheckRefreshToken` if you don't want to. For instance, you may need to modify on how
    the middleware will take Refresh Token. You can achieve this will minimal code using the provided:
    
    ```php
    Albet\SanctumRefresh\Helpers\CheckForRefreshToken::check($refreshToken);
    ```
    
    Simply pass the `$refreshToken` as a string and you're set. The Helpers will take care validating
    the entire thing for you and return `bool`.
    
    An example usage (CheckRefreshToken middleware):
    ```php
    // Check refresh token.
    $refreshToken = $request->hasCookie('refresh_token') ?
        $request->cookie('refresh_token') :
        $request->get('refresh_token');

    if (!$refreshToken) {
        return response()->json([
            'message' => SanctumRefresh::$middlewareMsg,
        ], 400);
    }

    if (!CheckForRefreshToken::check($refreshToken)) {
        return response()->json([
            'message' => SanctumRefresh::$middlewareMsg,
        ], 400);
    }

    return $next($request);
   
    ```
    Above is `CheckRefreshToken` middleware code.

- Custom PersonalAccessToken Model
    
    Since version 2. Sanctum Refresh no longer overriding any codes from Sanctum. Instead, this package wraps around it.
    With that being said, You're now free to modify whatever you want with the `PersonalAccessToken` Model. This is 
    important if you want to use this package in a already exist project.
    Simply put:
    
    ```php
    use Custom\Models\PersonalAccessToken;
    Albet\SanctumRefresh\SanctumRefresh::usePersonalAccessTokenModel(PersonalAccessToken::class);
    ```
    
    In any service provides. The model though must extend `HasApiToken` from Sanctum.
    
    - HasRefreshable Trait (PersonalAccessToken extension)
    
    Sanctum Refresh also provide:
    
    ```php
    Albet\SanctumRefresh\Traits\HasRefreshable::class
    ```
    
    Above trait will inject relationship to your custom `PersonalAccessToken` model.

- TokenIssuer

    Just like before, SanctumRefresh also provide `TokenIssuer`:
    
    ```php
    Albet\SanctumRefresh\Services\TokenIssuer::class
    ```
    
    This class contains two methods:
    
    ```php
    Albet\SanctumRefresh\Services\TokenIssuer::issue($model, $name, $config)
    ```
    
    Above method will generate a token complete with refresh token. The method takes 3 arguments. The Tokenable Model, 
    token name, and finally config (expiration, etc).
    
    ```php
    Albet\SanctumRefresh\Services\TokenIssuer::refreshToken($refreshToken, $name, $config)
    ```
    
    Above method will regenerate a token. But instead of based on Tokenable Model. This method will regenerate the token
    based on given Refresh Token. This method takes 3 arguments. The plain refresh token string, the new token name, 
    and config (expiration, etc).
    
    Both methods above return `Token` instance.
    
- HasRefreshableToken Trait (User Model)
    
    Instead of having pain putting `$model` over and over in `TokenIssuer`. You can just use `HasRefreshableToken` trait in
    your user model:
    
    ```php
    <?php
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Laravel\Sanctum\HasApiTokens;
    use Albet\SanctumRefresh\Traits\HasRefreshableToken;
    
    class User extends Model {
        use HasApiTokens, HasFactory, HasRefreshableToken;
    ...
    ```
    
    Above is the example of your final User model will look like.
    
    This trait will provide you with 2 methods.
    
    - createTokenWithRefresh($name, $config)
        
        Create an access token as well as refresh token. A wrapper around `TokenIssuer::issue()` without `$model`.
    
    - revokeBothTokens()
    
        Revoke both access token and refresh token.

- RefreshTokenRepository
    
    Finally, Sanctum Refresh also provide you a repository. As it's name suggest. This repository will help you with
    Revoking Refresh Token (Without revoking the access token).
    This repository provides you with 2 methods.
    
    - Revoke refresh token from given id
    
        `revokeRefreshTokenFromTokenId($id)` will revoke / delete the refresh token from given `$id`. This `$id` must be
        an id of RefreshToken table.
    - Revoke refresh token from plain token
        
        `revokeRefreshTokenFromToken($stringToken)` will revoke / delete the refresh token from given
        plain token.

That's all!

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
