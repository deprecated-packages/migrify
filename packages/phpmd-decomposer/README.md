# PHPMD Decomposer

[![Downloads total](https://img.shields.io/packagist/dt/migrify/phpmd-decomposer.svg?style=flat-square)](https://packagist.org/packages/migrify/phpmd-decomposer/stats)

Decompose phpmd.xml to PHPStan, ECS and Rector rules

## Install

```bash
composer require migrify/phpmd-decomposer --dev
```

## Use

```bash
vendor/bin/phpmd-decomposer decompose phpmd.xml
```

What you get are 3 configs:

- `phpmd-decomposed-phpstan.neon`
- `phpmd-decomposed-ecs.php`
- `phpmd-decomposed-rector.php`
