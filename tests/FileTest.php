<?php
/**
 * The FileTest class file
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2015 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Stream;

/**
 * A test suite for the File class
 *
 * @since  0.1.0
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	/* !Protected properties */
	
	/**
	 * @var  string  the name of a file that does not exst
	 */
	protected $fileDoesNotExist;
	
	/**
	 * @var  string  the name of a file that does exist but is empty
	 */
	protected $fileIsEmpty;
	
	/**
	 * @var  string  the name of a file that exists and is not empty
	 */
	protected $fileIsNotEmpty;
	

	/* !Magic methods */
	
	/**
	 * Called before each test
	 *
	 * I'll (drop) create a fresh test file.
	 *
	 * @return  void
	 */
	public function setUp()
	{	
		$this->fileDoesNotExist = dirname(__FILE__).DIRECTORY_SEPARATOR.'foo.txt';
		$this->fileIsEmpty      = dirname(__FILE__).DIRECTORY_SEPARATOR.'bar.txt';
		$this->fileIsNotEmpty   = dirname(__FILE__).DIRECTORY_SEPARATOR.'baz.txt';
		
		// delete the empty file if it exists
		if (is_file($this->fileIsEmpty)) {
			unlink($this->fileIsEmpty);	
		}
		
		// create a new empty file
		file_put_contents($this->fileIsEmpty, null);
		
		// delete the not empty file if it exists
		if (is_file($this->fileIsNotEmpty)) {
			unlink($this->fileIsNotEmpty);
		}
		
		// create a new not empty file
		file_put_contents($this->fileIsNotEmpty, 'foo bar baz');
		
		return;
	}
	
	/**
	 * Called after each test
	 *
	 * I'll delete the test files.
	 *
	 * @return  void
	 */
	public function tearDown()
	{
		// delete the empty file if it exists
		if (is_file($this->fileIsEmpty)) {
			unlink($this->fileIsEmpty);	
		}
		
		// delete the not empty file if it exists
		if (is_file($this->fileIsNotEmpty)) {
			unlink($this->fileIsNotEmpty);
		}

		return;
	}
	
	
	/* !setChunkIndex() */
	
	/**
	 * setChunkIndex() throws InvalidArgumentException if $index is not an integer
	 */
	public function testSetChunkIndex_throwsInvalidArgumentException_ifIndexIsNotAnInteger()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new File();
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
		
		$stream = new File();
		$stream->setChunkIndex(-1);
		
		return;
	}
	
	/**
	 * setChunkIndex() should return void if $index is a positive integer or zero
	 */
	public function testSetChunkIndex_returnsSelf_ifIndexIsPositiveIntegerOrZero()
	{
		$stream = new File();
		
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
		
		$stream = new File();
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
		
		$stream = new File();
		$stream->setChunkSize(-1);
		
		return;
	}
	
	/**
	 * setChunkSize() should return self if $chunkSize is a positive integer
	 */
	public function testSetChunkSize_returnsSelf_ifSizeIsAPositiveInteger()
	{
		$stream = new File();
		
		$this->assertSame($stream, $stream->setChunkSize(100));
		
		return;
	}
	
	
	/* !setName() */
	
	/**
	 * setName() should throw an InvalidArgumentException if $name is not a string
	 */
	public function testSetName_throwsInvalidArgumentException_ifNameIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new File();
		$stream->setName(123);
		
		return;
	}
	
	/**
	 * setName() should throw an InvalidArgumentException if $name is not readable
	 */
	public function testSetName_throwsInvalidArgumentException_ifNameIsNotReadable()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new File();
		$stream->setName('foo');
		
		return;
	}
	
	/**
	 * setName() should return self if $name is readable
	 */
	public function testSetName_returnsSelf_ifNameIsReadable()
	{
		$stream = new File();
		
		$this->assertSame($stream, $stream->setName($this->fileIsEmpty));
		
		return;
	}
	
	
	/* !getChunkIndex() */
	
	/**
	 * getChunkIndex() should return the chunk's index
	 */
	public function testSetChunkIndex_returnsIndex()
	{
		$stream = new File();
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
		$stream = new File();
		$stream->setChunkSize(1);
		
		$this->assertEquals(1, $stream->getChunkSize());
		
		return;
	}
	
	
	/* !getName() */
	
	/**
	 * getName() should return the file's name
	 */
	public function testSetName_returnsName()
	{
		$stream = new File();
		$stream->setName($this->fileIsEmpty);
		
		$this->assertEquals($this->fileIsEmpty, $stream->getName());
		
		return;
	}
	
	
	/* !__construct() */

	/**
	 * __construct() should throw an InvalidArgumentException if $name is not a string
	 */
	public function testConstruct_throwsInvalidArgumentException_ifNameIsNotAString()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new File(1);
		
		return;
	}
	
	/**
	 * __construct() should throw an InvalidArgumentException if $name is not readable
	 */
	public function testConstruct_throwsInvalidArgumentException_ifNameIsNotReadable()
	{
		$this->setExpectedException('InvalidArgumentException');
		
		$stream = new File('foo');
		
		return;
	}
	
	/**
	 * __construct() should return object if $name is readable
	 */
	public function testConstruct_returnsObject_ifNameIsReadable()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertEquals($this->fileIsEmpty, $stream->getName());
		
		return;
	}
	
	/**
	 * __construct() should return object if $name is empty
	 */
	public function testConstruct_returnsObject_ifNameIsEmpty()
	{
		$stream = new File();
		
		$this->assertTrue($stream instanceof File);
		$this->assertNull($stream->getName());
		
		return;
	}
	
	
	/* !getCurrentCharacter() */

	/**
	 * getCurrentCharacter() should throw an BadMethodCallException if $name is not set
	 */
	public function testGetCurrentCharacter_throwsBadMethodCallException_ifNameIsNotSet()
	{
		$this->setExpectedException('BadMethodCallException');
		
		$stream = new File();
		
		$stream->getCurrentCharacter();
		
		return;
	}

	/**
	 * getCurrentCharacter() should return false if file is empty
	 */
	public function testGetCurrentCharacter_returnsFalse_ifFileIsEmpty()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertFalse($stream->getCurrentCharacter());
		
		return;
	}
	
	/**
	 * getCurrentCharacter() should return string if file is not empty
	 */
	public function testGetCurrentCharacter_returnsString_ifFileIsNotEmpty()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$this->assertEquals('f', $stream->getCurrentCharacter());
		
		return;
	}
	

	/* !getNextCharacter() */
	
	/**
	 * getNextCharacter() should throw a BadMethodCallException if name is not set
	 */
	public function testGetNextCharacter_throwsBadMethodCallException_ifNameIsNotSet()
	{
		$this->setExpectedException('BadMethodCallException');
		
		$stream = new File();
		
		$stream->getNextCharacter();
		
		return;
	}
	
	/**
	 * getNextCharacter() should return false if the file is empty
	 */
	public function testGetNextCharacter_returnsFalse_ifFileIsEmpty()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertFalse($stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return false if a next character does not exist
	 */
	public function testGetNextCharacter_returnsFalse_ifNextDoesNotExist()
	{
		$stream = new File($this->fileIsEmpty);
		
		// fake the characters array; position the pointer at the last element
		$characters = str_split('foo bar baz');
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
		$stream = new File($this->fileIsNotEmpty);
		
		$this->assertEquals('o', $stream->getNextCharacter());
		
		return;
	}
	
	/**
	 * getNextCharacter() should return string if the next character exists in the
	 *     next chunk
	 */
	public function testGetNextCharacter_returnsString_ifNextDoesNotExistInCurrentChunk()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->setChunkSize(1);
		
		$expected = 'o';
		$actual   = $stream->getNextCharacter();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !getPreviousCharacter() */
	
	/**
	 * getPreviousCharacter() should throw a BadMethodCallException if name is not set
	 */
	public function testGetPreviousCharacter_throwsBadMethodCallException_ifNameIsNotSet()
	{
		$this->setExpectedException('BadMethodCallException');
		
		$stream = new File();
		
		$stream->getPreviousCharacter();
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return false if the file is empty
	 */
	public function testGetPreviousCharacter_returnsFalse_ifFileIsEmpty()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return false if a previous character does not exist
	 */
	public function testGetPreviousCharacter_returnsFalse_ifPreviousDoesNotExist()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$this->assertFalse($stream->getPreviousCharacter());
		
		return;
	}
	
	/**
	 * getPreviousCharacter() should return string if a previous character exists in the
	 *     current chunk
	 */
	public function testGetPreviousCharacter_returnsString_ifPreviousDoesExistInCurrentChunk()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$characters = str_split('foo bar baz');
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
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->setChunkSize(1);
		$stream->setChunkIndex(1);
		
		$expected = 'f';
		$actual   = $stream->getPreviousCharacter();
		
		$this->assertEquals($expected, $actual);
		
		return;
	}
	
	
	/* !isBeginning() */
	
	/**
	 * isBeginning() should return true if file is empty
	 */
	public function testIsBeginning_returnsTrue_ifFileIsEmpty()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertTrue($stream->isBeginning());
		
		return;
	}
	
	/**
	 * isBeginning() should return true if before first character
	 */
	public function testIsBeginning_returnsTrue_ifBeforeFirstCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$this->assertTrue($stream->isBeginning());
		
		return;
	}
	
	/**
	 * isBeginning() should return true if on first character
	 */
	public function testIsBeginning_returnsTrue_ifOnFirstCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->current();
		
		$this->assertTrue($stream->isBeginning());
		
		return;
	}
	
	/**
	 * isBeginning() should return false if after first character
	 */
	public function testIsBeginning_returnsFalse_ifAfterFirstCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->next();
		
		$this->assertFalse($stream->isBeginning());
		
		return;
	}
	
	
	/* !isEnd() */
	
	/**
	 * isEnd() should throw a BadMethodCallException if $name is not set
	 */
	public function testIsEnd_throwsBadMethodCallException_ifNameIsNotSet()
	{
		$this->setExpectedException('BadMethodCallException');
		
		$stream = new File();
		
		$stream->isEnd();
		
		return;
	}
	
	/**
	 * isEnd() should return true if the file is empty
	 */
	public function testIsEnd_returnsTrue_ifFileIsEmpty()
	{
		$stream = new File($this->fileIsEmpty);
		
		$this->assertTrue($stream->isEnd());
		
		return;
	}
	
	/**
	 * isEnd() should return false if before last character
	 */
	public function testIsEnd_returnsFalse_ifBeforeLastCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->next();
		
		$this->assertFalse($stream->isEnd());
		
		return;
	}
	
	/**
	 * isEnd() should return false if on last character
	 */
	public function testIsEnd_returnsTrue_ifOnLastCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$characters = str_split('foo bar baz');
		end($characters);
		
		$stream->setCharacters($characters);
		
		$this->assertFalse($stream->isEnd());
		
		return;
	}
	
	/**
	 * isEnd() should return true if after last character
	 */
	public function testIsEnd_returnsTrue_ifAfterLastCharacter()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$characters = str_split('foo bar baz');
		end($characters);
		
		$stream->setCharacters($characters);
		$stream->next();
		
		$this->assertTrue($stream->isEnd());
		
		return;
	}
	
	
	/* !reset() */
	
	/**
	 * reset() should reset the internal chunk pointer
	 */
	public function testReset()
	{
		$stream = new File($this->fileIsNotEmpty);
		
		$stream->getNextCharacter();  // returns "o"
		$stream->getNextCharacter();  // returns "o"
		$stream->getNextCharacter();  // returns " "
		
		$this->assertEquals(' ', $stream->getCurrentCharacter());
		
		$stream->reset();
		
		$this->assertEquals('f', $stream->getCurrentCharacter());
		
		return;
	}
	
}
