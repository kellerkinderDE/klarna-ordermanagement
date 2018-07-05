# Klarna OrderManagement

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [shopware](https://en.shopware.com/) plugins.
It adds support for the [Klarna OrderManagement API](https://developers.klarna.com/api/#order-management-api) and also includes a UI in the shopware backend.
If using this package, you will need to implement Klarna Payments order Klarna Checkout yourself.

// TODO: Add Klarna Checkout link once its ready

* [Klarna Payments Docs][link-kp-docs]
* [Order Management Docs][link-om-docs]

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

Then you can use the `OMInstaller` for any ohter necessary setup:

```php
public function install(InstallContext $context)
{
    // ...

    $this->getOmInstaller()->install($this, $context);

    // ...
}

public function uninstall(UninstallContext $context)
{
    // ...

    $this->getOmInstaller()->uninstall($this, $context);

    // ...
}

public function update(UpdateContext $context)
{
    // ...

    $this->getOmInstaller()->update($this, $context);

    // ...
}

protected function getOmInstaller()
{
    if ($this->omInstaller !== null) {
        return $this->omInstaller;
    }

    $this->omInstaller = new OmInstaller($this->container->get('shopware.snippet_database_handler'));

    return $this->omInstaller;
}
```

## Usage

See the [docs][link-om-docs] for more information.

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
[link-om-docs]: https://klarna.bestit-online.de/de/om/master/uebersicht
[link-kp-docs]: https://klarna.bestit-online.de/de/kp/master/installation
