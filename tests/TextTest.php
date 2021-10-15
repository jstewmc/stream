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

    public function testHasCharactersReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse((new Text(''))->hasCharacters());
    }

    public function testHasCharactersReturnsTrueWhenBeforeLastCharacter(): void
    {
        $this->assertTrue((new Text('foo'))->hasCharacters());
    }

    public function testHasCharactersReturnsTrueWhenOnLastCharacter(): void
    {
        $stream = new Text('foo');

        $stream->next();
        $stream->next();

        $this->assertTrue($stream->hasCharacters());
    }

    public function testHasCharactersReturnsFalseWhenAfterLastCharacter(): void
    {
        $stream = new Text('foo');

        $stream->next();
        $stream->next();
        $stream->next();

        $this->assertFalse($stream->hasCharacters());
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
}
