# Stream

Stream a very large text file or string character-by-character (multi-byte safe).

```php
use Jstewmc\Stream;
use Jstewmc\Chunker;

// create an example file
file_put_contents('example.txt', 'foo');

// create the chunker
$chunker = new Chunker\File('example.txt');

// create the stream
$stream = new Stream($chunker);

// while a current character exists
while (false !== ($character = $stream->current())) {
	// echo the current character to the screen
	echo $character."\n";	
	// advance to the next character
	$stream->next();
}
```

The example above would produce the following output:

```
f
o
o
```

Of course, this example is trivial. But, you get the idea. The combination of the Chunker library and the Stream library allow you to walk or chunk your way through a very large text file or a very large string in an easy, low-memory, multi-byte-friendy way.

## Methodology

Basically, the chunker library divides very large text files and very large strings into chunks (no surprise). As you move character-to-character in the stream, it get and splits the next or previous chunk in the background as needed.

## Chunker

See [Jstewmc\Chunker](https://github.com/jstewmc/chunker) for details on instantiating and initializing a File or Text Chunker, a constructor-injected dependency of this library.

## Stream

Once a stream has been instantiated, you can get the stream's current, next, and previous characters using the `getCurrentCharacter()`, `getNextCharacter()`, and `getPreviousCharacter()` methods, respectively. For convenience, the methods are aliased as `current()`, `next()`, and `previous()`.

```php
use Jstewmc\Chunker;
use Jstewmc\Stream;

file_put_contents('example.txt', 'foo');

$chunker = new Chunker\File('example.txt');

$stream = new Stream($chunker);

$stream->getCurrentCharacter();   // returns "f"
$stream->getNextCharacter();      // returns "o"
$stream->getPreviousCharacter();  // returns "f"
```

The `getNextCharacter()` and `getPreviousCharacter()` methods behave like PHP's native `next()` and `prev()` methods. When called, they'll increment or decrement the internal pointer and return the corresponding character.

If you need to, you can reset the stream's internal pointer:

```php
use Jstewmc\Chunker;
use Jstewmc\Stream;

$chunker = new Chunker\Text('foo');

$stream = new Stream($chunker);

$stream->getCurrentCharacter();  // returns "f"
$stream->getNextCharacter();     // returns "o"

$stream->reset();

$stream->getCurrentCharacter();  // returns "f"
```

## Author

Jack Clayton - [clayjs0@gmail.com](mailto:clayjs0@gmail.com)

## License

This library is released under the [MIT license](https://github.com/jstewmc/stream/blob/master/LICENSE).

## Version

### 0.2.1 - July 6, 2015

- Duh. I forgot to include the changes to `composer.json`.

### 0.2.0 - July 6, 2015
- *Update to using [Jstewmc\Chunker](https://github.com/jstewmc/chunker).* Before, both the chunking and the splitting were handled in this class. However, it was becoming a headache. So, I moved the chunking to my [Jstewmc\Chunker](https://github.com/jstewmc/chunker) library, and the Chunker is now a constructor-injected dependency.

### 0.1.0 - July 3, 2015
Initial release
