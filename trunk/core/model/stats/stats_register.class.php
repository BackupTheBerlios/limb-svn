/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class stats_register
{
	var $db = null;
	var $_reg_date;
	
	function stats_register()
	{
		$this->db =& db_factory :: instance();
		$this->reg_date = new date();		
	}

	function set_register_time($stamp)
	{
		$this->reg_date->set_by_stamp($stamp);
	}

	function get_register_time_stamp()
	{
		return $this->reg_date->get_stamp();
	}
}

?>