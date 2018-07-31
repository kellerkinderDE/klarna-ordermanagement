# Changelog

All notable changes to `klarnaordermanagement` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

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
