<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/command.interface.php');

class use_view_command implements Command
{
  protected $template_path;
  
  function __construct($template_path)
  {
    $this->template_path = $template_path;
  }
  
  public function perform()
  {
    $handle = array(LIMB_DIR . '/class/template/template', $this->template_path);
    
    Limb :: toolkit()->setView($handle);
    
    return Limb :: STATUS_OK;
  }
}

?> 
