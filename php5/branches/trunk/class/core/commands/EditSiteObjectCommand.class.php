<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class edit_site_object_command implements Command
{
  public function perform()
  {
    $object = Limb :: toolkit()->createSiteObject($this->_define_site_object_class_name());

    $this->_fill_object($object);

    try
    {
      $this->_update_object_operation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: STATUS_OK;
  }

  protected function _update_object_operation($object)
  {
    $object->update($this->_define_increase_version_flag($object));
  }

  protected function _fill_object($object)
  {
    $dataspace = Limb :: toolkit()->getDataspace();

    $object->import($this->_load_object_data());

    $object->merge($dataspace->export());
  }

  protected function _load_object_data()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('requested_object_datasource');
    $datasource->set_request($toolkit->getRequest());

    return $datasource->fetch();
  }

  protected function _define_increase_version_flag($object)
  {
    if (class_exists('content_object') && ($object instanceof content_object))
      return true;
    else
      return false;
  }

  protected function _define_site_object_class_name()
  {
    return 'site_object';
  }

}

?>
