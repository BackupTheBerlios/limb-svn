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

require_once(LIMB_DIR . '/core/lib/db/util/lob.class.php');

/**
* A class for handling character (ASCII) LOBs.
* 
*/
class clob extends lob
{
	/**
	* Dump the contents of the file using fpassthru().
	* 
	* @return void 
	* @throws exception if no file or contents.
	*/
	function dump()
	{
		if (!$this->data)
		{ 
			// is there a file name set?
			if ($this->in_file)
			{
				$fp = @fopen($this->in_file, "r");
				if (!$fp)
				{
					return new exception(DB_ERROR, 'Unable to open file: ' . $this->in_file);
				} 
				fpassthru($fp);
				@fclose($fp);
			} 
			else
			{
				return new exception(DB_ERROR, 'No data to dump');
			} 
		} 
		else
		{
			echo $this->data;
		} 
	} 
} 
