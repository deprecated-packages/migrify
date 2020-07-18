# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

<!-- changelog-linker -->

<!-- dumped content start -->
## Unreleased

### Added

#### ConfigTransformer

- [#101] Add tags to resource
- [#100] Add Symfony 5.1 service() method support

#### Unknown Package

- [#89] vendor-patches add additional autoload include path, Thanks to [@ilmiont]
- [#90] composer.json add sebastian/diff, Thanks to [@ilmiont]

### Changed

#### ConfigTransformer

- [#80] Keep unexpected int keys
- [#81] Keep YAML comments
- [#83] Move namespace to use imports
- [#78] Make named args per line with arg()
- [#84] Change imported file suffix from input to output format
- [#99] Skip basic non-class params

#### Unknown Package

- [#98] move tests to particular namespace

### Fixed

- [#92] Fix typo in docs, Thanks to [@TavoNiievez]

### Removed

#### ConfigTransformer

- [#97] make public(false) removed or to private(), depending on Symfony version

## [v0.3.11] - 2020-07-16

### Added

- [#73] Add support for implicit yaml arguments
- [#72] Add YML support

### Changed

- [#74] Prefix with __DIR__, if possible parameter path

## [v0.3.10] - 2020-07-16

### Added

- [#71] Add import ignore_error on correct arguments position, remove default values

## [v0.3.9] - 2020-07-16

- [#69] Add missing calls under resource, add old constant support
- [#67] Add import or PHP/glob support

### Changed

#### TemplateChecker

- [#70] init

## [v0.3.7] - 2020-07-15

### Added

#### ConfigTransformer

- [#65] add properties support

## [v0.3.6] - 2020-07-14

- [#63] Add --bc-layer option to keep old configs paths with references to new files

### Changed

- [#64] Switch YAML to PHP configs

## [v0.3.5] - 2020-07-14

### Added

- [#62] Add constant support

### Fixed

#### Unknown Package

- [#60] Fix invalid composer.json. Bin must be string, Thanks to [@berezuev]

## [v0.3.4] - 2020-07-14

### Changed

#### ConfigTransformer

- [#59] Use back-compatible ref

## [v0.3.3] - 2020-07-12

### Added

- [#51] Add maker-bundle test fixtures

### Changed

- [#46] exclude is only one array value
- [#58] Refactoring to collector
- [#48] re-use FileBySuffixFinder
- [#50] various node-related improvements
- [#47] service names and tags - Symfony 3.3 vs lower
- [#52] Moving from string to nodes
- [#53] re-use node factories
- [#54] Move empty line management to printer
- [#56] decoupling

#### Unknown Package

- [#55] Update composer.json, Thanks to [@sniper7kills]

#### [vendor-patch]

- [#57] Attempt to clarify "this package needs to be in root to work", Thanks to [@sniper7kills]

## [v0.3.2] - 2020-07-10

### Added

#### ConfigTransformer

- [#45] Add fluent interface printer
- [#44] add php-parser nodes over string
- [#43] add real-life example of yaml/php
- [#41] add YAML to PHP
- [#39] Add Xml To PHP
- [#38] Add --format-form option

### Changed

- [#42] Allow array source argument

#### FeatureShifter

- [#37] Merge symplify/autodiscovery feature to ConfigTransformer

#### Unknown Package

- [#36] move package under FormatSwitcher

### Fixed

- [#35] fixing multiple classes of same type

<!-- dumped content end -->

## [v0.3.2] - 2020-07-10

### Changed

#### ConfigClarity

- [#34] merge clarifier to config-format-converter

#### ConfigFormatConverter

- [#33] Various improvements based on practical use
- [#30] init

## [v0.3.1] - 2020-07-03

#### VendorPatches

- [#29] Prepare for release

## [v0.3.0] - 2020-07-01

### Added

#### ConfigClassPresence

- [#25] Rename to ClassPresence, add class constant check

### Changed

#### ConfigClarity

- [#22] Init new package

#### EasyCI

- [#26] init

## [v0.1.13] - 2020-05-11

#### ConfigClassPresence

- [#18] Init

## [v0.1.12] - 2020-05-10

#### DiffDataMiner

- [#16] init new package

#### FatalErrorScanner

- [#14] Init

#### Unknown Package

- [#8] [PSR-4 Switcher] Init

### Fixed

- [#15] fix fatal error scan path

<!-- dumped content end -->

## [v0.1.8] - 2020-04-17

### Added

#### SymfonyRouteUsage

- [#12] Add ShowDeadRoutesCommand
- [#10] Remove params from route usage, add method

## [v0.1.7] - 2020-04-10

### Changed

- [#9] Initialize table on-the-fly

## [v0.1.2] - 2020-04-09

- [#7] Update just once

## [v0.1.1] - 2020-04-08

### Added

#### SymfonyRouteUsage

- [#6] add SymfonyRouteUsageBundle

### Changed

#### SymfonyRouteUsage

- [#5] Init

## [v0.0.1] - 2020-01-24

### Added

- [#3] Add neon-to-yaml and latte-to-twig

[#12]: https://github.com/migrify/migrify/pull/12
[#10]: https://github.com/migrify/migrify/pull/10
[#9]: https://github.com/migrify/migrify/pull/9
[#7]: https://github.com/migrify/migrify/pull/7
[#6]: https://github.com/migrify/migrify/pull/6
[#5]: https://github.com/migrify/migrify/pull/5
[#3]: https://github.com/migrify/migrify/pull/3
[v0.1.8]: https://github.com/migrify/migrify/compare/v0.1.7...v0.1.8
[v0.1.7]: https://github.com/migrify/migrify/compare/v0.1.2...v0.1.7
[v0.1.2]: https://github.com/migrify/migrify/compare/v0.1.1...v0.1.2
[v0.1.1]: https://github.com/migrify/migrify/compare/v0.0.1...v0.1.1
[#34]: https://github.com/migrify/migrify/pull/34
[#33]: https://github.com/migrify/migrify/pull/33
[#30]: https://github.com/migrify/migrify/pull/30
[#29]: https://github.com/migrify/migrify/pull/29
[#26]: https://github.com/migrify/migrify/pull/26
[#25]: https://github.com/migrify/migrify/pull/25
[#22]: https://github.com/migrify/migrify/pull/22
[#18]: https://github.com/migrify/migrify/pull/18
[#16]: https://github.com/migrify/migrify/pull/16
[#15]: https://github.com/migrify/migrify/pull/15
[#14]: https://github.com/migrify/migrify/pull/14
[#8]: https://github.com/migrify/migrify/pull/8
[v0.3.1]: https://github.com/migrify/migrify/compare/v0.3.0...v0.3.1
[v0.3.0]: https://github.com/migrify/migrify/compare/v0.2.0...v0.3.0
[v0.1.13]: https://github.com/migrify/migrify/compare/v0.1.12...v0.1.13
[v0.1.12]: https://github.com/migrify/migrify/compare/v0.1.8...v0.1.12
[#101]: https://github.com/migrify/migrify/pull/101
[#100]: https://github.com/migrify/migrify/pull/100
[#99]: https://github.com/migrify/migrify/pull/99
[#98]: https://github.com/migrify/migrify/pull/98
[#97]: https://github.com/migrify/migrify/pull/97
[#92]: https://github.com/migrify/migrify/pull/92
[#90]: https://github.com/migrify/migrify/pull/90
[#89]: https://github.com/migrify/migrify/pull/89
[#84]: https://github.com/migrify/migrify/pull/84
[#83]: https://github.com/migrify/migrify/pull/83
[#81]: https://github.com/migrify/migrify/pull/81
[#80]: https://github.com/migrify/migrify/pull/80
[#78]: https://github.com/migrify/migrify/pull/78
[#74]: https://github.com/migrify/migrify/pull/74
[#73]: https://github.com/migrify/migrify/pull/73
[#72]: https://github.com/migrify/migrify/pull/72
[#71]: https://github.com/migrify/migrify/pull/71
[#70]: https://github.com/migrify/migrify/pull/70
[#69]: https://github.com/migrify/migrify/pull/69
[#67]: https://github.com/migrify/migrify/pull/67
[#65]: https://github.com/migrify/migrify/pull/65
[#64]: https://github.com/migrify/migrify/pull/64
[#63]: https://github.com/migrify/migrify/pull/63
[#62]: https://github.com/migrify/migrify/pull/62
[#60]: https://github.com/migrify/migrify/pull/60
[#59]: https://github.com/migrify/migrify/pull/59
[#58]: https://github.com/migrify/migrify/pull/58
[#57]: https://github.com/migrify/migrify/pull/57
[#56]: https://github.com/migrify/migrify/pull/56
[#55]: https://github.com/migrify/migrify/pull/55
[#54]: https://github.com/migrify/migrify/pull/54
[#53]: https://github.com/migrify/migrify/pull/53
[#52]: https://github.com/migrify/migrify/pull/52
[#51]: https://github.com/migrify/migrify/pull/51
[#50]: https://github.com/migrify/migrify/pull/50
[#48]: https://github.com/migrify/migrify/pull/48
[#47]: https://github.com/migrify/migrify/pull/47
[#46]: https://github.com/migrify/migrify/pull/46
[#45]: https://github.com/migrify/migrify/pull/45
[#44]: https://github.com/migrify/migrify/pull/44
[#43]: https://github.com/migrify/migrify/pull/43
[#42]: https://github.com/migrify/migrify/pull/42
[#41]: https://github.com/migrify/migrify/pull/41
[#39]: https://github.com/migrify/migrify/pull/39
[#38]: https://github.com/migrify/migrify/pull/38
[#37]: https://github.com/migrify/migrify/pull/37
[#36]: https://github.com/migrify/migrify/pull/36
[#35]: https://github.com/migrify/migrify/pull/35
[vendor-patch]: https://github.com/migrify/migrify/compare/v0.3.2...vendor-patch
[v0.3.9]: https://github.com/migrify/migrify/compare/v0.3.7...v0.3.9
[v0.3.7]: https://github.com/migrify/migrify/compare/v0.3.6...v0.3.7
[v0.3.6]: https://github.com/migrify/migrify/compare/v0.3.5...v0.3.6
[v0.3.5]: https://github.com/migrify/migrify/compare/v0.3.4...v0.3.5
[v0.3.4]: https://github.com/migrify/migrify/compare/v0.3.3...v0.3.4
[v0.3.3]: https://github.com/migrify/migrify/compare/vendor-patch...v0.3.3
[v0.3.2]: https://github.com/migrify/migrify/compare/v0.3.1...v0.3.2
[v0.3.11]: https://github.com/migrify/migrify/compare/v0.3.10...v0.3.11
[v0.3.10]: https://github.com/migrify/migrify/compare/v0.3.9...v0.3.10
[@sniper7kills]: https://github.com/sniper7kills
[@ilmiont]: https://github.com/ilmiont
[@berezuev]: https://github.com/berezuev
[@TavoNiievez]: https://github.com/TavoNiievez
