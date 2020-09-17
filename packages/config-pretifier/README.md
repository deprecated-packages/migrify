# Config Pretifier

[![Downloads total](https://img.shields.io/packagist/dt/migrify/config-pretifier.svg?style=flat-square)](https://packagist.org/packages/migrify/config-pretifier/stats)

Convert Symfony config formats and turn magic syntax to more readable one.

## Install

```bash
composer require migrify/config-pretifier --dev
```

<br>

## Make Configs Explicit

Take NEON/YAML magic *pro* short syntax and reveal it to clear syntax readable by any developer.

Do you use [Nette](https://nette.org/)? You can convert unpopular ["entities"](https://ne-on.org/) to clear arrays:

```diff
 services:
-    - SomeClass(1, 2)
+    -
+        class: SomeClass
+        arguments:
+            - 1
+            - 2
```

Just run `pretify` command:

```bash
vendor/bin/config-clarity pretify /config/sinle_file.neon
vendor/bin/config-clarity pretify /config /dev/sinle_file.neon 
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
