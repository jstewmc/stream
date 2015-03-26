<?php
/**
 * The TextTest class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Stream;

/**
 * A test suite for the Text class
 *
 * @since  0.1.0
 */
class TextTest extends \PHPUnit_Framework_TestCase
{
	/* !setChunkIndex() */
	
	/**
	 * setChunkIndex() throws InvalidArgumentException if $index is not an integer
	 */
	public function testSetChunkIndex_throwsInvalidArgumentException_ifIndexIsNotAnInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new Text();
		$stream->setChunkIndex('foo');
		
		return;
	}
	
	/**
	 * setChunkIndex() should throw an InvalidArgumentException if $index is a negative
	 *     integer
	 */
	public function testSetChunkIndex_throwsInvalidArgumentException_ifIndexIsNegativeInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new Text();
		$stream->setChunkIndex(-1);
		
		return;
	}
	
	/**
	 * setChunkIndex() should return void if $index is a positive integer or zero
	 */
	public function testSetChunkIndex_returnsSelf_ifIndexIsPositiveIntegerOrZero()
	{
		$stream = new Text();
		
		$this->assertSame($stream, $stream->setChunkIndex(1));
		
		return;
	}
	
	
	/* !setChunkSize() */
	
	/**
	 * setChunkSize() should throw InvalidArgumentException if $chunkSize is not an
	 *     integer
	 */
	public function testSetChunkSize_throwsInvalidArgumentException_ifSizeIsNotAnInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new Text();
		$stream->setChunkSize('foo');
		
		return;
	}
	
	/**
	 * setChunkSize() should throw InvalidArgumentException if $chunkSize is not a
	 *     positive integer
	 */
	public function testSetChunkSize_throwsInvalidArgumentException_ifSizeIsNotAPositiveInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new Text();
		$stream->setChunkSize(-1);
		
		return;
	}
	
	/**
	 * setChunkSize() should return self if $chunkSize is a positive integer
	 */
	public function testSetChunkSize_returnsSelf_ifSizeIsAPositiveInteger()
	{
		$stream = new Text();
		
		$this->assertSame($stream, $stream->setChunkSize(1));
		
		return;
	}
	
	
	/* !getChunkIndex() */
	
	/**
	 * getChunkIndex() should return the chunk's index
	 */
	public function testSetChunkIndex_returnsIndex()
	{
		$stream = new Text();
		$stream->setChunkIndex(1);
		
		$this->assertEquals(1, $stream->getChunkIndex());
		
		return;
	}
	
	
	/* !getChunkSize() */
	
	/**
	 * getChunkSize() should return the chunk's size
	 */
	public function testSetChunkSize_returnsSize()
	{
		$stream = new Text();
		$stream->setChunkSize(1);
		
		$this->assertEquals(1, $stream->getChunkSize());
		
		return;
	}

	
	
	/* !__construct() */

	/**
	 * __construct() should throw an InvalidArgumentException if $text is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifTextIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new Text(1);
		
		return;
	}
	
	/**
	 * __construct() should return object if $text exists
	 */
	public function testConstruct_returnsObject_ifTextDoesExist()
	{
		$stream = new Text('foo');
		
		$this->assertEquals('foo', $stream->getText());
		
		return;
	}
	
	/**
	 * __construct() should return object if $text does not exist
	 */
	public function testConstruct_returnsObject_ifTextDoesNotExist()
	{
		$stream = new Text();
		
		$this->assertNull($stream->getText());
		
		return;
	}
	
	
	/* !getCurrentCharacter() */

	/**
	 * getCurrentCharacter() should return false if text is empty
	 */
	public function testGetCurrentCharacter_returnsFalse_ifTextIsEmpty()
	{
		$stream = new Text();
		
		$this->assertFalse($stream->getCurrentCharacter());
		
		return;
	}
	
	/**
	 * getCurrentCharacter() should return string if file is not empty
	 */
	public function testGetCurrentCharacter_returnsString_ifFileIsNotEmpty()
	{
		$stream = new Text('foo');
		
		$this->assertEquals('f', $stream->getCurrentCharacter());
		
		return;
	}
	
	/**
	 * getCurrentCharacter() should return string if character evaluates to empty
	 */
	public function testGetCharacter_returnsString_ifCharacterEvaluatesEmpty()
	{
		$stream = new Text('0');
		
		$this->assertEquals('0', $stream->getCurrentCharacter());
		
		return;
	}
	

	/* !getNextCharacter() */
	
	/**
	 * getNextCharacter() should return false if the text is empty
	 */
	public function testGetNextCharacter_returnsFalse_ifTextIsEmpty()
	{
		$stream = new Text();
		
		$this->assertFalse($stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return false if a next character does not exist
	 */
	public function testGetNextCharacter_returnsFalse_ifNextDoesNotExist()
	{
		$stream = new Text('foo');
		
		// fake the characters array; position the pointer at the last element
		$characters = str_split('foo');
		end($characters);
		
		$stream->setCharacters($characters);
		
		$this->assertFalse($stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return string if the next character exists in the
	 *     current chunk
	 */
	public function testGetNextCharacter_returnsString_ifNextDoesExistInCurrentChunk()
	{
		$stream = new Text('foo');
		
		$this->assertEquals('o', $stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return string if the next character exists in the
	 *     next chunk
	 */
	public function testGetNextCharacter_returnsString_ifNextDoesNotExistInCurrentChunk()
	{
		$stream = new Text('foo');
		
		$stream->setChunkSize(1);
		
		$expected = 'o';
		$actual   = $stream->getNextCharacter();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getPreviousCharacter() */
	
	/**
	 * getPreviousCharacter() should return false if the file is empty
	 */
	public function testGetPreviousCharacter_returnsFalse_ifTextIsEmpty()
	{
		$stream = new Text();
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return false if a previous character does not exist
	 */
	public function testGetPreviousCharacter_returnsFalse_ifPreviousDoesNotExist()
	{
		$stream = new Text('foo');
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return string if a previous character exists in the
	 *     current chunk
	 */
	public function testGetPreviousCharacter_returnsString_ifPreviousDoesExistInCurrentChunk()
	{
		$stream = new Text('foo');
		
		$characters = str_split('foo');
		next($characters);
		
		$stream->setCharacters($characters);
		
		$expected = 'f';
		$actual   = $stream->getPreviousCharacter();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return string if a previous character does not 
	 *     exist in the current chunk
	 */
	public function testGetPreviousCharacter_returnsString_ifPreviousDoesNotExistInCurrentChunk()
	{
		$stream = new Text('foo');
		
		$stream->setChunkSize(1);
		$stream->setChunkIndex(1);
		
		$expected = 'f';
		$actual   = $stream->getPreviousCharacter();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !hasCharacter() */
	
	/**
	 * hasCharacter() should return false if the file is empty
	 */
	public function testHasCharacter_returnsFalse_ifFileIsEmpty()
	{
		$stream = new Text();
		
		$this->assertFalse($stream->hasCharacter());
		
		return;
	}
	
	/**
	 * hasCharacter() should return true if stream is before last character
	 */
	public function testHasCharacter_returnsTrue_ifBeforeLastCharacter()
	{
		$stream = new Text('foo');
		
		$this->assertTrue($stream->hasCharacter());
		
		return;
	}
	
	/**
	 * hasCharacter() should return true if stream is on last character
	 */
	public function testHasCharacter_returnsTrue_ifOnLastCharacter()
	{
		$stream = new Text('foo');
		
		$characters = str_split('foo');
		end($characters);
		
		$stream->setCharacters($characters);
		
		$this->assertTrue($stream->hasCharacter());
		
		return;
	}
	
	/**
	 * hasCharacter() should return false if stream is after last character
	 */
	public function testHasCharacter_returnsFalse_ifAfterLastCharacter()
	{
		$stream = new Text('foo');
		
		$characters = str_split('foo');
		end($characters);
		
		$stream->setCharacters($characters);
		$stream->next();
		
		$this->assertFalse($stream->hasCharacter());
		
		return;
	}
	
	
	/* !reset() */
	
	/**
	 * reset() should reset the internal chunk pointer
	 */
	public function testReset()
	{
		$stream = new Text('foo');
		
		$stream->getNextCharacter();  // returns "o"
		$stream->getNextCharacter();  // returns "o"
		
		$this->assertEquals('o', $stream->getCurrentCharacter());
		
		$stream->reset();
		
		$this->assertEquals('f', $stream->getCurrentCharacter());
		
		return;
	}
	
}
