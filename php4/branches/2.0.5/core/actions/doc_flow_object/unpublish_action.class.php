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
require_once(LIMB_DIR . 'core/actions/doc_flow_object/set_publish_status_action.class.php');

class unpublish_action extends set_publish_status_action
{
	function unpublish_action($name='')
	{
		parent :: set_publish_status_action($name);
	}
	
	function perform()
	{
		$this->set_publish_status(get_ini_option('doc_flow.ini', 'default', 'unpublished'));
		close_popup();
	}
	
}

?>