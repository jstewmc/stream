<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker;
use org\bovigo\vfs\{vfsStream, vfsStreamDirectory, vfsStreamFile};

class FileTest extends \PHPUnit\Framework\TestCase
{
    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->root = vfsStream::setup('root');
    }

    public function testConstructThrowsExceptionWhenSourceIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new File([]);
    }

    public function testConstructReturnsStreamWhenSourceIsChunker(): void
    {
        $chunker = new Chunker\File($this->presentFile()->url());

        $this->assertInstanceOf(File::class, new File($chunker));
    }

    public function testConstructReturnsStreamWhenSourceIsString(): void
    {
        $this->assertInstanceOf(
            File::class,
            new File($this->presentFile()->url())
        );
    }

    public function testCurrentReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->current());
    }

    public function testCurrentReturnsStringWhenTextIsNotEmpty(): void
    {
        $this->assertEquals(
            $this->presentCharacter1(),
            $this->presentStream()->current()
        );
    }

    public function testCurrentReturnsStringWhenCharacterEvaluatesEmpty(): void
    {
        $this->assertEquals(
            $this->emptyishCharacter1(),
            $this->emptyishStream()->current()
        );
    }

    public function testGetCharactersReturnsArrayWhenCharactersDoNotExist(): void
    {
        $this->assertEquals([], $this->blankStream()->getCharacters());
    }

    public function testGetCharactersReturnsArrayWhenCharactersExist(): void
    {
        $this->assertEquals(
            $this->presentCharacters(),
            $this->presentStream()->getCharacters()
        );
    }

    public function testGetCurrentCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->getCurrentCharacter());
    }

    public function testGetCurrentCharacterReturnsStringWhenTextIsNotEmpty(): void
    {
        $this->assertEquals(
            $this->presentCharacter1(),
            $this->presentStream()->getCurrentCharacter()
        );
    }

    public function testGetCurrentCharacterReturnsStringWhenCharacterEvaluatesEmpty(): void
    {
        $this->assertEquals(
            $this->emptyishCharacter1(),
            $this->emptyishStream()->getCurrentCharacter()
        );
    }

    public function testGetNextCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->getNextCharacter());
    }

    public function testGetNextCharacterReturnsFalseWhenNextDoesNotExist(): void
    {
        $stream = $this->presentStream();

        $stream->next();
        $stream->next();

        $this->assertFalse($stream->getNextCharacter());
    }

    public function testGetNextCharacterReturnsStringWhenNextChunkExistsInCurrentChunk(): void
    {
        $this->assertEquals(
            $this->presentCharacter2(),
            $this->presentStream()->getNextCharacter()
        );
    }

    public function testGetNextCharacterReturnsStringWhenNextDoesNotExistInCurrentChunk(): void
    {
        $stream = $this->customStream();

        $streamAtEndOfChunk1 = $this->advanceCustomStreamToEndOfChunk1($stream);

        $this->assertEquals(
            $this->customCharacterStartOfChunk2(),
            $streamAtEndOfChunk1->getNextCharacter()
        );
    }

    public function testGetPreviousCharacterReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->getPreviousCharacter());
    }

    public function testGetPreviousCharacterReturnsFalseWhenPreviousDoesNotExist(): void
    {
        $this->assertFalse($this->presentStream()->getPreviousCharacter());
    }

    public function testGetPreviousCharacterReturnsStringWhenPreviousDoesExistInCurrentChunk(): void
    {
        $stream = $this->presentStream();

        $stream->next();

        $this->assertEquals(
            $this->presentCharacter1(),
            $stream->getPreviousCharacter()
        );
    }

    public function testGetPreviousCharacterReturnsStringWhenPreviousDoesNotExistInCurrentChunk(): void
    {
        $stream = $this->customStream();

        $streamAtStartOfChunk2 = $this->advanceCustomStreamToStartOfChunk2($stream);

        $this->assertEquals(
            $this->customCharacterEndOfChunk1(),
            $streamAtStartOfChunk2->getPreviousCharacter()
        );
    }

    public function testNextReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->next());
    }

    public function testNextReturnsFalseWhenNextDoesNotExist(): void
    {
        $stream = $this->presentStream();

        $stream->next();
        $stream->next();

        $this->assertFalse($stream->next());
    }

    public function testNextReturnsStringWhenNextChunkExistsInCurrentChunk(): void
    {
        $this->assertEquals(
            $this->presentCharacter2(),
            $this->presentStream()->next()
        );
    }

    public function testNextReturnsStringWhenNextDoesNotExistInCurrentChunk(): void
    {
        $stream = $this->customStream();

        $streamAtEndOfChunk1 = $this->advanceCustomStreamToEndOfChunk1($stream);

        $this->assertEquals(
            $this->customCharacterStartOfChunk2(),
            $streamAtEndOfChunk1->next()
        );
    }

    public function testPreviousReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->blankStream()->previous());
    }

    public function testPreviousReturnsFalseWhenPreviousDoesNotExist(): void
    {
        $this->assertFalse($this->presentStream()->previous());
    }

    public function testPreviousReturnsStringWhenPreviousDoesExistInCurrentChunk(): void
    {
        $stream = $this->presentStream();

        $stream->getNextCharacter();

        $this->assertEquals(
            $this->presentCharacter1(),
            $stream->previous()
        );
    }

    public function testPreviousReturnsStringWhenPreviousDoesNotExistInCurrentChunk(): void
    {
        $stream = $this->customStream();

        $streamAtStartOfChunk2 = $this->advanceCustomStreamToStartOfChunk2($stream);

        $this->assertEquals(
            $this->customCharacterEndOfChunk1(),
            $stream->previous()
        );
    }

    public function testResetResetsInternalPointer(): void
    {
        $stream = $this->presentStream();

        $stream->next();
        $stream->next();

        $this->assertEquals($this->presentCharacter2(), $stream->current());

        $stream->reset();

        $this->assertEquals($this->presentCharacter1(), $stream->current());
    }

    public function testPeekThrowsInvalidArgumentExceptionWhenNIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->presentStream()->peek(-1);
    }

    public function testPeekThrowsInvalidArgumentExceptionWhenNIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->presentStream()->peek(0);
    }

    public function testPeekReturnsStringWhenTextIsEmpty(): void
    {
        $this->assertEquals('', $this->blankStream()->peek());
    }

    public function testPeekReturnsStringWhenOnLastCharacter(): void
    {
        $stream = $this->presentStream();

        $stream->next();
        $stream->next();

        $this->assertEquals('', $stream->peek());
    }

    public function testPeekReturnsStringWhenAfterLastCharacter(): void
    {
        $stream = $this->presentStream();

        $stream->next();
        $stream->next();
        $stream->next();

        $this->assertEquals('', $stream->peek());
    }

    public function testPeekReturnsStringWhenNIsPositive(): void
    {
        $this->assertEquals('oo', $this->presentStream()->peek(2));
    }

    public function testPeekDoesNotChangeIndexWhenNIsPositive(): void
    {
        $stream = $this->presentStream();

        $this->assertEquals('f', $stream->current());
        $this->assertEquals('oo', $stream->peek(2));
        $this->assertEquals('f', $stream->current());
    }

    public function testPeekReturnsStringWhenNIsLongerThanText(): void
    {
        $this->assertEquals('oo', $this->presentStream()->peek(999));
    }

    public function testPeekDoesNotChangeIndexWhenNIsLongerThanText(): void
    {
        $stream = $this->presentStream();

        $this->assertEquals('f', $stream->current());
        $this->assertEquals('oo', $stream->peek(999));
        $this->assertEquals('f', $stream->current());
    }

    public function testIsOnReturnsFalseWhenTextIsBlank(): void
    {
        $this->assertFalse($this->blankStream()->isOn('a'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotOnCharacter(): void
    {
        $this->assertFalse($this->presentStream()->isOn('a'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotOnString(): void
    {
        $this->assertFalse($this->presentStream()->isOn('bar'));
    }

    public function testIsOnReturnsTrueWhenTextIsOnCharacter(): void
    {
        $this->assertTrue($this->presentStream()->isOn('f'));
    }

    public function testIsOnReturnsTrueWhenTextIsOnString(): void
    {
        $this->assertTrue($this->presentStream()->isOn('foo'));
    }

    public function testIsOnReturnsFalseWhenTextIsNotInArrayOfCharacters(): void
    {
        $this->assertFalse($this->presentStream()->isOn(['b', 'a', 'r']));
    }

    public function testIsOnReturnsFalseWhenTextIsNotInArrayOfStrings(): void
    {
        $this->assertFalse($this->presentStream()->isOn(['bar', 'baz', 'qux']));
    }

    public function testIsOnReturnsTrueWhenTextIsInArrayOfCharacters(): void
    {
        $this->assertTrue($this->presentStream()->isOn(['a', 'b', 'c', 'd', 'e', 'f']));
    }

    public function testIsOnReturnsTrueWhenTextIsInArrayOfStrings(): void
    {
        $this->assertTrue($this->presentStream()->isOn(['foo', 'bar', 'baz']));
    }

    public function testIsOnRegexThrowsExceptionWhenLengthIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->presentStream()->isOnRegex('/[a-z]/', -1);
    }

    public function testIsOnRegexThrowsExceptionWhenLengthIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->presentStream()->isOnRegex('/[a-z]/', 0);
    }

    public function testIsOnRegexReturnsFalseWhenTextIsBlank(): void
    {
        $this->assertFalse($this->blankStream()->isOnRegex('/[a-z]/'));
    }

    public function testIsOnRegexReturnsFalseWhenTextIsNotOnPattern(): void
    {
        $this->assertFalse($this->presentStream()->isOnRegex('/bar/'));
    }

    public function testIsOnRegexReturnsTrueWhenTextIsOnPattern(): void
    {
        $this->assertTrue($this->presentStream()->isOnRegex('/foo/', 3));
    }

    /**
     * A blank string is, well, blank (e.g., "")
     */
    private function blankFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')->at($this->root);
    }

    private function blankStream(): File
    {
        return new File($this->blankFile()->url());
    }

    /**
     * An "empty-ish" string is something PHP's `empty()` method would evaluate
     * to true (e.g., "0")
     */
    private function emptyishFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')
            ->withContent('0')
            ->at($this->root);
    }

    private function emptyishStream(): File
    {
        return new File($this->emptyishFile()->url());
    }

    private function emptyishCharacter1(): string
    {
        return '0';
    }

    /**
     * A present string is not empty (e.g., "foo")
     */
    private function presentFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')
            ->withContent('foo')
            ->at($this->root);
    }

    private function presentStream(): File
    {
        return new File($this->presentFile()->url());
    }

    private function presentCharacters(): array
    {
        return ['f', 'o', 'o'];
    }

    private function presentCharacter1(): string
    {
        return 'f';
    }

    private function presentCharacter2(): string
    {
        return 'o';
    }

    private function presentCharacter3(): string
    {
        return 'o';
    }

    /**
     * A "custom" stream is useful for chunk-related tests (e.g., when the
     * next character exists in the next chunk)
     */
    private function customString(): string
    {
        return 'foo bar baz qux quux corge';
    }

    private function customFile(): vfsStreamFile
    {
        return vfsStream::newFile('example.txt')
            ->withContent($this->customString())
            ->at($this->root);
    }

    private function customChunkSize(): int
    {
        return 5;
    }

    private function customChunker(): Chunker\File
    {
        return new Chunker\File(
            $this->customFile()->url(),
            null,
            $this->customChunkSize()
        );
    }

    private function customStream(): File
    {
        return new File($this->customChunker());
    }

    private function advanceCustomStreamToEndOfChunk1($stream): File
    {
        for ($i = 0; $i < $this->customChunkSize() - 1; ++$i) {
            $stream->next();
        }

        return $stream;
    }

    private function advanceCustomStreamToStartOfChunk2($stream): File
    {
        for ($i = 0; $i < $this->customChunkSize(); ++$i) {
            $stream->next();
        }

        return $stream;
    }

    private function customCharacterEndOfChunk1(): string
    {
        return 'b';
    }

    private function customCharacterStartOfChunk2(): string
    {
        return 'a';
    }
}
