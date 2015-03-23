<?php
/**
 * The Jstewmc\Stream\Text class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Stream;

/**
 * The Text stream class
 *
 * @since  0.1.0
 */
class Text extends Stream
{
	/* !Protected properties */
	
	/**
	 * @var  string  the text stream's underlying text
	 * @since  0.1.0
	 */
	protected $text;	
	
	
	/* !Get methods */
	
	/**
	 * Returns the text stream's text
	 *
	 * @return  string|null
	 * @since   0.1.0
	 */
	public function getText()
	{
		return $this->text;
	}
	
	
	/* !Set methods */
	
	/**
	 * Sets the stream's text
	 *
	 * @param  string|null  $text  the stream's text
	 * @return  self  
	 * @since  0.1.0
	 */
	public function setText($text)
	{
		if ( ! is_string($text)) {
			throw new \InvalidArgumentException(
				__METHOD__."() expects parameter one, text, to be a string"
			);
		}
		
		$this->text = $text;
		
		return $this;
	}
	
	
	/* !Magic methods */
	
	/**
	 * Called to construct the object
	 *
	 * @param  string  $text  the stream's text (optional; if omitted, defaults to 
	 *     null)
	 * @return  self
	 * @throws  InvalidArgumentException  if $text is neither null nor a string
	 * @since   0.1.0
	 */
	public function __construct($text = null)
	{
		if ($text !== null) {
			$this->setText($text);
		}
		
		return;
	}
	 
	
	/* !Protected methods */
	
	/**
	 * Reads the current chunk
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	protected function readCurrentChunk()
	{
		$chunk = substr(
			$this->text, 
			$this->chunkIndex * $this->chunkSize, 
			$this->chunkSize
		);
		
		$this->readChunk($chunk);
		
		return;
	}
	
	/**
	 * Reads the next chunk
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	protected function readNextChunk()
	{
		$chunk = substr(
			$this->text, 
			++$this->chunkIndex * $this->chunkSize, 
			$this->chunkSize
		);
		
		$this->readChunk($chunk);
		
		return;
	}
	
	/**
	 * Reads the current chunk
	 *
	 * @return  void
	 * @since   0.1.0
	 */
	protected function readPreviousChunk()
	{
		// if the chunk index is greater than zero
		if ($this->chunkIndex > 0) {
			$chunk = substr(
				$this->text, 
				--$this->chunkIndex * $this->chunkSize, 
				$this->chunkSize
			);
		} else {
			// otherwise, the index will be negative and substr() will start at the end
			//    of the string (which is undesired)
			//
			$chunk = false;
		}
		
		$this->readChunk($chunk);
		
		return;
	}
}
