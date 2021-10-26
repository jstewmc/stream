<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker;

class TextTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructThrowsExceptionWhenSourceIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Text([]);
    }

    public function testConstructReturnsStreamWhenSourceIsChunker(): void
    {
        $this->assertInstanceOf(Text::class, new Text(new Chunker\Text('foo')));
    }

    public function testConstructReturnsStreamWhenSourceIsString(): void
    {
        $this->assertInstanceOf(Text::class, new Text('foo'));
    }

    public function testCurrentReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->current());
    }

    public function testCurrentReturnsStringWhenTextIsNotEmpty(): void
    {
        $this->assertEquals('f', (new Text('foo'))->current());
    }

    public function testCurrentReturnsStringWhenCharacterEvaluatesEmpty(): void
    {
        $this->assertEquals('0', (new Text('0'))->current());
    }

    public function testGetCharactersReturnsArrayWhenCharactersDoNotExist(): void
    {
        $this->assertEquals([], (new Text(''))->getCharacters());
    }

    public function testGetCharactersReturnsArrayWhenCharactersExist(): void
    {
        $this->assertEquals(['f', 'o', 'o'], (new Text('foo'))->getCharacters());
    }

    public function testGetCurrentCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->getCurrentCharacter());
    }

    public function testGetCurrentCharacterReturnsStringWhenTextIsNotEmpty(): void
    {
        $this->assertEquals('f', (new Text('foo'))->getCurrentCharacter());
    }

    public function testGetCurrentCharacterReturnsStringWhenCharacterEvaluatesEmpty(): void
    {
        $this->assertEquals('0', (new Text('0'))->getCurrentCharacter());
    }

    public function testGetNextCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->getNextCharacter());
    }

    public function testGetNextCharacterReturnsFalseWhenNextDoesNotExist(): void
    {
        $this->assertFalse((new Text('a'))->getNextCharacter());
    }

    public function testGetNextCharacterReturnsStringWhenNextChunkExistsInCurrentChunk(): void
    {
        // The chunker's default chunk size fits "foo" in a single chunk.
        $this->assertEquals('o', (new Text('foo'))->getNextCharacter());
    }

    public function testGetNextCharacterReturnsStringWhenNextDoesNotExistInCurrentChunk(): void
    {
        // Instantiate a chunker with a chunk size of one character.
        $chunker = new Chunker\Text('foo', null, 1);

        $this->assertEquals('o', (new Text($chunker))->getNextCharacter());
    }

    public function testGetPreviousCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->getPreviousCharacter());
    }

    public function testGetPreviousCharacterReturnsFalseWhenPreviousDoesNotExist(): void
    {
        $this->assertFalse((new Text('foo'))->getPreviousCharacter());
    }

    public function testGetPreviousCharacterReturnsStringWhenPreviousDoesExistInCurrentChunk(): void
    {
        $stream = new Text('foo');

        $stream->getNextCharacter();

        $this->assertEquals('f', $stream->getPreviousCharacter());
    }

    public function testGetPreviousCharacterReturnsStringWhenPreviousDoesNotExistInCurrentChunk(): void
    {
        $chunker = new Chunker\Text('foo', null, 1);

        $stream = new Text($chunker);

        $stream->getNextCharacter();

        $this->assertEquals('f', $stream->getPreviousCharacter());
    }

    public function testNextReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->next());
    }

    public function testNextReturnsFalseWhenNextDoesNotExist(): void
    {
        $this->assertFalse((new Text('a'))->next());
    }

    public function testNextReturnsStringWhenNextChunkExistsInCurrentChunk(): void
    {
        // The chunker's default chunk size fits "foo" in a single chunk.
        $this->assertEquals('o', (new Text('foo'))->next());
    }

    public function testNextReturnsStringWhenNextDoesNotExistInCurrentChunk(): void
    {
        // Instantiate a chunker with a chunk size of one character.
        $chunker = new Chunker\Text('foo', null, 1);

        $this->assertEquals('o', (new Text($chunker))->next());
    }

    public function testPreviousReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->previous());
    }

    public function testPreviousReturnsFalseWhenPreviousDoesNotExist(): void
    {
        $this->assertFalse((new Text('foo'))->previous());
    }

    public function testPreviousReturnsStringWhenPreviousDoesExistInCurrentChunk(): void
    {
        $stream = new Text('foo');

        $stream->getNextCharacter();

        $this->assertEquals('f', $stream->previous());
    }

    public function testPreviousReturnsStringWhenPreviousDoesNotExistInCurrentChunk(): void
    {
        $chunker = new Chunker\Text('foo', null, 1);

        $stream = new Text($chunker);

        $stream->getNextCharacter();

        $this->assertEquals('f', $stream->previous());
    }

    public function testResetResetsInternalPointer(): void
    {
        $stream = new Text('foo');

        $stream->next();
        $stream->next();

        $this->assertEquals('o', $stream->current());

        $stream->reset();

        $this->assertEquals('f', $stream->current());
    }

    public function testPeekThrowsInvalidArgumentExceptionWhenNIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Text('foo'))->peek(-1);
    }

    public function testPeekThrowsInvalidArgumentExceptionWhenNIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Text('foo'))->peek(0);
    }

    public function testPeekReturnsStringWhenTextIsEmpty(): void
    {
        $this->assertEquals('', (new Text(''))->peek());
    }

    public function testPeekReturnsStringWhenOnLastCharacter(): void
    {
        $stream = new Text('bar');

        $stream->next(); // returns "a"
        $stream->next(); // returns "r"

        $this->assertEquals('', $stream->peek());
    }

    public function testPeekReturnsStringWhenAfterLastCharacter(): void
    {
        $stream = new Text('bar');

        $stream->next();  // returns "a"
        $stream->next();  // returns "r"
        $stream->next();  // returns false

        $this->assertEquals('', $stream->peek());
    }

    public function testPeekReturnsStringWhenNIsPositive(): void
    {
        $this->assertEquals('ar', (new Text('bar'))->peek(2));
    }

    public function testPeekDoesNotChangeIndexWhenNIsPositive(): void
    {
        $stream = new Text('bar');

        $this->assertEquals('b', $stream->current());
        $this->assertEquals('ar', $stream->peek(2));
        $this->assertEquals('b', $stream->current());
    }

    public function testPeekReturnsStringWhenNIsLongerThanText(): void
    {
        $this->assertEquals('ar', (new Text('bar'))->peek(999));
    }

    public function testPeekDoesNotChangeIndexWhenNIsLongerThanText(): void
    {
        $stream = new Text('bar');

        $this->assertEquals('b', $stream->current());
        $this->assertEquals('ar', $stream->peek(999));
        $this->assertEquals('b', $stream->current());
    }

    public function testIsOnReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->isOn('a'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotOnCharacter(): void
    {
        $this->assertFalse((new Text('foo'))->isOn('a'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotOnString(): void
    {
        $this->assertFalse((new Text('foo'))->isOn('bar'));
    }

    public function testIsOnReturnsTrueWhenTextIsOnCharacter(): void
    {
        $this->assertTrue((new Text('foo'))->isOn('f'));
    }

    public function testIsOnReturnsTrueWhenTextIsOnString(): void
    {
        $this->assertTrue((new Text('foo'))->isOn('foo'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotInArrayOfCharacters(): void
    {
        $this->assertFalse((new Text('foo'))->isOn(['b', 'a', 'r']));
    }

    public function testIsOnReturnsFalseWhenTextIsNotInArrayOfStrings(): void
    {
        $this->assertFalse((new Text('foo'))->isOn(['bar', 'baz', 'qux']));
    }

    public function testIsOnReturnsTrueWhenTextIsInArrayOfCharacters(): void
    {
        $this->assertTrue((new Text('foo'))->isOn(['a', 'b', 'c', 'd', 'e', 'f']));
    }

    public function testIsOnReturnsTrueWhenTextIsInArrayOfStrings(): void
    {
        $this->assertTrue((new Text('foo'))->isOn(['foo', 'bar', 'baz']));
    }

    public function testIsOnRegexThrowsExceptionWhenLengthIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Text(''))->isOnRegex('/[a-z]/', -1);
    }

    public function testIsOnRegexThrowsExceptionWhenLengthIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Text(''))->isOnRegex('/[a-z]/', 0);
    }

    public function testIsOnRegexReturnsFalseWhenTextIsBlank(): void
    {
        $this->assertFalse((new Text(''))->isOnRegex('/[a-z]/'));
    }

    public function testIsOnRegexReturnsFalseWhenTextIsNotOnPattern(): void
    {
        $this->assertFalse((new Text('foo'))->isOnRegex('/bar/'));
    }

    public function testIsOnRegexReturnsTrueWhenTextIsOnPattern(): void
    {
        $this->assertTrue((new Text('foo'))->isOnRegex('/foo/', 3));
    }
}
