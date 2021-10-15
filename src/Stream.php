<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker\Chunker;

abstract class Stream
{
    /**
     * The current chunk's characters
     */
    private array $characters = [];

    /**
     * The stream's underlying chunker
     */
    protected Chunker $chunker;

    public function __construct()
    {
        $this->readCurrentChunk();
    }

    /**
     * An alias for the `getCurrentCharacter()` method
     *
     * @return  string|false
     */
    public function current()
    {
        return $this->getCurrentCharacter();
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Returns the current character
     *
     * @return  string|false
     */
    public function getCurrentCharacter()
    {
        return current($this->characters);
    }

    /**
     * Returns the next character
     *
     * @return  string|false
     */
    public function getNextCharacter()
    {
        $next = next($this->characters);

        if ($next !== false) {
            return $next;
        }

        if ($this->chunker->hasNextChunk()) {
            $this->readNextChunk();
            $next = reset($this->characters);
        }

        return $next;
    }

    /**
     * Returns the previous character
     *
     * @return  string|false
     * @since   0.1.0
     */
    public function getPreviousCharacter()
    {
        $previous = prev($this->characters);

        if ($previous !== false) {
            return $previous;
        }

        if ($this->chunker->hasPreviousChunk()) {
            $this->readPreviousChunk();
            $previous = end($this->characters);
        }

        return $previous;
    }

    /**
     * Returns true if the stream has one or more characters remaining
     * (including the current character)
     */
    public function hasCharacters(): bool
    {
        return $this->current() !== false;
    }

    /**
     * An alias for the getNextCharacter() method
     *
     * @return  string|false
     */
    public function next()
    {
        return $this->getNextCharacter();
    }

    /**
     * An alias for the getPreviousCharacter() method
     *
     * @return  string|false
     */
    public function previous()
    {
        return $this->getPreviousCharacter();
    }

    /**
     * Resets the stream's internal pointer
     */
    public function reset(): void
    {
        $this->characters = [];

        $this->chunker->reset();

        $this->read($this->chunker->current());

        return;
    }

    /**
     * Reads a chunk into the $characters array
     *
     * @param  string|false  $chunk  the string chunk to read (or false)
     */
    private function read($chunk): void
    {
        if ($this->exists($chunk) && $this->present($chunk)) {
            $this->characters = $this->explode($chunk);
        } else {
            $this->characters = [];
        }
    }

    private function readCurrentChunk(): void
    {
        $this->read($this->chunker->current());
    }

    private function readNextChunk(): void
    {
        $this->read($this->chunker->next());
    }

    private function readPreviousChunk(): void
    {
        $this->read($this->chunker->previous());
    }

    private function exists($chunk): bool
    {
        return $chunk !== false;
    }

    /**
     * Returns true if the chunk is not empty
     *
     * We want to avoid exploding an empty string, because the `$characters`
     * array will have an empty string as its only element (e.g., `['']`).
     *
     * Keep in mind, the single-byte `strlen()` function is ok to use here. Our
     * goal is to be sure the string isn't empty without using PHP's native
     * `empty()` method, which will consider "0" an empty string.
     *
     * @param  string  $chunk  the chunk to check
     */
    private function present(string $chunk): bool
    {
        return strlen($chunk) > 0;
    }

    private function count(string $chunk): int
    {
        return mb_strlen($chunk, $this->chunker->getEncoding());
    }

    private function explode(string $chunk): array
    {
        $characters = [];

        $length = $this->count($chunk);

        // Use a loop to split the multi-byte string. I have an old note that
        // using `mb_split('//', $chunk)` here didn't work as expected.
        for ($i = 0; $i < $length; ++$i) {
            $characters[] = mb_substr(
                $chunk,
                $i,
                1,
                $this->chunker->getEncoding()
            );
        }

        return $characters;
    }
}
