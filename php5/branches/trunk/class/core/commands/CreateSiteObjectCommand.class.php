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
  protected $behaviour_name;

  function __construct($behaviour_name)
  {
    $this->behaviour_name = $behaviour_name;
  }

  public function perform()
  {
    $object = Limb :: toolkit()->createSiteObject($this->_defineSiteObjectClassName());

    $this->_fillObject($object);

    try
    {
      $this->_createObjectOperation($object);
    }
    catch(LimbException $e)
    {
      return Limb :: STATUS_ERROR;
    }

    return Limb :: STATUS_OK;
  }

  protected function _createObjectOperation($object)
  {
    $object->create();

    Limb :: toolkit()->getDataspace()->set('created_site_object', $object);
  }

  protected function _fillObject($object)
  {
    $dataspace = Limb :: toolkit()->getDataspace();

    $object->merge($dataspace->export());

    $object->setBehaviourId($this->_getBehaviourId());

    if (!$dataspace->get('parent_node_id'))
    {
      $parent_object_data = $this->_loadParentObjectData();
      $object->set('parent_node_id', $parent_object_data['node_id']);
    }
  }

  protected function _getBehaviourId()
  {
    $behaviour = Limb :: toolkit()->createBehaviour($this->behaviour_name);
    return $behaviour->getId();
  }

  protected function _loadParentObjectData()
  {
    $toolkit = Limb :: toolkit();
    $datasource = $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($toolkit->getRequest());
    return $datasource->fetch();
  }

  protected function _defineSiteObjectClassName()
  {
    return 'site_object';
  }

}

?>
