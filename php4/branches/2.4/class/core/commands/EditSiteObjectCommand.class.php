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
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject($this->_defineSiteObjectClassName());

    $this->_fillObject($object);

    try
    {
      $this->_updateObjectOperation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: getSTATUS_OK();
  }

  function _updateObjectOperation($object)
  {
    $object->update($this->_defineIncreaseVersionFlag($object));
  }

  function _fillObject($object)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $object->import($this->_loadObjectData());

    $object->merge($dataspace->export());
  }

  function _loadObjectData()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());

    return $datasource->fetch();
  }

  function _defineIncreaseVersionFlag($object)
  {
    if (class_exists('ContentObject') &&  ($object instanceof ContentObject))
      return true;
    else
      return false;
  }

  function _defineSiteObjectClassName()
  {
    return 'site_object';
  }

}

?>
