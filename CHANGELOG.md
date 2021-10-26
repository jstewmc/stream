# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.2] - 2021-10-26

### Added

- Added the `peek()` method to look at the next _n_ characters without changing the internal index.
- Added the `isOn()` and `isOnRegex()` methods to test whether or not the current character (and, potentially, next characters) match a given string or an array of strings.

## [0.4.1] - 2021-10-26

### Fixed

- Fixed [#4](https://github.com/jstewmc/stream/issues/4), where the `current()` method never returned false when the stream had advanced after the last character or before the first one.

## [0.4.0] - 2021-10-25

### Added

- Added _idempotency_ to navigation methods. You can call navigation methods like `next()` or `previous()` multiple times at the stream's endpoints without updating the internal pointer.

### Changed

- Reordered the methods in the `Stream` class to be grouped according to usage. Although this mixes `private` and `public` methods, it makes it easier to read without jumping around.

### Removed

- Removed the `hasCharacters()` method. This is not a particularly meaningful method, as it only checks whether or not a current character exists.

### Fixed

- Fixed [#2](https://github.com/jstewmc/stream/issues/2), where attempts to call `previous()` from the end of the string would return `false`.

## [0.3.0] - 2021-10-15

Version `0.3.0` includes a number of breaking changes, intended to make the library easier to use and maintain.

### Removed

- Removed the `setCharacters()` method. It's better if this low-level implementation detail is not editable.
- Removed the `protected` methods `hasNextCharacter()` and `hasPreviousCharacter()` since they were not being used.

### Changed

- Updated the library for PHP 7.4+ and PHP 8.
- Changed `Stream` to an `abstract class` with `File` and `Text` implementations to make the source more intentional.
- Changed constructor arguments to accept a `string` or `Chunker` instance. This simplifies instantiation, if you accept the chunker's default encoding and chunk size.
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
