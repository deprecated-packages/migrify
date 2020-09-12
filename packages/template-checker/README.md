# Template Checker

[![Downloads total](https://img.shields.io/packagist/dt/migrify/template-checker.svg?style=flat-square)](https://packagist.org/packages/migrify/template-checker/stats)

Check your TWIG templates

## Install

```bash
composer require migrify/template-checker --dev
```

## Usage

### Check Latte Templates

- for existing classes
- for existing class constants
- for existing static calls

```bash
vendor/bin/template-checker check-latte-template templates 
```

### Check Twig Controller Paths

```php
final class SomeController
{
    public function index()
    {
        return $this->render('does_path_exist.twig');
    }
}
```

```bash
vendor/bin/template-checker check-twig-render src/Controller 
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
