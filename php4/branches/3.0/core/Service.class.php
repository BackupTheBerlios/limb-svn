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
require_once(LIMB_DIR . '/core/Object.class.php');

class Service extends Object
{
  var $service;

  function getServiceId()
  {
    return (int)$this->get('service_id');
  }

  function setServiceId($service_id)
  {
    $this->set('service_id', (int)$service_id);
  }

  function & getService()
  {
    return $this->service;
  }

  function attachService(&$service)
  {
    return $this->service =& $service;
  }

  function getTitle()
  {
    return $this->get('title', '');
  }

  function setTitle($title)
  {
    $this->set('title', $title);
  }

  function & getController()
  {
    include_once(LIMB_DIR . '/core/ServiceController.class.php');
    return new ServiceController($this->getService());
  }

}

?>
