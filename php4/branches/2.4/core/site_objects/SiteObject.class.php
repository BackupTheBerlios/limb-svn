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
require_once(LIMB_DIR . '/core/DomainObject.class.php');

class SiteObject extends DomainObject
{
  var $behaviour;

  function & getBehaviour()
  {
    return $this->behaviour;
  }

  function attachBehaviour(&$behaviour)
  {
    return $this->behaviour =& $behaviour;
  }

  function getParentNodeId()
  {
    return (int)$this->get('parent_node_id');
  }

  function setParentNodeId($parent_node_id)
  {
    $this->set('parent_node_id', (int)$parent_node_id);
  }

  function getNodeId()
  {
    return (int)$this->get('node_id');
  }

  function setNodeId($node_id)
  {
    $this->set('node_id', (int)$node_id);
  }

  function getIdentifier()
  {
    return $this->get('identifier');
  }

  function setIdentifier($identifier)
  {
    $this->set('identifier', $identifier);
  }

  function getTitle()
  {
    return $this->get('title', '');
  }

  function setTitle($title)
  {
    $this->set('title', $title);
  }

  function setVersion($version)
  {
    $this->set('version', $version);
  }

  function getVersion()
  {
    return (int)$this->get('version');
  }

  function getLocaleId()
  {
    return $this->get('locale_id');
  }

  function setLocaleId($locale_id)
  {
    $this->set('locale_id', $locale_id);
  }

  function getCreatorId()
  {
    return (int)$this->get('creator_id');
  }

  function setCreatorId($creator_id)
  {
    $this->set('creator_id', (int)$creator_id);
  }

  function getModifiedDate()
  {
    return (int)$this->get('modified_date');
  }

  function setModifiedDate($modified_date)
  {
    $this->set('modified_date', (int)$modified_date);
  }

  function getCreatedDate()
  {
    return (int)$this->get('created_date');
  }

  function setCreatedDate($created_date)
  {
    $this->set('created_date', (int)$created_date);
  }

  function getClassId()
  {
    return (int)$this->get('class_id');
  }

  function setClassId($class_id)
  {
    $this->set('class_id', (int)$class_id);
  }

  function & getController()
  {
    include_once(LIMB_DIR . '/core/site_objects/SiteObjectController.class.php');
    return new SiteObjectController($this->getBehaviour());
  }

}

?>
