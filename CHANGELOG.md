# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.2.0] - 2025-11-04
### :sparkles: New Features
- [`c5ce7d6`](https://github.com/wipop-bbva/wipop-php-client/commit/c5ce7d61bfa31d5f3ccb3053944b912a0e766f7f) - implement checkout API client with payload factories *(commit by [@victorvierna](https://github.com/victorvierna))*

### :recycle: Refactors
- [`ceca8d8`](https://github.com/wipop-bbva/wipop-php-client/commit/ceca8d8e4a1d3d89b04991859d3921e5f2a52593) - **exceptions**: introduce dedicated more semantic Wipop API exception types *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`9d13145`](https://github.com/wipop-bbva/wipop-php-client/commit/9d1314530d46fff7b83f790d34aeba545e2d58de) - **payload**: validate checkout responses with OptionsResolver *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`bb63bff`](https://github.com/wipop-bbva/wipop-php-client/commit/bb63bff78678f17be6ff04c41de926aeb8c2d2ca) - **customerFactory**: implement NullObject pattern to handle empty Customers *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`664892f`](https://github.com/wipop-bbva/wipop-php-client/commit/664892f45dc28d0ccb924730186576a6b11e8687) - **CheckoutResponse**: change customer type to CustomerInterface *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`40b0861`](https://github.com/wipop-bbva/wipop-php-client/commit/40b0861bf1b978ef9f4ed3f26c40dd97c910acc1) - **CustomerFactory**: remove unused DateTimeInterface import *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`b458bfd`](https://github.com/wipop-bbva/wipop-php-client/commit/b458bfdbe041e947642aaa2b4bab403b8cb0823e) - **HttpClient**: Abstract Http implementation from the client with new custom adapter and exceptions. *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`47e52b1`](https://github.com/wipop-bbva/wipop-php-client/commit/47e52b19442e32f34480c5adae68669acec6604e) - **Wipop client**: decouple Guzzle initialization *(commit by [@victorvierna](https://github.com/victorvierna))*

### :white_check_mark: Tests
- [`07dc245`](https://github.com/wipop-bbva/wipop-php-client/commit/07dc245b4bba7b2e2b77cdaa950562cf84f8084d) - **factories**: add factory test to assert expected build behaviour *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`74543cc`](https://github.com/wipop-bbva/wipop-php-client/commit/74543cc9e347a65bc0ff67dd6a1e487c6dbf108e) - **WipopClient**: add unit test for default HTTP client configuration *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`d1584a0`](https://github.com/wipop-bbva/wipop-php-client/commit/d1584a07777f17bbf30717aa32f68f06e1d1b54b) - **WipopClient**: fix assert condition on number *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`13a3d2d`](https://github.com/wipop-bbva/wipop-php-client/commit/13a3d2dddf2fd6a9ffc4c79c7905d866c2eda69b) - **CheckoutService**: add unit tests for checkout requests and error handling *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`36fc2a0`](https://github.com/wipop-bbva/wipop-php-client/commit/36fc2a0a1f296c09a770926adbeef877097b7b18) - **CheckoutService**: add unit tests for checkout requests and error handling *(commit by [@victorvierna](https://github.com/victorvierna))*

### :wrench: Chores
- [`9b13824`](https://github.com/wipop-bbva/wipop-php-client/commit/9b13824c7131fe1d6e5ad906e4fca02f0c7a9891) - **dependencies**: use fixed dependencies versions *(commit by [@victorvierna](https://github.com/victorvierna))*
- [`a3b8f1b`](https://github.com/wipop-bbva/wipop-php-client/commit/a3b8f1b9ec337e9eea99251c992dbd671ad4c957) - **dependencies**: downgrade phpro/grumphp to version 2.16.0 *(commit by [@victorvierna](https://github.com/victorvierna))*

[v0.2.0]: https://github.com/wipop-bbva/wipop-php-client/compare/v0.1.0...v0.2.0
