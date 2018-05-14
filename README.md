# Klarna OrderManagement

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [shopware](https://en.shopware.com/) plugins.
It adds support for the [Klarna OrderManagement API](https://developers.klarna.com/api/#order-management-api) and also includes a UI in the shopware backend.
If using this package, you will need to implement Klarna Payments order Klarna Checkout yourself.

// TODO: add link to Klarna Payments / Klarna Checkout once they're ready
// TODO: add link to documentation once it's ready

## Install

Via Composer

``` bash
$ composer require bestit/klarna-ordermanagement
```

### Registering dependencies

In your Shopware plugin entry file, you need to specify the following container parameters:

* Plugin name
* Plugin version

```php
public function build(ContainerBuilder $containerBuilder)
{
    parent::build($containerBuilder);

    $containerBuilder->setParameter('bestit_klarna.plugin_name', 'ExamplePluginName');
    $containerBuilder->setParameter('bestit_klarna.plugin_version', '1.0.0');
}
```

And you will also need to register our dependencies:

```php
public function build(ContainerBuilder $containerBuilder)
{
    parent::build($containerBuilder);

    //
    
    $dependencyInjectionExtensions = [
        \BestitKlarnaOrderManagement\Components\DependencyInjection\DependencyInjectionExtension::class
    ];

    foreach ($dependencyInjectionExtensions as $dependencyInjectionExtension) {
        if (!class_exists($dependencyInjectionExtension)) {
            continue;
        }

        $dependencyInjectionExtension = new $dependencyInjectionExtension();

        if (!$dependencyInjectionExtension instanceof \BestitKlarnaOrderManagement\Components\DependencyInjection\DependencyInjectionExtensionInterface) {
            continue;
        }

        $dependencyInjectionExtension->injectDependencies($containerBuilder);
    }
}
```

## Usage

// Link to documentation will be added here once it is ready

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email [best it](mailto:support@bestit-online.de).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/bestit/klarna-ordermanagement.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/bestit/klarna-ordermanagement.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/bestit/klarna-ordermanagement
[link-downloads]: https://packagist.org/packages/bestit/klarna-ordermanagement
