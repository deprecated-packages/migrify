# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

<!-- changelog-linker -->

<!-- dumped content start -->
## v0.3.27 - 2020-08-18

### Changed

#### PhpConfigPrinter

- [#136] Init new package

## [v0.3.25] - 2020-08-12

### Added

#### EasyCI

- [#135] Add GenerateSonarProjectCommand

## [v0.3.24] - 2020-08-05

#### ConfigTransformer

- [#133] Add support for all args for route import in YAML, Thanks to [@natepage]

## [v0.3.21] - 2020-08-01

- [#130] Add routing support

### Changed

- [#131] Detect routing based on keys

## [v0.3.20] - 2020-07-30

### Fixed

- [#128] Fix keeping template path instead of ref

### Removed

- [#129] Drop comments magic preserving, way too buggy

## [v0.3.19] - 2020-07-29

### Added

#### Unknown Package

- [#124] added notes about contribution/issue reporting, Thanks to [@clxmstaab]

### Changed

#### ZephirToPHP

- [#107] Init

### Fixed

#### ConfigTransformer

- [#125] fix comments in list after item

## [v0.3.18] - 2020-07-26

### Added

#### CI

- [#115] Add split tests + add few fixes of ConfigTransformer

#### ConfigTransformer

- [#120] Add extension support
- [#116] Add single case converter architecture

### Changed

- [#117] Move _instanceof to case converter"

## [v0.3.17] - 2020-07-23

### Added

- [#112] Fix missing php-parser + add declare(strict_types=1)

### Changed

#### LatteToTwig

- [#108] Simplify fixtures

#### NeonToYaml

- [#109] Simplify fixtures

## [v0.3.16] - 2020-07-18

#### ConfigTransformer

- [#106] Various improvements from Rector set upgrades

### Fixed

- [#104] Fix escaped slashes

## [v0.3.15] - 2020-07-18

### Changed

- [#103] Clean breaking comment lines

## [v0.3.13] - 2020-07-18

### Fixed

- [#102] Fix FQN names of functions

<!-- dumped content end -->

<!-- dumped content start -->
## [v0.3.12] - 2020-07-18

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
[#136]: https://github.com/migrify/migrify/pull/136
[#135]: https://github.com/migrify/migrify/pull/135
[#133]: https://github.com/migrify/migrify/pull/133
[#131]: https://github.com/migrify/migrify/pull/131
[#130]: https://github.com/migrify/migrify/pull/130
[#129]: https://github.com/migrify/migrify/pull/129
[#128]: https://github.com/migrify/migrify/pull/128
[#125]: https://github.com/migrify/migrify/pull/125
[#124]: https://github.com/migrify/migrify/pull/124
[#120]: https://github.com/migrify/migrify/pull/120
[#117]: https://github.com/migrify/migrify/pull/117
[#116]: https://github.com/migrify/migrify/pull/116
[#115]: https://github.com/migrify/migrify/pull/115
[#112]: https://github.com/migrify/migrify/pull/112
[#109]: https://github.com/migrify/migrify/pull/109
[#108]: https://github.com/migrify/migrify/pull/108
[#107]: https://github.com/migrify/migrify/pull/107
[#106]: https://github.com/migrify/migrify/pull/106
[#104]: https://github.com/migrify/migrify/pull/104
[#103]: https://github.com/migrify/migrify/pull/103
[#102]: https://github.com/migrify/migrify/pull/102
[v0.3.25]: https://github.com/migrify/migrify/compare/v0.3.24...v0.3.25
[v0.3.24]: https://github.com/migrify/migrify/compare/v0.3.21...v0.3.24
[v0.3.21]: https://github.com/migrify/migrify/compare/v0.3.20...v0.3.21
[v0.3.20]: https://github.com/migrify/migrify/compare/v0.3.19...v0.3.20
[v0.3.19]: https://github.com/migrify/migrify/compare/v0.3.18...v0.3.19
[v0.3.18]: https://github.com/migrify/migrify/compare/v0.3.17...v0.3.18
[v0.3.17]: https://github.com/migrify/migrify/compare/v0.3.16...v0.3.17
[v0.3.16]: https://github.com/migrify/migrify/compare/v0.3.15...v0.3.16
[v0.3.15]: https://github.com/migrify/migrify/compare/v0.3.13...v0.3.15
[v0.3.13]: https://github.com/migrify/migrify/compare/v0.3.12...v0.3.13
[v0.3.12]: https://github.com/migrify/migrify/compare/v0.3.11...v0.3.12
[@natepage]: https://github.com/natepage
[@clxmstaab]: https://github.com/clxmstaab
