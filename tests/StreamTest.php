<?php
/**
 * The TextTest class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Stream;

use Jstewmc\Chunker;

/**
 * A test suite for the Stream class
 *
 * @since  0.1.0
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
	/* !getCharacters() */
	
	/**
	 * getCharacters() should return an array if characters do not exist
	 */
	public function testGetCharacters_returnsArray_ifCharactersDoNotExist()
	{
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		
		$this->assertEquals([], $stream->getCharacters());
		
		return;
	}
	
	/**
	 * getCharacters() should return an array if characters do exist
	 */
	public function testGetCharacters_returnsArray_ifCharactersDoExist()
	{
		$characters = ['f', 'o', 'o'];
		
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		$stream->setCharacters($characters);
		
		$this->assertEquals($characters, $stream->getCharacters());
		
		return;
	}
	
	
	/* !getCurrentCharacter() */

	/**
	 * getCurrentCharacter() should return false if text is empty
	 */
	public function testGetCurrentCharacter_returnsFalse_ifTextIsEmpty()
	{
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->getCurrentCharacter());
		
		return;
	}
	
	/**
	 * getCurrentCharacter() should return string if text is not empty
	 */
	public function testGetCurrentCharacter_returnsString_ifTextIsNotEmpty()
	{
		$chunker = new Chunker\Text('foo bar baz');
		
		$stream = new Stream($chunker);
		
		$this->assertEquals('f', $stream->getCurrentCharacter());
		
		return;
	}
	
	/**
	 * getCurrentCharacter() should return string if character evaluates to empty
	 */
	public function testGetCharacter_returnsString_ifCharacterEvaluatesEmpty()
	{
		$chunker = new Chunker\Text('0');
		
		$stream = new Stream($chunker);
		
		$this->assertEquals('0', $stream->getCurrentCharacter());
		
		return;
	}
	

	/* !getNextCharacter() */
	
	/**
	 * getNextCharacter() should return false if the text is empty
	 */
	public function testGetNextCharacter_returnsFalse_ifTextIsEmpty()
	{
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return false if a next character does not exist
	 */
	public function testGetNextCharacter_returnsFalse_ifNextDoesNotExist()
	{
		$chunker = new Chunker\Text('foo');
		
		$chunker->setSize(1);
		$chunker->setIndex(2);
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return string if the next character exists in the
	 *     current chunk
	 */
	public function testGetNextCharacter_returnsString_ifNextDoesExistInCurrentChunk()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$this->assertEquals('o', $stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return string if the next character exists in the
	 *     next chunk
	 */
	public function testGetNextCharacter_returnsString_ifNextDoesNotExistInCurrentChunk()
	{
		$chunker = new Chunker\Text('foo');
		
		$chunker->setSize(1);
		
		$stream = new Stream($chunker);
		
		$this->assertEquals('o', $stream->getNextCharacter());
		
		return;
	}
	
	
	/* !getPreviousCharacter() */
	
	/**
	 * getPreviousCharacter() should return false if the file is empty
	 */
	public function testGetPreviousCharacter_returnsFalse_ifTextIsEmpty()
	{
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return false if a previous character does not exist
	 */
	public function testGetPreviousCharacter_returnsFalse_ifPreviousDoesNotExist()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return string if a previous character exists in the
	 *     current chunk
	 */
	public function testGetPreviousCharacter_returnsString_ifPreviousDoesExistInCurrentChunk()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$stream->getNextCharacter();
		
		$this->assertEquals('f', $stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return string if a previous character does not 
	 *     exist in the current chunk
	 */
	public function testGetPreviousCharacter_returnsString_ifPreviousDoesNotExistInCurrentChunk()
	{
		$chunker = new Chunker\Text('foo');
		
		$chunker->setSize(1);
		$chunker->setIndex(1);
		
		$stream = new Stream($chunker);
		
		$this->assertEquals('f', $stream->getPreviousCharacter());
		
		return;
	}
	
	
	/* !hasCharacters() */
	
	/**
	 * hasCharacters() should return false if the file is empty
	 */
	public function testHasCharacters_returnsFalse_ifFileIsEmpty()
	{
		$chunker = new Chunker\Text();
		
		$stream = new Stream($chunker);
		
		$this->assertFalse($stream->hasCharacters());
		
		return;
	}
	
	/**
	 * hasCharacters() should return true if stream is before last character
	 */
	public function testHasCharacters_returnsTrue_ifBeforeLastCharacter()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$this->assertTrue($stream->hasCharacters());
		
		return;
	}
	
	/**
	 * hasCharacters() should return true if stream is on last character
	 */
	public function testHasCharacters_returnsTrue_ifOnLastCharacter()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$stream->next();
		$stream->next();
		
		$this->assertTrue($stream->hasCharacters());
		
		return;
	}
	
	/**
	 * hasCharacters() should return false if stream is after last character
	 */
	public function testHasCharacters_returnsFalse_ifAfterLastCharacter()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$stream->next();
		$stream->next();
		$stream->next();
		
		$this->assertFalse($stream->hasCharacters());
		
		return;
	}
	
	
	/* !reset() */
	
	/**
	 * reset() should reset the internal chunk pointer
	 */
	public function testReset()
	{
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$stream->next();  // returns "o"
		$stream->next();  // returns "o"
		
		$this->assertEquals('o', $stream->current());
		
		$stream->reset();
		
		$this->assertEquals('f', $stream->current());
		
		return;
	}
	
	
	/* !setCharacters() */
	
	/**
	 * setCharacters() should set the characters array and return self
	 */
	public function testSetCharacters_returnsSelf()
	{
		$characters = ['f', 'o', 'o'];
		
		$chunker = new Chunker\Text('foo');
		
		$stream = new Stream($chunker);
		
		$this->assertSame($stream, $stream->setCharacters($characters));
		$this->assertSame($characters, $stream->getCharacters());	
		
		return;
	}
}
