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
class poll_component extends component
{
  public $path = '';

  protected $_poll_container = null;

  public function can_vote()
  {
    return $this->_poll_container->can_vote();
  }

  public function prepare()
  {
    $this->_poll_container = Limb :: toolkit()->createSiteObject('poll_container');

    $this->import($this->_poll_container->get_active_poll());
  }

  public function poll_exists()
  {
    return sizeof($this->_poll_container->get_active_poll());
  }


}

?>