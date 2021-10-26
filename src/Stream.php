<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker\Chunker;

abstract class Stream
{
    /**
     * The current chunk's characters
     */
    private array $characters = [];

    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * The current chunk's index
     */
    private int $index = 0;

    /**
     * The stream's underlying chunker
     */
    protected Chunker $chunker;

    public function __construct()
    {
        $this->readCurrentChunk();
    }

    private function readCurrentChunk(): void
    {
        $this->read($this->chunker->current());

        $this->index = 0;
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

    /**
     * Returns the current character
     *
     * @return  string|false
     */
    public function getCurrentCharacter()
    {
        if (!$this->hasCurrentCharacter()) {
            return false;
        }

        if ($this->isBeforeFirst() || $this->isAfterLast()) {
            return false;
        }

        return $this->characters[$this->index];
    }

    private function hasCurrentCharacter(): bool
    {
        return array_key_exists($this->index, $this->characters);
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
     * Returns the next character
     *
     * @return  string|false
     */
    public function getNextCharacter()
    {
        if ($this->isAfterLast()) {
            return false;
        }

        if ($this->hasNextCharacter()) {
            $next = $this->characters[++$this->index];
        } elseif ($this->hasNextChunk()) {
            $this->readNextChunk();
            $next = $this->characters[$this->index];
        } else {
            ++$this->index;
            $next = false;
        }

        return $next;
    }

    private function hasNextChunk(): bool
    {
        return $this->chunker->hasNextChunk();
    }

    private function hasNextCharacter(): bool
    {
        return array_key_exists($this->index + 1, $this->characters);
    }

    private function readNextChunk(): void
    {
        $this->read($this->chunker->next());

        $this->index = 0;
    }

    private function isAfterLast(): bool
    {
        return $this->index === count($this->characters);
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
     * Returns the previous character
     *
     * @return  string|false
     */
    public function getPreviousCharacter()
    {
        if ($this->isBeforeFirst()) {
            return false;
        }

        if ($this->hasPreviousCharacter()) {
            $previous = $this->characters[--$this->index];
        } elseif ($this->hasPreviousChunk()) {
            $this->readPreviousChunk();
            $previous = $this->characters[$this->index];
        } else {
            --$this->index;
            $previous = false;
        }

        return $previous;
    }

    private function hasPreviousChunk(): bool
    {
        return $this->chunker->hasPreviousChunk();
    }

    private function hasPreviousCharacter(): bool
    {
        return array_key_exists($this->index - 1, $this->characters);
    }

    private function readPreviousChunk(): void
    {
        $this->read($this->chunker->previous());

        $this->index = count($this->characters) - 1;
    }

    private function isBeforeFirst(): bool
    {
        return $this->index < 0;
    }

    /**
     * Peeks the next $n characters without changing the index
     */
    public function peek(int $n = 1): string
    {
        if ($n <= 0) {
            throw new \InvalidArgumentException(
                'n must be a positive, non-zero integer'
            );
        }

        $characters = '';

        for ($i = 0; $i < $n; ++$i) {
            $character = $this->next();
            if ($character === false) {
                $this->previous();
                break;
            }
            $characters .= $character;
        }

        while ($i > 0) {
            $this->previous();
            --$i;
        }

        return $characters;
    }

    /**
     * Returns true if the stream is "on" the $search(es) (includes the current
     * character)
     */
    public function isOn($search): bool
    {
        if (is_string($search)) {
            $isOn = $this->isOnString($search);
        } elseif (is_array($search)) {
            $isOn = $this->isOnArray($search);
        } else {
            throw new \InvalidArgumentException(
                'search should be a string or array of strings'
            );
        }

        return $isOn;
    }

    private function isOnString(string $search): bool
    {
        $subject = $this->current();

        $n = mb_strlen($search, $this->chunker->getEncoding()) - 1;
        if ($n > 0) {
            $subject .= $this->peek($n);
        }

        return $subject === $search;
    }

    private function isOnArray(array $searches): bool
    {
        foreach ($searches as $search) {
            if ($this->isOnString($search)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A `$length` argument is required, because we need to know how many
     * characters to get for the comparison.
     */
    public function isOnRegex(string $pattern, int $length = 1): bool
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException(
                'limit must be a positive, non-zero integer'
            );
        }

        $subject = $this->current();

        $n = $length - 1;
        if ($n > 0) {
            $subject .= $this->peek($n);
        }

        return preg_match($pattern, $subject);
    }

    /**
     * Resets the stream's internal pointer
     */
    public function reset(): void
    {
        $this->index = 0;

        $this->characters = [];

        $this->chunker->reset();

        $this->read($this->chunker->current());
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

    private function count(string $chunk): int
    {
        return mb_strlen($chunk, $this->chunker->getEncoding());
    }
}
