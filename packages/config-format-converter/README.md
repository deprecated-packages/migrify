# Config Format Converter

[![Downloads total](https://img.shields.io/packagist/dt/migrify/config-format-converter.svg?style=flat-square)](https://packagist.org/packages/migrify/config-format-converter/stats)

Convert 

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
