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
require_once(LIMB_DIR . '/class/core/commands/Command.interface.php');

class EditSiteObjectCommand implements Command
{
  public function perform()
  {
    $object = Limb :: toolkit()->createSiteObject($this->_defineSiteObjectClassName());

    $this->_fillObject($object);

    try
    {
      $this->_updateObjectOperation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: STATUS_OK;
  }

  protected function _updateObjectOperation($object)
  {
    $object->update($this->_defineIncreaseVersionFlag($object));
  }

  protected function _fillObject($object)
  {
    $dataspace = Limb :: toolkit()->getDataspace();

    $object->import($this->_loadObjectData());

    $object->merge($dataspace->export());
  }

  protected function _loadObjectData()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());

    return $datasource->fetch();
  }

  protected function _defineIncreaseVersionFlag($object)
  {
    if (class_exists('ContentObject') &&  ($object instanceof ContentObject))
      return true;
    else
      return false;
  }

  protected function _defineSiteObjectClassName()
  {
    return 'site_object';
  }

}

?>
