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
require_once(LIMB_DIR . '/class/core/DomainObject.class.php');

class SiteObject extends DomainObject
{
  protected $behaviour;

  public function getBehaviour()
  {
    return $this->behaviour;
  }

  public function attachBehaviour($behaviour)
  {
    return $this->behaviour = $behaviour;
  }

  public function getParentNodeId()
  {
    return (int)$this->get('parent_node_id');
  }

  public function setParentNodeId($parent_node_id)
  {
    $this->set('parent_node_id', (int)$parent_node_id);
  }

  public function getNodeId()
  {
    return (int)$this->get('node_id');
  }

  public function setNodeId($node_id)
  {
    $this->set('node_id', (int)$node_id);
  }

  public function getIdentifier()
  {
    return $this->get('identifier');
  }

  public function setIdentifier($identifier)
  {
    $this->set('identifier', $identifier);
  }

  public function getTitle()
  {
    return $this->get('title', '');
  }

  public function setTitle($title)
  {
    $this->set('title', $title);
  }

  public function setVersion($version)
  {
    $this->set('version', $version);
  }

  public function getVersion()
  {
    return (int)$this->get('version');
  }

  public function getLocaleId()
  {
    return $this->get('locale_id');
  }

  public function setLocaleId($locale_id)
  {
    $this->set('locale_id', $locale_id);
  }

  public function getCreatorId()
  {
    return (int)$this->get('creator_id');
  }

  public function setCreatorId($creator_id)
  {
    $this->set('creator_id', (int)$creator_id);
  }

  public function getModifiedDate()
  {
    return (int)$this->get('modified_date');
  }

  public function setModifiedDate($modified_date)
  {
    $this->set('modified_date', (int)$modified_date);
  }

  public function getCreatedDate()
  {
    return (int)$this->get('created_date');
  }

  public function setCreatedDate($created_date)
  {
    $this->set('created_date', (int)$created_date);
  }

  public function getStatus()//???
  {
    return (int)$this->get('status', 0);
  }

  public function setStatus($status)
  {
    $this->set('status', (int)$status);
  }

  public function getController()
  {
    include_once(LIMB_DIR . '/class/core/site_objects/SiteObjectController.class.php');
    return new SiteObjectController($this->getBehaviour());
  }

}

?>
