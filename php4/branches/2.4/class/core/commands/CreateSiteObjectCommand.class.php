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

class CreateSiteObjectCommand implements Command
{
  var $behaviour_name;

  function CreateSiteObjectCommand($behaviour_name)
  {
    $this->behaviour_name = $behaviour_name;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject($this->_defineSiteObjectClassName());

    $this->_fillObject($object);

    try
    {
      $this->_createObjectOperation($object);
    }
    catch(LimbException $e)
    {
      return LIMB_STATUS_ERROR;
    }

    return Limb :: getSTATUS_OK();
  }

  function _createObjectOperation($object)
  {
    $object->create();

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('created_site_object', $object);
  }

  function _fillObject($object)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $object->merge($dataspace->export());

    $object->setBehaviourId($this->_getBehaviourId());

    if (!$dataspace->get('parent_node_id'))
    {
      $parent_object_data = $this->_loadParentObjectData();
      $object->set('parent_node_id', $parent_object_data['node_id']);
    }
  }

  function _getBehaviourId()
  {
    $toolkit =& Limb :: toolkit();
    $behaviour =& $toolkit->createBehaviour($this->behaviour_name);

    return $behaviour->getId();
  }

  function _loadParentObjectData()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());
    return $datasource->fetch();
  }

  function _defineSiteObjectClassName()
  {
    return 'SiteObject';
  }

}

?>
