<?php
/**
 * The Jstewmc\Stream\Stream class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Stream;

/**
 * The stream class
 *
 * The Stream class allows you treat the contents of files and strings as a
 * continuous flow of characters. That is, you can iterate over the characters 
 * in a very large file or string without holding the contents of the file as 
 * an array in memory.
 *
 * @since  0.1.0
 */
abstract class Stream
{
	/* !Protected properties */
	
	/**
	 * @var  string[]  an array of the current chunk's characters
	 * @since  0.1.0
	 */
	protected $characters;
	
	/**
	 * @var  int  the current chunk's index; defaults to 0
	 * @since  0.1.0
	 */
	protected $chunkIndex = 0;
	
	/**
	 * @var  int  the (maximum) chunk size; defaults to 8 kilobytes (8 * 1024 bytes)
	 * @since  0.1.0
	 */
	protected $chunkSize = 8192;
	
	
	/* !Get methods */
	
	/**
	 * Returns the current chunk's characters
	 *
	 * @return  string[]|null
	 * @since  0.1.0
	 */
	public function getCharacters()
	{
		return $this->characters;
	}
	
	/**
	 * Returns the current chunk's index
	 *
	 * @return  int|null
	 * @since  0.1.0
	 */
	public function getChunkIndex()
	{
		return $this->chunkIndex;
	}

	/**
	 * Returns the (maximum) chunk size
	 *
	 * @return  string|null
	 * @since  0.1.0
	 */
	public function getChunkSize()
	{
		return $this->chunkSize;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the chunk's current characters
	 *
	 * @param  string[]  an array of characters
	 * @return  self
	 * @throws  InvalidArgumentException  if $characters is not an array
	 * @throws  InvalidArgumentException  if $characters is not an array of strings
	 * @since  0.1.0
	 */
	public function setCharacters($characters)
	{
		if ( ! is_array($characters) 
			|| count($characters) !== count(array_filter($characters, 'is_string'))
		) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, characters, to be an array of strings"
			);	
		}
		
		$this->characters = $characters;
		
		return $this;
	}
	
	/**
	 * Sets the chunk index
	 *
	 * @param  int  $chunkIndex  the chunk's index
	 * @return  self
	 * @throws  InvalidArgumentException  if $chunkIndex is not an integer
	 * @since  0.1.0
	 */
	public function setChunkIndex($chunkIndex)
	{
		if ( ! is_numeric($chunkIndex) || ! is_int(+$chunkIndex) || $chunkIndex < 0) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, index, to be a position integer or zero"
			);
		}
		
		$this->chunkIndex = $chunkIndex;
		
		return $this;
	}
	
	/**
	 * Sets the (maximum) chunk size
	 *
	 * @param  int  $chunkSize  the (maximum) chunk size
	 * @return  self
	 * @throws  InvalidArgumentException  if $size is not a positive, non-zero integer
	 * @since  0.1.0
	 */
	public function setChunkSize($chunkSize)
	{
		if ( ! is_numeric($chunkSize) || ! is_int(+$chunkSize) || $chunkSize <= 0) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, size, to be a positive, non-zero integer"
			);	
		}
		
		$this->chunkSize = $chunkSize;
		
		return $this;
	}
	
	
	/* !Public methods */
	
	/**
	 * An alias for the getCurrentCharacter() method
	 *
	 * @return  string|false
	 * @since   0.1.0
	 */
	public function current()
	{
		return $this->getCurrentCharacter();	
	}
	
	/**
	 * Returns the current character
	 *
	 * @return  string|false
	 * @since   0.1.0
	 */
	public function getCurrentCharacter()
	{
		$this->beforeGet();
			
		return current($this->characters);
	}
	
	/**
	 * Returns the next character
	 *
	 * @return  string|false
	 * @since   0.1.0
	 */
	public function getNextCharacter()
	{
		$this->beforeGet();
	
		if ($this->hasNextCharacter()) {
			$next = next($this->characters);
		} elseif ($this->hasNextChunk()) {
			$this->readNextChunk();
			$next = reset($this->characters);
		} else {
			$next = false;
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
		$this->beforeGet();
		
		if ($this->hasPreviousCharacter()) {
			$previous = prev($this->characters);
		} elseif ($this->hasPreviousChunk()) {
			$this->readPreviousChunk();
			$previous = end($this->characters);
		} else {
			$previous = false;
		}
		
		return $previous;
	}
	
	/**
	 * Returns true if the stream has a character
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasCharacter()
	{
		return $this->current() !== false;
	}
	
	/**
	 * An alias for the getNextCharacter() method
	 *
	 * @return  string|false
	 * @since   0.1.0
	 */
	public function next()
	{
		return $this->getNextCharacter();
	}
	
	/**
	 * An alias for the getPreviousCharacter() method
	 *
	 * @return  string|false
	 * @since   0.1.0
	 */
	public function previous()
	{
		return $this->getPreviousCharacter();
	}
	
	/**
	 * Resets the stream's internal pointer
	 *
	 * @return  void
	 * @since  0.1.0
	 */
	public function reset()
	{
		$this->chunkIndex = 0;
		$this->characters = null;
		
		return;
	}
	
	
	/* !Protected methods */
	
	/**
	 * Returns the max number of chunks in the stream
	 *
	 * @return  int
	 * @since  0.1.0
	 */
	abstract protected function getMaxChunks();
	
	/**
	 * Called before getting a character
	 *
	 * On the first call to a getXCharacter() method, the $characters array will be 
	 * null, and I'll initialize it to the first chunk.
	 * 
	 * @return  void
	 * @since  0.1.0
	 */
	protected function beforeGet()
	{
		if ($this->characters === null) {
			$this->readCurrentChunk();
		}
		
		return;
	}
	
	/**
	 * Returns true if a next character exists in the current chunk
	 *
	 * @return  bool
	 * @since   0.1.0
	 */
	protected function hasNextCharacter()
	{
		return is_array($this->characters) 
			&& key($this->characters) !== null 
			&& array_key_exists(key($this->characters) + 1, $this->characters);
	}
	
	/**
	 * Returns true if a next chunk exists in the stream's source
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	protected function hasNextChunk()
	{
		return $this->chunkIndex + 1 <= $this->getMaxChunks();
	}
	
	/**
	 * Returns true if a previous character exists in the current chunk
	 * 
	 * @return  bool
	 * @since   0.1.0
	 */
	protected function hasPreviousCharacter()
	{
		return is_array($this->characters)
			&& key($this->characters) !== null 
			&& array_key_exists(key($this->characters) - 1, $this->characters);
	}
	
	/**
	 * Returns true if a previous chunk exists in the stream's source
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	protected function hasPreviousChunk()
	{
		return $this->chunkIndex - 1 >= 0;
	}
	
	/**
	 * Reads a chunk
	 *
	 * @param  string  $chunk  the string chunk to read
	 * @return  void
	 * @since  0.1.0
	 */
	protected function readChunk($chunk)
	{
		if (is_string($chunk) && ! empty($chunk)) {
			$this->characters = str_split($chunk);
		} else {
			$this->characters = [];
		}
		
		return;
	}
	
	/**
	 * Reads the current chunk into memory
	 *
	 * @return  void
	 * @since  0.1.0
	 */
	abstract protected function readCurrentChunk();
	
	/**
	 * Reads the next chunk into memory
	 *
	 * @return  void
	 * @since  0.1.0
	 */
	abstract protected function readNextChunk();
	
	/**
	 * Reads the previous chunk into memory
	 *
	 * @return  void
	 * @since  0.1.0
	 */
	abstract protected function readPreviousChunk();
}