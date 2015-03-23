# Stream

Stream a text file character-by-character.

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
	echo "\"$stream->getCurrentCharacter()\""."\n";	
	// advance to the next character
	$stream->getNextCharacter();
}
```

The example above would produce the following output:

```
"f"
"o"
"o"
```

Of course, this example is trivial. However, for very large files (on the order of several megabytes), storing the entire file's contents in memory as a string or as an array can be memory intensive. With the `Stream` class, you can loop through the file character-by-character.

## Methodology

Basically, this library divides very large text files into chunks. As you move character-to-character, it get and splits the next or previous chunk in the background as needed.

## Methods

You can set the file's name via the constructor or the `setName()` method:

```php
use Jstewmc\Stream;

$a = new File('path\to\file.txt');

$b = new File();
$b->setName('path\to\file.txt');

$a == $b;  // returns true
```

Keep in mind, however you set the file's name, the file must exist and be readable. Otherwise, an `InvalidArgumentException` will be thrown.

You can get the file's current, next, and previous characters:

```php
use Jstewmc\Stream;

file_put_contents('path\to\file.txt', 'foo');

$stream = new File('path\to\file.txt');

$stream->getCurrentCharacter();   // returns "f"
$stream->getNextCharacter();      // returns "o"
$stream->getPreviousCharacter();  // returns "f"
```

The `getNextCharacter()` and `getPreviousCharacter()` methods behave like PHP's native `next()` and `prev()` methods. When called, they'll increment or decrement the internal pointer and return the corresponding character.

You can reset the stream's internal pointer:

```php
use Jstewmc\Stream;

file_put_contents('path\to\file.txt', 'foo');

$stream = new File('path\to\file.txt');

$stream->getCurrentCharacter();  // returns "f"
$stream->getNextCharacter();     // returns "o"

$stream->reset();

$stream->getCurrentCharacter();  // returns "f"
```

The `getCurrentCharacter()`, `getNextCharacter()`, and `getPreviousCharacter()` methods are aliased for convenience as `current()`, `next()`, and `previous()`, respectively.

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
