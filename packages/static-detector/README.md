# Static Detector

[![Downloads total](https://img.shields.io/packagist/dt/migrify/static-detector.svg?style=flat-square)](https://packagist.org/packages/migrify/static-detector/stats)

Detect static and its calls in your project!

## Install

```bash
composer require migrify/static-detector --dev
```

## Usage

```bash
vendor/bin/static-detector detect src
```

## Configuration

Do you want to look only on specific classes? Just create `static-detector.php` config in your root and add filter them:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Migrify\StaticDetector\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::FILTER_CLASSES, [
        '*\\Helpers'
    ]);
};
```

That's it :)

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
