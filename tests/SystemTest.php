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

        $this->assertEquals('r', $stream->current());

        while (false !== $stream->previous()) {
            $stream->previous();
        }

        $this->assertEquals('b', $stream->current());

        while (false !== $stream->next()) {
            $stream->next();
        }

        $this->assertEquals('r', $stream->current());
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

        $this->assertEquals('r', $stream->current());
    }

    public function testStreamIsIdempotentAtBeginning(): void
    {
        $stream = new Text('bar');

        for ($i = 0; $i < 3; ++$i) {
            $stream->previous();
        }

        $this->assertEquals('b', $stream->current());
    }
}
