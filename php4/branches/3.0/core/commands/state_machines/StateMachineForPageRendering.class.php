<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FormProcessingCommand.class.php 1215 2005-04-12 14:35:01Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/StateMachineCommand.class.php');

class StateMachineForFormProcessing extends StateMachineCommand
{
  function StateMachineForFormProcessing(&$factory)
  {
    parent :: StateMachineCommand($factory);

    $this->registerState('Init',
                          array(LIMB_STATUS_OK => 'Render'));

    $this->registerState('Render');
  }
}
?>
