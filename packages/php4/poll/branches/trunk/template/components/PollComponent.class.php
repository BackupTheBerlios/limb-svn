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
  var $path = '';

  var $_poll_container = null;

  function canVote()
  {
    return $this->_poll_container->canVote();
  }

  function prepare()
  {
    $toolkit =& Limb :: toolkit();
    $this->_poll_container =& $toolkit->createSiteObject('PollContainer');

    $this->import($this->_poll_container->getActivePoll());
  }

  function pollExists()
  {
    return sizeof($this->_poll_container->getActivePoll());
  }


}

?>