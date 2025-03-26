# Changelog

## 1.0 (Pre-Release)
The early prototype of how Sanctum-Refresh will work. This release contains main functionality of 
Sanctum-Refresh. However it's still fully untested and not yet work.

## 1.0.1 (Beta Release)
This release has been released with full test code coverage. However it's untested in real laravel
application.

- Fix Known Bugs
- Add Services (To allows customization)
- New Helper to estimate refreshToken.

## 1.0.2
This release has been tested with real laravel application and works well.

- Fix known bugs

## 1.0.3

- Remove unnecessary `User::find` queries when refreshing token.

## 2.0.0
This release has major rewrite of internal of Sanctum Refresh. Fully changed the flow in order to gives
developer flexibility over expiration of both tokens.

- New reworked PruneToken command
- Removed Calculate:
```php
Albet\SanctumRefresh\Helpers\Calculate::class
```

- Added Helpers for checking refresh token validity.
```php
Albet\SanctumRefresh\Helpers\CheckForRefreshToken::class
```

- Added helpers for building array using named parameters:
```php
config_builder(
    abilities: ['*'],
    tokenExpiresAt: now(),
    refreshTokenExpiresAt: now()
);
```

- Renaming Old IssueToken to TokenIssuer:

    From
    ```php
    Albet\SanctumRefresh\Services\IssueToken
    ```
    
    To
    ```php
    Albet\SanctumRefresh\Services\TokenIssuer
    ```

- Renaming Old TokenIssuer to Token:

    From
    ```php
    Albet\SanctumRefresh\Services\Contracts\TokenIssuer
    ```
    
    To
    ```php
    Albet\SanctumRefresh\Services\Contracts\Token
    ```

- Added `$config` in `issue()` and `refresh()` methods of `TokenIssuer`.
- `TokenIssuer::issue()` and `TokenIssuer::refresh()` now return `Token::getToken()` by default.
- Added `HasRefreshable` trait:

```php
Albet\SanctumRefresh\Traits\HasRefreshable::class
```

- Added `HasRefreshableTokens` trait:

```php
Albet\SanctumRefresh\Traits\HasRefreshableToken::class
```

- Added `RefreshTokenRepository` class:

```php
Albet\SanctumRefresh\Repositories\RefreshTokenRepository::class
```

## 2.0.1

Apart from being 1 version difference from beta, this version introduces major changes including breaking ones:

- Removed `Albet\SanctumRefresh\Helpers\CheckForRefreshToken` class
- Removed `config_builder` in favor of `Albet\SanctumRefresh\Factories\TokenConfig` class
- Removed `getToken` from `Albet\SanctumRefresh\Factories\Token` class
- Removed `sanctum_refresh` entry from the `sanctum_refresh.php` config file
- Removed `AuthController` and its affected routes (`login`, `refresh`)
- Removed `InvalidModelException`, `InvalidTokenException`, `MustExtendsHasApiTokens` exceptions in favor of `SanctumRefreshExceptions`
- Removed `Albet\SanctumRefresh\Helpers\CheckForRefreshToken` helper in favor of `Albet\SanctumRefresh\Helpers::getRefreshToken`
- Renamed `revokeRefreshTokenFromTokenId` to `revokeFromTokenId` in `RefreshTokenRepository`
- Renamed `revokeRefreshTokenFromToken` to `revokeFromTokenText` in `RefreshTokenRepository`
- Removed `LoginRequest`
- Removed `boot` and `routes` method in SanctumRefresh facade
- Renamed `HasRefreshable` to `HasRefreshableToken`
- Added `WithRefreshable` trait for relationship with `RefreshToken` model

and many more changes can be check on: https://github.com/albetnov/sanctum-refresh/compare/2.0.0-beta...2.x
