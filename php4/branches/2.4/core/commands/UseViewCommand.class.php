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

class UseViewCommand //implements Command
{
  var $template_path;

  function UseViewCommand($template_path)
  {
    $this->template_path = $template_path;
  }

  function perform()
  {
    $handle = new Handle(WACT_ROOT . '/template/template.inc.php|Template',
                         array($this->template_path));

    $toolkit =& Limb :: toolkit();
    $toolkit->setView($handle);

    return LIMB_STATUS_OK;
  }
}

?>
