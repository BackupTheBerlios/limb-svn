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

class EditSiteObjectCommand// implements Command
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject($this->_defineSiteObjectClassName());

    $this->_fillObject($object);

    $this->_updateObjectOperation($object);

    if(catch('LimbException', $e))
      return LIMB_STATUS_ERROR;

    return LIMB_STATUS_OK;
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
    $dao = $toolkit->createDAO('RequestedObjectDAO');
    $dao->setRequest($toolkit->getRequest());

    return $dao->fetch();
  }

  function _defineIncreaseVersionFlag($object)
  {
    if (is_a($object, 'ContentObject'))
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
