# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.3.0] - 2025-11-06
### :sparkles: New Features
- [`c6f51ef`](https://github.com/wipop-bbva/wipop-php-client/commit/c6f51ef4e82680daf17314931a605015b3494d61) - **charge**: add charge method signatures and params *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`477b8f8`](https://github.com/wipop-bbva/wipop-php-client/commit/477b8f8679210700be769951484f4961837b5922) - **charge**: add capture parameter to ChargeParams and related tests *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`5f6a50e`](https://github.com/wipop-bbva/wipop-php-client/commit/5f6a50ecc0d41a46fc369c8784894df1b2a52a04) - **examples**: add bizum charge example *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`8037ae1`](https://github.com/wipop-bbva/wipop-php-client/commit/8037ae1bc039494c500ce07b2098b2cc2ab28f06) - **charge**: add CardParams for COF (MIT & CIT) *(commit by [@victorvierna](https://github.com/victorvierna))*

### :bug: Bug Fixes
- [`d52311d`](https://github.com/wipop-bbva/wipop-php-client/commit/d52311d5e8daa410d26c24fbcd7c65741e4954c2) - **phpstan**: update ignored errors path to CheckoutOperationTest *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`4529027`](https://github.com/wipop-bbva/wipop-php-client/commit/45290272adb5cb8f460c754787f8dc7b2383f834) - **params**: correct customer parameters and path ID *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`ecf9906`](https://github.com/wipop-bbva/wipop-php-client/commit/ecf990629328c3f90eacaf20540ef7677dcda759) - **PostType**: update return type in toArray method to use string for mode *(commit by [@victorvierna](https://github.com/victorvierna))*

### :recycle: Refactors
- [`93ec25e`](https://github.com/wipop-bbva/wipop-php-client/commit/93ec25efba67104d2873e13db76c1a5cd8e05d1c) - **tests**: update CheckoutService references to checkoutOperation *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`6951d16`](https://github.com/wipop-bbva/wipop-php-client/commit/6951d16c9b8fc638565a4e2d7be3688e0dfbd936) - **charge operation**: refactor charge operation tests *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`f966803`](https://github.com/wipop-bbva/wipop-php-client/commit/f966803a80fd8c879c528b14f3ff9c9ac942f814) - **tests**: replace Client instantiation with GuzzleHttpClient in charge operation tests *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`d572475`](https://github.com/wipop-bbva/wipop-php-client/commit/d572475700713fd218c3fad81db6da3a956707ed) - **docs**: update test descriptions *(commit by [@victorvierna](https://github.com/victorvierna))*

### :wrench: Chores
- [`5592f3d`](https://github.com/wipop-bbva/wipop-php-client/commit/5592f3df45adc400176af6df70d90985b1f4fcd7) - **example**: add card charge example script *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`eef4d24`](https://github.com/wipop-bbva/wipop-php-client/commit/eef4d24c38c6b812dbb69fb41de8cbbfe71146db) - **merge**: resolve merge issues *(commit by [@victorvierna](https://github.com/victorvierna))*

[v0.3.0]: https://github.com/wipop-bbva/wipop-php-client/compare/v0.2.1...v0.3.0
