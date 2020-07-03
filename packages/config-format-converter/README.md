# Config Clarity

[![Downloads total](https://img.shields.io/packagist/dt/migrify/config-format-converter.svg?style=flat-square)](https://packagist.org/packages/migrify/config-format-converter/stats)

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
composer require migrify/config-format-converter --dev
```

## Usage

```bash
vendor/bin/config-format-converter clarify /config/sinle_file.neon

vendor/bin/config-format-converter clarify /config

vendor/bin/config-format-converter clarify /config/sinle_file.yaml
```
