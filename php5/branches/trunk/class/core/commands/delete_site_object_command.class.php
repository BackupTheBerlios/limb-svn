<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class delete_site_object_command implements Command
{
  protected function _get_object_to_delete()
  {
    $toolkit = Limb :: toolkit();
    return wrap_with_site_object($toolkit->getFetcher()->fetch_requested_object($toolkit->getRequest()));
  }
  
	public function perform()
	{
		$object = $this->_get_object_to_delete();

		try
		{
		  $object->delete();
		}
		catch (SQLException $sql_e)
		{
		  throw $sql_e;
		}
		catch(LimbException $e)
		{
			return Limb :: STATUS_ERROR;
		}

		return Limb :: STATUS_OK;
  }
}

?>
