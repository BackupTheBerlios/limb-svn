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


class poll_component extends component
{
	var $path = '';
	
	var $_poll_container = null;
			
	function can_vote()
	{
		return $this->_poll_container->can_vote();
	}
		
	function prepare()
	{	
		$this->_poll_container =& site_object_factory :: create('poll_container');
		
		$this->import($this->_poll_container->get_active_poll());
	}

	function poll_exists()
	{
		return sizeof($this->_poll_container->get_active_poll());
	}


} 

?>