<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainBehaviour.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');

class StateMachineForDeleteEntity extends StateMachineCommand
{
  function StateMachineForDeleteEntity(&$factory)
  {
    parent :: StateMachineCommand($factory);

    $this->registerState('InitEntity',
                          array(LIMB_STATUS_OK => 'DeleteEntity',
                                LIMB_STATUS_ERROR => 'NotFound',
                                ));

    $this->registerState('DeleteEntity',
                          array(LIMB_STATUS_OK => 'Redirect',
                                LIMB_STATUS_ERROR => 'Error'));

    $this->registerState('Error',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('Redirect');

    $this->registerState('NotFound',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('Render');
  }
}

?>