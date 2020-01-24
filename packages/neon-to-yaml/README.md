# Neon to Yaml Converter

[![Downloads total](https://img.shields.io/packagist/dt/migrify/neon-to-yaml.svg?style=flat-square)](https://packagist.org/packages/migrify/neon-to-yaml/stats)

Do you want to turn your [Neon](https://ne-on.org/) templates to [Yaml](https://symfony.com/doc/current/components/yaml.html)? There are [many differences](https://www.tomasvotruba.cz/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/) you need to watch out for.

This tool automates it :)

**Before**

```yaml
includes:
    - another-config.neon

parameters:
    perex: '''
        This is long multiline perex,
that takes too much space.
'''

services:
    - App\SomeService(@anotherService, %perex%)
```

**After**

```yaml
imports:
    - { resource: another-config.yaml }

parameters:
    perex: |
        This is long multiline perex,
        that takes too much space.

services:
    App\SomeService:
        arguments:
            - '@anotherService'
            - '%perex%'
```

And much more!

This package won't do it all for you, but **it will help you with 90 % of the boring work**.

## Install

```bash
composer require migrify/neon-to-yaml --dev
```

## Usage

It scan all the `*.(yml|yaml|neon)` files and converts Neon syntax to Yaml and `*.yaml` file.

```bash
vendor/bin/neon-to-yaml convert file.neon
vendor/bin/neon-to-yaml convert /directory
```

That's it :)
