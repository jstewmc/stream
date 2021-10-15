# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.0] - 2021-10-15

Version `0.2.0` includes a number of breaking changes, intended to make the library easier to use and maintain.

### Removed

- Removed the `setCharacters()` method. It's better if this low-level implementation detail is not editable.
- Removed the `protected` methods `hasNextCharacter()` and `hasPreviousCharacter()` since they were not being used.

### Changed

- Updated the library for PHP 7.4+ and PHP 8.
- Changed `Stream` to an `abstract class` with `File` and `Text` implementations.
- Changed constructor arguments to accept a `string` or `Chunker` instance. This simplifies instantiation, if you are happy using the chunker's default encoding and chunk size.
- Modernized the classes with property type hints, argument type hints, return type hints, guard clauses, etc.
- Changed `File` tests from using concrete files and folders to using a virtual file system with [bovigo/vfsStream](https://github.com/bovigo/vfsStream).
- Updated from PHPUnit version 4 to version 9.
- Updated the README to better explain the problem this library solves.
- Removed lots and lots of unnecessary comments :).
- Changed `protected` methods to `private`, where possible, and decomposed methods like `read()` into smaller ones.

### Added

- Added `ext-mbstring` requirement to `composer.json`.
- Added `CHANGELOG` to keep track of changes.
- Added [slevomat/coding-standard](https://github.com/slevomat/coding-standard) to enforce coding standards.
- Added [roave/security-advisories](https://github.com/Roave/SecurityAdvisories) to exclude dependencies with known vulnerabilities.
- Added continuous integration with [CircleCI](https://circleci.com/gh/jstewmc/usps-address).
- Added code coverage analysis with [CodeCov](https://codecov.io/gh/jstewmc/usps-address).

## [0.2.1] - 2015-07-06

### Fixed

- Duh. I forgot to include the changes to `composer.json`.

## [0.2.0] - 2015-07-06

### Changed

- Update to requiring a `Jstewmc\Chunker` instance. Before, both the chunking and the splitting were handled in this class. However, it was becoming a headache. So, I moved the chunking to [jstewmc\chunker](https://github.com/jstewmc/chunker), and the Chunker is now a required constructor argument.

## [0.1.0] - 2015-07-03

Initial release
