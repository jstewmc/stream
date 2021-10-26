[![CircleCI](https://circleci.com/gh/jstewmc/stream.svg?style=svg)](https://circleci.com/gh/jstewmc/stream) [![codecov](https://codecov.io/gh/jstewmc/stream/branch/master/graph/badge.svg?token=GxhdQr71JU)](https://codecov.io/gh/jstewmc/stream)

# Stream

_A multi-byte-safe stream for reading very large files (or strings) character-by-character._

Reading very large files or strings into PHP's memory and exploding them for character-by-character processing - like parsing or lexing - can quickly overrun your process memory.

This library streams the characters of very large text files in an easy, multi-byte, memory-safe manner:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

while (false !== $characters->current()) {
	echo "{$characters->current()}\n";
	$characters->next();
}
```

The (trivial) example above would produce the following output:

```
f
o
o
```

In the background, this library uses [jstewmc/chunker](https://github.com/jstewmc/chunker) to chunk very large text files or strings in a multi-byte, low-memory manner and moves between chunks as necessary.

## Installation

This library requires [PHP 7.4+](https://secure.php.net).

It is multi-platform, and we strive to make it run equally well on Windows, Linux, and OSX.

It should be installed via [Composer](https://getcomposer.org). To do so, add the following line to the `require` section of your `composer.json` file, and run `composer update`:

```javascript
{
   "require": {
       "jstewmc/stream": "^0.4"
   }
}
```

## Usage

### Instantiating a stream

A stream can be instantiated as `Text` or `File`:

```php
use Jstewmc\Stream\{File, Text};

$textCharacters = new Text('foo');

$fileCharacters = new File('/path/to/file.txt');
```

By default, a stream uses the environment's character encoding and a chunk size of around 8kb. If you need more control, you can instantiate a stream using a `Chunker` instance, instead of a string:

```php
use Jstewmc\{Chunker, Stream};

$textChunks = new Chunker\Text('foo', 'UTF-8', 16384 /* characters */);
$textCharacters = new Stream\Text($textChunks);

$fileChunks = new Chunker\File('/path/to/file.txt', 'UTF-8', 65536 /* bytes */);
$fileCharacters = new Stream\File($fileChunks);
```

### Navigating a stream

Once a stream has been instantiated, you can get the stream's current, next, and previous characters using the `getCurrentCharacter()`, `getNextCharacter()`, and `getPreviousCharacter()` methods, respectively (these methods are aliased as `current()`, `next()`, and `previous()`, respectively, and they will return `false` if the character does not exist):

```php
use Jstewmc\Stream\Text;

$characters = new Text('bar');

$characters->current();   // returns "b"

$characters->next();      // returns "a"
$characters->next();      // returns "r"
$characters->next();      // returns false

$characters->current();   // returns false

$characters->previous();  // returns "r"
$characters->previous();  // returns "a"
$characters->previous();  // returns "b"
$characters->previous();  // returns false

$characters->current();   // returns false
```

These methods will typically be combined in a `while` loop like so:

```php
use Jstewmc\Stream\Text;

$characters = new Text('bar');

while (false !== $characters->current()) {
	echo "{$characters->current()}\n";
	$characters->next();
}
```

Keep in mind, these methods are _idempotent_ and _repeatable_. For example, you can call `next()` multiple times at the end of the stream without proceeding past the end of the stream, and you can call `previous()` from the end of the stream to navigate in the opposite direction.

### Peaking ahead

You can use the `peek()` method to look ahead to the next _n_ characters without updating the internal index:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->current();  // returns "f"
$characters->peek();     // returns "o"
$characters->peek(2);    // returns "oo"
$characters->peek(3);    // returns "oo"
$characters->current();  // returns "f"
```

### Testing the content

You can use the `isOn()` method to test whether or not the stream is on a string or includes one of an array of strings:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->isOn('f');  // returns true (because the current character is "f")
$characters->isOn('b');  // returns false

$characters->isOn('foo');  // returns true
$characters->isOn('bar');  // returns false

$characters->isOn(['f', 'a', 'b']);  // returns true (because "f" matches)
$characters->isOn(['b', 'a', 'r']);  // returns false

$characters->isOn(['foo', 'bar', 'baz']);  // returns true (because "foo" matches)
$characters->isOn(['bar', 'baz', 'qux']);  // returns false
```

You can use the `isOnRegex()` method to test whether or not a number of characters match the given regular expression (rather than attempt to detect the number of characters in the regular expression, which would be very difficult, the number of characters to search is the second argument):

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->isOnRegex('/f/');  // returns true
$characters->isOnRegex('/b/');  // returns false

$characters->isOnRegex('/foo/', 3);  // returns true
$characters->isOnRegex('/bar/', 3);  // returns false
```

### Resetting the stream

If you need to, you can reset the stream's internal pointer:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->next();     // returns "o"

$characters->reset();

$characters->current();  // returns "f"
```

## License

This library is released under the [MIT license](LICENSE).

## Contributing

Contributions are welcome!

Here are the steps to get started:

```bash
# Clone the repository (assuming you have Git installed).
~/path/to $ git clone git@github.com:jstewmc/stream.git

# Install dependencies (assuming you are using Composer locally).
~/path/to/stream $ php composer.phar install

# Run the tests.
~/path/to/stream $ ./vendor/bin/phpunit

# Create and checkout a new branch.
~/path/to/stream $ git checkout -b YOUR_BRANCH_NAME

# Make your changes (be sure to add tests with 95%+ coverage and describe your
# changes in the CHANGELOG's "Unreleased" section).

# Run the tests.
~/path/to/stream $ ./vendor/bin/phpunit

# Lint your changes.
~/path/to/stream $ ./vendor/bin/phpcs .

# Automatically fix any issues that arise.
~/path/to/stream $ ./vendor/bin/phpcbf .

# Push your changes to Github and create a pull request.
~/path/to/stream $ git push origin YOUR_BRANCH_NAME
```
