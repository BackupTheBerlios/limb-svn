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
require_once(LIMB_DIR . '/core/commands/state_machines/StateMachineForPageRendering.class.php');

class PageRenderingCommand
{
  var $template_name;

  function PageRenderingCommand($template_name)
  {
    $this->template_name = $template_name;
  }

  function perform()
  {
    $state_machine = new StateMachineForPageRendering($this);
    return $state_machine->perform();
  }

  function performInitial()
  {
    include_once(LIMB_DIR . '/core/commands/UseViewCommand.class.php');
    $command = new UseViewCommand($this->template_name);
    return $command->perform();
  }

  function performRender()
  {
    include_once(LIMB_DIR . '/core/commands/DisplayViewCommand.class.php');
    $command = new DisplayViewCommand();
    return $command->perform();
  }
}

?>
