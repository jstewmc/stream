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
 * The Stream class
 *
 * The Stream class allows you treat the contents of files and strings as a
 * continuous flow of characters. That is, you can iterate over the characters 
 * in a very large file or string without holding the contents of the file as 
 * an array in memory.
 *
 * @since  0.1.0
 * @since  0.2.0  switched to using Jstewmc\Chunker\Chunker class
 */
class Stream
{
	/* !Protected properties */
	
	/**
	 * @var  string[]  an array of the current chunk's characters
	 * @since  0.1.0
	 */
	protected $characters = [];
	
	/**
	 * @var  Jstewmc\Chunker\Chunker  the stream's chunker
	 * @since  0.2.0
	 */
	protected $chunker;
	
	
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
	
	
	/* !Set methods */
	
	/**
	 * Sets the chunk's current characters
	 *
	 * @param  string[]  an array of characters
	 * @return  self
	 * @throws  InvalidArgumentException  if $characters is not an array of strings
	 * @since  0.1.0
	 */
	public function setCharacters(Array $characters)
	{
		if (count($characters) !== count(array_filter($characters, 'is_string'))) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, characters, to be an array of "
					. "strings"
			);	
		}
		
		$this->characters = $characters;
		
		return $this;
	}
		
	
	/* !Magic methods */
	
	/**
	 * Called when the Stream is constructed
	 *
	 * @param  Jstewmc\Chunker\Chunker  $chunker  the stream's chunker
	 * @return  self
	 */
	public function __construct(\Jstewmc\Chunker\Chunker $chunker)
	{
		$this->chunker = $chunker;
		
		$this->read($this->chunker->current());
		
		return;
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
		$next = next($this->characters);
		
		if ($next === false && $this->chunker->hasNextChunk()) {
			$this->read($this->chunker->getNextChunk());
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

		if ($previous === false && $this->chunker->hasPreviousChunk()) {
			$this->read($this->chunker->getPreviousChunk());
			$previous = end($this->characters);
		}
		
		return $previous;
	}
	
	/**
	 * Returns true if the stream has *one or more* characters
	 *
	 * @return  bool
	 * @since  0.1.0
	 */
	public function hasCharacters()
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
		$this->characters = [];
		
		$this->chunker->reset();
		
		$this->read($this->chunker->current());
		
		return;
	}
	
	
	/* !Protected methods */
	
	/**
	 * Returns true if a next character exists in the current chunk
	 *
	 * @return  bool
	 * @since   0.1.0
	 */
	protected function hasNextCharacter()
	{
		return key($this->characters) !== null 
			&& array_key_exists(key($this->characters) + 1, $this->characters);
	}
	
	/**
	 * Returns true if a previous character exists in the current chunk
	 * 
	 * @return  bool
	 * @since   0.1.0
	 */
	protected function hasPreviousCharacter()
	{
		return key($this->characters) !== null 
			&& array_key_exists(key($this->characters) - 1, $this->characters);
	}
	
	/**
	 * Reads a chunk into the stream's characters array
	 *
	 * @param  string|false  $chunk  the string chunk to read or false
	 * @return  void
	 * @since  0.2.0
	 */
	protected function read($chunk)
	{
		if ( ! is_string($chunk) && $chunk !== false) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, chunk, to be a string or false"
			);
		}
		
		$this->characters = [];
		
		// if $chunk is not false and not empty...
		// keep in mind, the single-byte strlen() function is ok here, I just want
		//     to be sure the string isn't empty; otherwise, the characters array
		//     will have an empty string as an element (e.g., [''])
		// also, don't use PHP's native empty() method here either; empty() will
		//     consider "0" an empty string, but it's a valid value
		//
		if (is_string($chunk) && strlen($chunk) > 0) {
			// get the chunk's length
			$len = mb_strlen($chunk, $this->chunker->getEncoding());
			// loop through the chunk's characters
			for ($i = 0; $i < $len; ++$i) {
				// append the character to the characters array...
				// I (Jack) tried using mb_split('//', $chunk) here; however, it 
				//     didn't split the string into characters as expected
				//
				$this->characters[] = mb_substr(
					$chunk, $i, 1, $this->chunker->getEncoding()
				);
			}	
		}
		
		return;
	}
}