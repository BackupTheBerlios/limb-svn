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
class PollComponent extends Component
{
  public $path = '';

  protected $_poll_container = null;

  public function canVote()
  {
    return $this->_poll_container->canVote();
  }

  public function prepare()
  {
    $this->_poll_container = Limb :: toolkit()->createSiteObject('PollContainer');

    $this->import($this->_poll_container->getActivePoll());
  }

  public function pollExists()
  {
    return sizeof($this->_poll_container->getActivePoll());
  }


}

?>