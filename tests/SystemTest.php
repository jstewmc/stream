<?php

namespace Jstewmc\Stream;

class SystemTest extends \PHPUnit\Framework\TestCase
{
    public function testStreamIsRepeatable(): void
    {
        $stream = new Text('bar');

        while (false !== $stream->next()) {
            $stream->next();
        }

        $this->assertFalse($stream->current());

        $this->assertEquals('r', $stream->previous());
        while (false !== $stream->previous()) {
            $stream->previous();
        }

        $this->assertFalse($stream->current());

        $this->assertEquals('b', $stream->next());
        while (false !== $stream->next()) {
            $stream->next();
        }

        $this->assertFalse($stream->current());

        $this->assertEquals('r', $stream->previous());
    }

    public function testStreamIsIdempotentAtEnd(): void
    {
        $stream = new Text('bar');

        while (false !== $stream->next()) {
            $stream->next();
        }

        for ($i = 0; $i < 3; ++$i) {
            $stream->next();
        }

        $this->assertFalse($stream->current());
        $this->assertEquals('r', $stream->previous());
    }

    public function testStreamIsIdempotentAtBeginning(): void
    {
        $stream = new Text('bar');

        for ($i = 0; $i < 3; ++$i) {
            $stream->previous();
        }

        $this->assertFalse($stream->current());
        $this->assertEquals('b', $stream->next());
    }

    public function testExampleIsCorrect(): void
    {
        $stream = new Text('bar');

        $this->assertEquals('b', $stream->current());

        $this->assertEquals('a', $stream->next());
        $this->assertEquals('r', $stream->next());
        $this->assertFalse($stream->next());

        $this->assertFalse($stream->current());

        $this->assertEquals('r', $stream->previous());
        $this->assertEquals('a', $stream->previous());
        $this->assertEquals('b', $stream->previous());
        $this->assertFalse($stream->previous());

        $this->assertFalse($stream->current());
    }
}
