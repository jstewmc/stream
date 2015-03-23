<?php
/**
 * The Jstewmc\Stream\File class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */
 
namespace Jstewmc\Stream;

/**
 * The File stream class 
 *
 * The File stream class allows you to iterate over the characters in a very large
 * text file character-by-character.
 *
 * @since  0.1.0
 */
class File extends Stream
{
	/* !Protected properties */
	
	/**
	 * @var  string  the file's name
	 * @since  0.1.0
	 */
	protected $name;	
	
	
	/* !Get methods */
	
	/**
	 * Returns the name of the file
	 *
	 * @return  string|null
	 * @since  0.1.0
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the file's name
	 *
	 * @param  string  $name  the file's name
	 * @return  self
	 * @throws  InvalidArgumentException  if $name is not a string or is not readable
	 * @since  0.1.0
	 */
	public function setName($name)
	{
		if ( ! is_string($name) || ! is_readable($name)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, name, to be a readable file name"
			);	
		}
		
		$this->name = $name;
		
		return $this;
	}

	
	/* !Magic methods */
	
	/**
	 * Called to construct the class
	 *
	 * @param  string  $name  the file's name (optional; if omitted, defaults to null)
	 * @return  self
	 * @throws  InvalidArgumentException  if $name is not a readable file name
	 * @since  0.1.0
	 */
	public function __construct($name = null) 
	{
		if ($name !== null) {
			$this->setName($name);
		}
		
		return;
	}
	
	
	/* !Protected methods */
	
	/**
	 * Reads the current chunk's characters
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	protected function readCurrentChunk()
	{
		if ($this->name === null) {
			throw new \BadMethodCallException(
				__METHOD__."() expects property 'name' to be set"
			);
		}
		
		$chunk = @file_get_contents(
			$this->name, 
			false, 
			null, 
			$this->chunkIndex * $this->chunkSize, 
			$this->chunkSize
		);
		
		$this->readChunk($chunk);
		
		return;
	}
	
	/**
	 * Reads the next chunk's characters
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	protected function readNextChunk()
	{
		if ($this->name === null) {
			throw new \BadMethodCallException(
				__METHOD__."() expects property 'name' to be set"
			);
		}
		
		// file_get_contents() will return false and raise an E_WARNING if the offset 
		//     argument does not exist (we just need the false)
		// 
		$chunk = @file_get_contents(
			$this->name,
			false, 
			null,
			++$this->chunkIndex * $this->chunkSize,
			$this->chunkSize
		);

		$this->readChunk($chunk);
		
		return;
	}
	
	/**
	 * Reads the previous chunk's characters
	 * 
	 * @return  void
	 * @since  0.1.0
	 */
	protected function readPreviousChunk()
	{
		if ($this->name === null) {
			throw new \BadMethodCallException(
				__METHOD__."() expects property 'name' to be set"
			);
		}
		
		// if index is not zero
		if ($this->chunkIndex > 0) {
			$chunk = @file_get_contents(
				$this->name, 
				false,
				null,
				--$this->chunkIndex * $this->chunkSize,
				$this->chunkSize
			);
		} else {
			// otherwise, the offset will be negative, and file_get_contents() will return
			//     contents starting from the end of the file (which we don't want)
			//
			$chunk = false;
		}
		
		$this->readChunk($chunk);
		
		return;
	}
}
