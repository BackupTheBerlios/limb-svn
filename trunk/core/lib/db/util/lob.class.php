<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

/**
* An abstract class for handling LOB (Locator Object) columns.
* 
*/
class lob
{
	/**
	* The contents of the lob.
	* DO NOT SET DIRECTLY (or you will disrupt the
	* ability of is_modified() to give accurate results).
	* 
	* @var string 
	*/
	var $data;

	/**
	* File that blob should be written out to.
	* 
	* @var string 
	*/
	var $out_file;

	/**
	* File that blob should be read in from
	* 
	* @var string 
	*/
	var $in_file;

	/**
	* This is a 3-state value indicating whether column has been
	* modified.
	* Initially it is NULL.  Once first call to set_contents() is made
	* it is FALSE, because this will be initial state of lob.  Once
	* a subsequent call to set_contents() is made it is TRUE.
	* 
	* @var boolean 
	*/
	var $modified = null;

	/**
	* Construct a new lob.
	* 
	* @param sttring $data The data contents of the lob.
	* @see set_contents
	*/
	// public function __construct($data = null)
	function lob($data = null)
	{
		if ($data !== null)
		{
			$this->set_contents($data);
		} 
	} 

	/**
	* Get the contents of the LOB.
	* 
	* @return string The characters in this LOB.
	* @throws exception
	*/
	function get_contents()
	{
		if ($this->data === null && $this->is_from_file())
		{
			$this->read_from_file();
		} 
		return $this->data;
	} 

	/**
	* Set the contents of this LOB.
	* Sets the modified flag to FALSE if this is the first call
	* to set_contents() for this object.  Sets the bit to TRUE if
	* this any subsequent call to set_contents().
	* 
	* @param string $bytes 
	*/
	function set_contents($data)
	{
		$this->data = $data;

		if ($this->modified === null)
		{ 
			// if modified bit hasn't been set yet,
			// then it should now be set to FALSE, since
			// we just did inital population
			$this->modified = false;
		} elseif ($this->modified === false)
		{ 
			// if it was already FALSE, then it should
			// now be set to TRUE, since this is a subsequent
			// modfiication.
			$this->modified = true;
		} 
	} 

	/**
	* Dump the contents of the file to stdout.
	* Must be implemented by subclasses so that binary status is handled
	* correctly. (i.e. ignored for Clob, handled for Blob)
	* 
	* @return void 
	* @throws exception if no file or contents.
	*/
	function dump()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Specify the file that we want this LOB read from.
	* 
	* @param string $file_path The location of the file.
	* @return void 
	*/
	function set_input_file($file_path)
	{
		$this->in_file = $file_path;
	} 

	/**
	* Get the file that we want this LOB read from.
	* 
	* @return string The location of the file.
	*/
	function &get_input_file()
	{
		return $this->in_file;
	} 

	/**
	* Specify the file that we want this LOB saved to.
	* 
	* @param string $file_path The location of the file.
	* @return void 
	*/
	function set_output_file($file_path)
	{
		$this->out_file = $file_path;
	} 

	/**
	* Get the file that we want this LOB saved to.
	* 
	* @return string $file_path The location of the file.
	*/
	function &get_output_file()
	{
		return $this->out_file;
	} 

	/**
	* Returns whether this lob is loaded from file.
	* This is useful for bypassing need to read in the contents of the lob.
	* 
	* @return boolean Whether this LOB is to be read from a file.
	*/
	function is_from_file()
	{
		return ($this->in_file !== null);
	} 

	/**
	* Read LOB data from file (binary safe).
	* (Implementation may need to be moved into Clob / Blob subclasses, but
	* since file_get_contents() is binary-safe, it hasn't been necessary so far.)
	* 
	* @param string $file Filename may also be specified here (if not specified using set_input_file()).
	* @return void 
	* @throws exception - if no file specified or error on read.
	* @see set_input_file
	*/
	function &read_from_file($file = null)
	{
		if ($file !== null)
		{
			$this->set_input_file($file);
		} 
		if (!$this->in_file)
		{
			return new exception(DB_ERROR, 'No file specified for read.');
		} 
		$data = @file_get_contents($this->in_file);
		if ($data === false)
		{
			return new exception(DB_ERROR, 'Unable to read from file: ' . $this->in_file);
		} 
		$this->set_contents($data);
	} 

	/**
	* Write LOB data to file (binary safe).
	* (Impl may need to move into subclasses, but so far not necessary.)
	* 
	* @param string $file Filename may also be specified here (if not set using set_output_file()).
	* @throws exception - if no file specified, no contents to write, or error on write.
	* @see set_output_file
	*/
	function write_to_file($file = null)
	{
		if ($file !== null)
		{
			$this->set_output_file($file);
		} 
		if (!$this->out_file)
		{
			return new exception(DB_ERROR, 'No file specified for write');
		} 
		if ($this->data === null)
		{
			return new exception(DB_ERROR, 'No data to write to file');
		} 
		if (false === @file_put_contents($this->out_file, $this->data))
		{
			return new exception(DB_ERROR, 'Unable to write to file: ' . $this->out_file);
		} 
	} 

	/**
	* Set whether LOB contents have been modified after initial setting.
	* 
	* @param boolean $b 
	*/
	function set_modified($b)
	{
		$this->modified = $b;
	} 

	/**
	* Whether LOB contents have been modified after initial setting.
	* 
	* @return boolean TRUE if the contents have been modified after initial setting.
	*                   FALSE if contents have not been modified or if no contents have bene set.
	*/
	function is_modified()
	{ 
		// cast it so that NULL will also eval to false
		return (boolean) $this->modified;
	} 
} 
