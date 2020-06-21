# Config Clarity

[![Downloads total](https://img.shields.io/packagist/dt/migrify/config-clarity.svg?style=flat-square)](https://packagist.org/packages/migrify/config-clarity/stats)

Take NEON/YAML magic *pro* short syntax and reveal it to clear syntax readable by any developer

From Nette, unpopular ["entities"](https://ne-on.org/) to clear arrays:

```diff
 services:
-    - SomeClass(1, 2)
+    -
+        class: SomeClass
+        arguments:
 +           - 1
+            - 2
```

## Install

```bash
composer require migrify/config-clarity --dev
```

## Usage

```bash
vendor/bin/config-clarity clarify /config/sinle_file.neon

vendor/bin/config-clarity clarify /config

vendor/bin/config-clarity clarify /config/sinle_file.yaml
```
