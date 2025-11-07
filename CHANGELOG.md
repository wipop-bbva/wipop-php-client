# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.4.0] - 2025-11-07
### :sparkles: New Features
- [`8f8bf64`](https://github.com/wipop-bbva/wipop-php-client/commit/8f8bf64b7b57187926918840879aed9f53a989a9) - **CaptureParams**: add CaptureParams class with amount validation *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`3562270`](https://github.com/wipop-bbva/wipop-php-client/commit/3562270915d3955e7a278221b070060fe0b7f1b6) - **PaymentMethod**: add PaymentMethod class with type and URL properties *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`f6f6f74`](https://github.com/wipop-bbva/wipop-php-client/commit/f6f6f7491cf0d15ab7be86fc8ef474dc6c178f3a) - **RefundParams**: add RefundParams class for handling refund parameters *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`f98065d`](https://github.com/wipop-bbva/wipop-php-client/commit/f98065d1fe7817f6b6a8f5d7a91ba87116d3766b) - **ReversalParams**: add ReversalParams class for handling preauth reversals *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`2991544`](https://github.com/wipop-bbva/wipop-php-client/commit/29915441c84da88d3b53f63886f1ecbd975acd01) - **Refund**: create Refund class extending Transaction *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`77027c6`](https://github.com/wipop-bbva/wipop-php-client/commit/77027c6d97b233418f76582757688cf1a1b0e2c4) - **PreauthChargeOperationTest**: add test for preauthorization flow management *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`88a86f3`](https://github.com/wipop-bbva/wipop-php-client/commit/88a86f3e38ec1ba07f3623eb5f7435934c764ea5) - **CofChargeOperationTest**: add test for card tokenization and gateway charge flow *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`9c6910c`](https://github.com/wipop-bbva/wipop-php-client/commit/9c6910cfa192d70bfe207a746cbf069f86efe7b6) - **ChargeParams**: add dueDate method for setting payment due date *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`168ec00`](https://github.com/wipop-bbva/wipop-php-client/commit/168ec0047b6eb0796d06e8045609a15ed506b4c6) - **docs**: update README and add LEEME.md for comprehensive Wipop PHP client documentation following ts and Java SDKs *(commit by [@victorvierna](https://github.com/victorvierna))*

### :bug: Bug Fixes
- [`71fa4c4`](https://github.com/wipop-bbva/wipop-php-client/commit/71fa4c4675208841ab38709f07c0b81d1f546f67) - **composer**: move vlucas/phpdotenv to require-dev section *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`57f041e`](https://github.com/wipop-bbva/wipop-php-client/commit/57f041e2b630ee936c9ce8a9122398b2f2c0590e) - **GuzzleHttpClient**: ensure secret key ends with a colon for proper authorization *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`7aa8b29`](https://github.com/wipop-bbva/wipop-php-client/commit/7aa8b293e84be2413dc0ece17cdef98ffb336184) - **CheckoutParams**: update expiration date format to ISO as in docs *(commit by [@victorvierna](https://github.com/victorvierna))*

### :recycle: Refactors
- [`7a19b50`](https://github.com/wipop-bbva/wipop-php-client/commit/7a19b5065bbbeabd1f31fabb8a6a90ca587cbfab) - **api**: map API responses to domain models using serializer *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`f8f7621`](https://github.com/wipop-bbva/wipop-php-client/commit/f8f7621c95d188228b2e06b284bfcebc1636500b) - **ChargeOperation**: remove unused confirm method *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`a56ecaa`](https://github.com/wipop-bbva/wipop-php-client/commit/a56ecaa7a051ef7e86fe0767dbdd1e6174d45cf4) - **Charge**: don't rename classname to align with the other libraries *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`0c0ced3`](https://github.com/wipop-bbva/wipop-php-client/commit/0c0ced35e8d5cb967450b465728db13c5ea96d14) - **AddressPayload**: remove unused AddressPayload class *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`6071bc6`](https://github.com/wipop-bbva/wipop-php-client/commit/6071bc694fa6032d5b413f0ebfdc69faf0f5dab7) - **tests**: replace string literals with constants *(commit by [@victorvierna](https://github.com/victorvierna))*

### :wrench: Chores
- [`72fbea8`](https://github.com/wipop-bbva/wipop-php-client/commit/72fbea8e011785862b2f234b335aaa9ca6f41ad5) - **preauth**: add example scripts for development validation on sandbox env *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`6aada3a`](https://github.com/wipop-bbva/wipop-php-client/commit/6aada3a6d94deff6126cbb5b44297ba1501a7a91) - **test**: add orderId to various charge and checkout operations *(commit by [@victorvierna](https://github.com/victorvierna))*

[v0.4.0]: https://github.com/wipop-bbva/wipop-php-client/compare/v0.3.0...v0.4.0
