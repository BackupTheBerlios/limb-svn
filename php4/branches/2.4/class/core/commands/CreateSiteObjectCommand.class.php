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

class CreateSiteObjectCommand// implements Command
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

    $this->_createObjectOperation($object);

    if(catch('LimbException', $e))
      return LIMB_STATUS_ERROR;

    return LIMB_STATUS_OK;
  }

  function _createObjectOperation(&$object)
  {
    $object->create();

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('created_site_object', $object);
  }

  function _fillObject(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $object->merge($dataspace->export());

    $object->attachBehaviour($this->_getBehaviour());

    if (!$dataspace->get('parent_node_id'))
    {
      $parent_object_data = $this->_loadParentObjectData();
      $object->set('parent_node_id', $parent_object_data['node_id']);
    }
  }

  function & _getBehaviour()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createBehaviour($this->behaviour_name);
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
