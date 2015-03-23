# Stream

Stream a very large text file or string.

For example:

```php
use Jstewmc\Stream;

// create a trivial text file
file_put_contents('path\to\file.txt', 'foo');

// create a new stream
$stream = new File('path\to\file.txt');

// while a current character exists
while ($stream->getCurrentCharacter()) {
	// echo the current character to the screen
	echo $stream->getCurrentCharacter()."\n";	
	// advance to the next character
	$stream->getNextCharacter();
}
```

The example above would produce the following output:

```
f
o
o
```

Of course, this example is trivial. However, storing the entire contents of very large files or very large strings is memory intensive. With the `Stream` class, you can loop through a file or string character-by-character with a much smaller memory footprint.

## Methodology

Basically, this library divides very large text files and very large strings into chunks. As you move character-to-character, it get and splits the next or previous chunk in the background as needed.

## Files

You can set the file's name via the constructor or the `setName()` method:

```php
use Jstewmc\Stream;

$a = new File('path\to\file.txt');

$b = new File();
$b->setName('path\to\file.txt');

$a == $b;  // returns true
```

Keep in mind, however you set the file's name, the file must exist and be readable. Otherwise, an `InvalidArgumentException` will be thrown.

## Text

You can set the text via the constructor or the `setText()` method:

```php
use Jstewmc\Stream;

$a = new Text('foo');

$b = new Text();
$b->setText('foo');

$a == $b;  // returns true
```

## Methods

You can get the stream's current, next, and previous characters:

```php
use Jstewmc\Stream;

file_put_contents('path\to\file.txt', 'foo');

$stream = new File('path\to\file.txt');

$stream->getCurrentCharacter();   // returns "f"
$stream->getNextCharacter();      // returns "o"
$stream->getPreviousCharacter();  // returns "f"
```

The `getNextCharacter()` and `getPreviousCharacter()` methods behave like PHP's native `next()` and `prev()` methods. When called, they'll increment or decrement the internal pointer and return the corresponding character.

For convenience, the `getCurrentCharacter()`, `getNextCharacter()`, and `getPreviousCharacter()` methods are aliased as `current()`, `next()`, and `previous()`, respectively.

If needed, you can reset the stream's internal pointer:

```php
use Jstewmc\Stream;

$stream = new Text('foo');

$stream->getCurrentCharacter();  // returns "f"
$stream->getNextCharacter();     // returns "o"

$stream->reset();

$stream->getCurrentCharacter();  // returns "f"
```

## About

In February 2015, I wrote a library to [read and write RTF files](https://github.com/jstewmc/rtf). Unfortunately, I soon realized that some RTF files can be very large, too large to get and split as one string.

## Contributing

See [CONTRIBUTING.md](https://github.com/jstewmc/stream/blob/master/CONTRIBUTING.md) for details.

## Author

Jack Clayton - [clayjs0@gmail.com](mailto:clayjs0@gmail.com)

## License

This library is released under the [MIT license](https://github.com/jstewmc/stream/blob/master/LICENSE).

## Version

0.1.0 - See [CHANGELOG.md](https://github.com/jstewmc/stream/blob/master/CHANGELOG.md) for details.
