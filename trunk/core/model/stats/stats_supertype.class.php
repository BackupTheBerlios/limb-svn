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

require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class stats_supertype
{
	var $db = null;
	var $reg_date;

	function stats_supertype()
	{
		$this->db =& db_factory :: instance();
		$this->reg_date = new date();		
	}

	function set_register_time($stamp = null)
	{
		if(!$stamp)
			$stamp = time();
			
		$this->reg_date->set_by_stamp($stamp);
	}

	function get_register_time_stamp()
	{
		return $this->reg_date->get_stamp();
	}
}

?>