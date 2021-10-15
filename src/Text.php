<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker;

class Text extends Stream
{
    /**
     * @param  Chunker\Text|string  the stream's source
     */
    public function __construct($source)
    {
        if ($source instanceof Chunker\Text) {
            $this->chunker = $source;
        } elseif (is_string($source)) {
            $this->chunker = new Chunker\Text($source);
        } else {
            throw new \InvalidArgumentException(
                'source must be a text chunker or string'
            );
        }

        parent::__construct();
    }
}
