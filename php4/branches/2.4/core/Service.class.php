<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Service.class.php 1085 2005-02-02 16:04:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/Object.class.php');

class Service extends Object
{
  var $behaviour;

  function getServiceId()
  {
    return (int)$this->get('service_id');
  }

  function setServiceId($service_id)
  {
    $this->set('service_id', (int)$service_id);
  }

  function & getBehaviour()
  {
    return $this->behaviour;
  }

  function attachBehaviour(&$behaviour)
  {
    return $this->behaviour =& $behaviour;
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
    return new ServiceController($this->getBehaviour());
  }

}

?>
