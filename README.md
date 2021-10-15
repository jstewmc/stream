[![CircleCI](https://circleci.com/gh/jstewmc/stream.svg?style=svg)](https://circleci.com/gh/jstewmc/stream) [![codecov](https://codecov.io/gh/jstewmc/stream/branch/master/graph/badge.svg?token=GxhdQr71JU)](https://codecov.io/gh/jstewmc/stream)

# Stream

_A multi-byte-safe stream for reading very large files (or strings) character-by-character._

Reading very large files or strings into PHP's memory and exploding them for character-by-character processing - like parsing or lexing - can quickly overrun your process memory.

This library streams the characters of very large text files in an easy, multi-byte, memory-safe manner:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

while (false !== ($character = $characters->current())) {
	echo "{$character}\n";
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
       "jstewmc/stream": "^0.3"
   }
}
```

## Usage

### Instantiating a stream

A stream can be instantiated as `Text` or `File`:

```php
use Jstewmc\Stream\{File, Text};

$characters1 = new Text('foo');

$characters2 = new File('/path/to/file.txt');
```

By default, a stream uses the environment's character encoding and a chunk size of around 8kb. If you need more control, you can instantiate a stream using a `Chunker` instance, instead of a string:

```php
use Jstewmc\{Chunker, Stream};

$textChunks = new Chunker\Text('foo', 'UTF-8', 16384);
$textCharacters = new Stream\Text($textChunks);

$fileChunks = new Chunker\File('/path/to/file.txt', 'UTF-8', 65536);
$fileCharacters = new Stream\File($fileChunks);
```

### Navigating a stream

Once a stream has been instantiated, you can get the stream's current, next, and previous characters using the `getCurrentCharacter()`, `getNextCharacter()`, and `getPreviousCharacter()` methods, respectively. For convenience, the methods are aliased as `current()`, `next()`, and `previous()`:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->current();   // returns "f"

$characters->next();      // returns "o"
$characters->next();      // returns "o"
$characters->next();      // returns false (because next does not exist)

$characters->current();   // returns false

$characters->previous();  // returns "o"
$characters->previous();  // returns "o"
$characters->previous();  // returns "f"
$characters->previous();  // returns false (because previous does not exist)

$characters->current();   // returns "f"
```

Typically, these methods will be combined in a `while` loop like so:

```php
// while characters exist
while (false !== ($character = $characters->current())) {
	// do something with the current character
	echo "{$character}\n";
	// advance to the next character for the next iteration
	$characters->next();
}
```

If you need to, you can reset the stream's internal pointer:

```php
use Jstewmc\Stream\Text;

$characters = new Text('foo');

$characters->current();  // returns "f"
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
~/path/to/stream $ git branch -c YOUR_BRANCH_NAME

# Make your changes...

# Run the tests again.
~/path/to/stream $ ./vendor/bin/phpunit

# Lint your changes.
~/path/to/stream $ ./vendor/bin/phpcs .

# Automatically fix any issues that arise.
~/path/to/stream $ ./vendor/bin/phpcbf .

# Push your changes to Github and create a pull request.
~/path/to/stream $ git push origin YOUR_BRANCH_NAME
```
