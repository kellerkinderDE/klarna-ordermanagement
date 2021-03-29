# Klarna OrderManagement

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This is a package for [Shopware 5](https://en.shopware.com/) plugins.
It adds support for the [Klarna OrderManagement API](https://developers.klarna.com/api/#order-management-api) and also includes a UI in the shopware backend.
If using this package, you will need to implement Klarna Payments order Klarna Checkout yourself.

* [Klarna Payments Docs][link-kp-docs]
* [Klarna Checkout Docs][link-kco-docs]
* [Order Management Docs][link-om-docs]

## Install

Via Composer

``` bash
$ composer require k10r/klarna-ordermanagement
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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/k10r/klarna-ordermanagement.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/k10r/klarna-ordermanagement.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/k10r/klarna-ordermanagement
[link-downloads]: https://packagist.org/packages/k10r/klarna-ordermanagement
[link-om-docs]: https://klarna.kellerkinder.de/sw5/de/3-ko/index.html
[link-kp-docs]: https://klarna.kellerkinder.de/sw5/de/1-kp/index.html
[link-kco-docs]: https://klarna.kellerkinder.de/sw5/de/2-kc/index.html
