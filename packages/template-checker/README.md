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

### Extract Static Calls from Latte Templates to FilterProvider

Do you have a static call in your template? It's a hidden filter. Let's decouple it so we can use DI and services as in the rest of project:

```bash
vendor/bin/template-checker extract-latte-static-call-to-filter templates
```

But that's just a dry run... how to apply changes?

```bash 
vendor/bin/template-checker extract-latte-static-call-to-filter templates --fix 
```

What happens? The static call will be replaced by a Latte filter:

```diff
 # any latte file
-{\App\SomeClass::someStaticMethod($value)}
+{$value|someStaticMethod}
```

The filter will be provided 

```php
<?php

final class SomeMethodFilterProvider implements \App\Contract\Latte\FilterProviderInterface
{
    public const FILTER_NAME = 'someMethod';
    
    public function getName() : string
    {
        return self::FILTER_NAME;
    }

    public function __invoke(string $name) : int
    {
        return \App\SomeClass::someStaticMethod($name);
    }
}
```

The file will be generated into `/generated` directory. Just rename namespaces and copy it to your workflow.   

Do you want to know more about **clean Latte filters**? Read [How to Get Rid of Magic, Static and Chaos from Latte Filters](https://tomasvotruba.com/blog/2020/08/17/how-to-get-rid-of-magic-static-and-chaos-from-latte-filters/)  

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
