<?php

namespace Jstewmc\Stream;

use Jstewmc\Chunker;

class File extends Stream
{
    /**
     * @param  Chunker\File|string  the stream's source
     */
    public function __construct($source)
    {
        if ($source instanceof Chunker\File) {
            $this->chunker = $source;
        } elseif (is_string($source)) {
            $this->chunker = new Chunker\File($source);
        } else {
            throw new \InvalidArgumentException(
                'source must be a file chunker or string pathname'
            );
        }

        parent::__construct();
    }
}
