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

require_once(LIMB_DIR . '/core/lib/db/common/prepared_statement_common.class.php');

/**
* mysql subclass for prepared statements.
* 
*/
class mysql_prepared_statement extends prepared_statement_common
{
	/**
	* Quotes string using native mysql function.
	* 
	* @param string $str 
	* @return string 
	*/
	function escape($str)
	{
		return mysql_escape_string($str);
	} 
} 
