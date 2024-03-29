# Changelog

All notable changes to `klarnaordermanagement` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [0.6.5] - 2024-03-13
### Added
- Added all line items to automatic capture request of full amount

## [0.6.4] - 2023-06-19
### Fixed
- Changed constraint to match anything below PHP 8.3

## [0.6.3] - 2023-06-19
### Fixed
- Fixed building of product URLs for line items with missing link details because of third-party plugins
- Added support for PHP 8.2

## [0.6.2] - 2023-01-13
### Fixed
- Fixed handling of nullable column 
- Fixed code style warnings

## [0.6.1] - 2022-09-05
### Fixed
- Save multiple tracking codes with first capture
- Added currency fallback if purchase_currency in backend is not valid
- Fixed handling of non JSON responses from API

## [0.6.0] - 2022-06-14
### Fixed
- The purchase currency of the order is now used for displaying the amounts
- Format of amounts in the details of a capture and refund
### Changed
- Changed client for requests from guzzle to curl
- Adjusted error handling
- Codestyle
### Added
- Validation of inputs for capture and refund

## [0.5.1] - 2022-04-12
### Fixed
- Fixed a missing auth header during customer token generation
- Fixed the config reader to return the correct value when falsy values are configured (e.g. 0, 'false')
- Fixed compatibility to PHP 8.0
### Added
- Allowed manual and individual adding of positions which are not related to an article

## [0.5.0] - 2022-02-28 
### Fixed
- Fixed the update of order positions 
- Fixed the handling of multiple opened orders in the backend at the same time
### Added
- Added possibility to save multiple tracking codes

## [0.4.0] - 2021-11-04
### Added
- Moved services from Klarna Payments to order management
- Added service classes for recurring orders and customer token
### Fixed
- Restored compatibility for subshops

## [0.3.3] - 2021-10-10
### Fixed 
- Snippet caused error 500 in Shopware backend
- Changed source of plugin version in logger
- Version constraint in composer.json does not allow PHP Version smaller than 7.2 anymore
- Fixed ignoring log level setting

## [0.3.2] - 2021-08-04
### Added
- Added method to ProductIdentifiers to check for existing values
### Fixed
- Fixed an error when saving a trackingnumber
- Updated Order::cancel signature

## [0.3.1] - 2021-06-25
### Fixed
- Fixed non-editable orders
- Changed int to float cast for ModeConverter
### Added
- Added php8 compatibility
- Added missing GuzzleHttp interface for compatibility with Shopware 5.7
- Compatibility established to Shopware 5.7

## [0.3.00] - 2021-02-11
### Fixed
- Compatibility for Shopware 5.7
- Updated Symfony/Property-Info to v5.1.0
- Updated phpdocumentor/reflection-docblock to v5.1.0    

## [0.2.29] - 2020-12-03
### Fixed
- Partial refunds not working correctly from backend 

## [0.2.28] - 2020-09-22
### Changed
- Fixed a bug that could lead to wrong captures/refunds.

## [0.2.27] - 2019-10-25
### Changed
- Extended the response resource to have the status code of the response.

## [0.2.26] - 2019-10-25
### Changed
- Allow changing payment method when no klarna transcation id exists
- Allow deleting order when no klarna transcation id exists
### Fixed
- Fixed a small CSS Bug

## [0.2.25] - 2019-10-25
### Changed
- Extended the ShopwareModules Factory to make sArticles available

## [0.2.24] - 2019-10-25
### Changed
- Added Support for PayPalUnified

## [0.2.23] - 2019-10-25
### Changed
- Changed Translate text snippets that was not translated.

## [0.2.22] - 2019-10-25
### Changed
- Changed Support more than 1 shipping line items for proportional taxes

## [0.2.21] - 2019-09-09
### Changed
- Changed Replaced SHOPWARE::Version with ShopwareVersionHelper Service to find the right Verion
### Fixed
- Test / Live configuration of sub shops not used  

## [0.2.20] - 2019-08-22
### Removed
- Changed the log file name so KP and KCO have their own log files 
- Changed the logger format to support shopware standard.

## [0.2.19] - 2019-08-22
### Removed
- Removed the downloadLogs action so it can't be used/called anymore. 
- Removed the auhtorization Header from the logs.

## [0.2.18] - 2019-05-27
### Added
- Prevent capture/refund if transaction id is empty 
- Tax helper to determine if user is allowed for tax free order

## [0.2.17] - 2019-01-16
### Added
- Changed string to string|null on $title to prevent NULL errors 

## [0.2.16] - 2018-11-12
### Fixed
- Also use language shop ids for sub shop settings 

## [0.2.15] - 2018-11-26
### Added
- Added: Constants for Custom Products Plugin

## [0.2.14] - 2018-11-26
### Added
- Added: |null for reference in LineItem in order to be compatible with custom products plugin

## [0.2.13] - 2018-11-26
### Added
- Fix: Missing use

## [0.2.12] - 2018-11-26
### Added
- Added 'changed' request parameter to make backend non klarna orders editable again

## [0.2.11] - 2018-11-12
### Added
- Added AuthorizationHelper class to set the right auth header, when using orders from sub shop

## [0.2.10] - 2018-11-12
### Added
- Added Status of the position: "Completed" for support of Pickware
- Added Status of the position: "Cancelled" for support of Pickware
- Added Implementation of a configuration for the order line status to react on

## [0.2.9] - 2018-11-05
### Added
- Changed string to string|null on $shippingCompany to prevent NULL errors 

## [0.2.8] - 2018-10-19
### Added
- Added a Shop Aware HTTP Client Service, required in KP and KCO for sub shop settings 

## [0.2.7] - 2018-08-28
### Fixed
- Round the capture and refund amount in order to be able to compare it correctly.  

## [0.2.6] - 2018-08-23
### Fixed
- Shows the suitable text for confirmation capture and refund amount.  

## [0.2.5] - 2018-08-08
### Fixed
- Changing the payment method to Klarna was possible under some circumstances

## [0.2.4] - 2018-08-08
### Added
- Make sure that changing the payment method to Klarna is not possible in the backend

## [0.2.3] - 2018-08-06
### Added
- New fields, which are required for B2B support, to Klarna models (ShippingAddress/BillingAddress/Customer)

## [0.2.2] - 2018-07-31
### Added
- Add more functionality to the PaymentInsights

### Fixed
- Refund throwing an exception when the description field is empty

## [0.2.1] - 2018-07-27
### Changed
- Make sure empty strings are always transformed to `null` when deserializing data

### Fixed
- Maximux refundable amount being the order amount instead of the captured amount (backend view)
- Remaining refundable amount not being calculated correctly (backend view)

## [0.2.0] - 2018-07-05
### Changed
- Renamed `buildForLineItems` in the `BreadcrumbBuilder` to `addBreadcrumb`
- Renamed `buildProductUrls` in the `ProductUrlBuilder` to `addProductUrls`

### Fixed
- Fix not sending the product identifiers when modifying an order in the backend

## [0.1.5] - 2018-06-27
### Added
- Package installer
- All snippets related to the OM
- Config backend controller

## [0.1.4] - 2018-06-18
### Added
- Update merchent references endpoint
- Acknowledge order endpoint

## [0.1.3] - 2018-06-18
### Added
- SignautreGenerator

## [0.1.2] - 2018-06-13
### Added
- Breadcrumb builder
- Product url builder

## [0.1.1] - 2018-05-18
### Changed
- Improve DataProvider/DataWriter

## 0.1.0 - 2018-05-14

- Initial release
