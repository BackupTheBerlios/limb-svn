<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: richedit_component.class.php 628 2004-09-09 14:02:25Z dbrain $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/template/components/form/richedit/richedit_component.class.php');
@define('FCKeditor_BasePath', '/FCKeditor/');

class fckeditor_component extends richedit_component
{
  function render_contents()
  {
    if(!defined('FCKeditor_DIR'))
    {
      echo 'Constant FCKeditor_DIR is not defined<br/>';
      parent :: render_contents();
    }
    else
    {
      $this->render_editor();
    }
  }

  function render_editor()
  {
    include_once(FCKeditor_DIR. 'fckeditor.php');
    
    $editor = new FCKeditor($this->_process_name_attribute($this->get_attribute('name'))) ;
    $editor->BasePath	= FCKeditor_BasePath;
    $editor->Value		= $this->get_value() ;
    $editor->Width = $this->get_attribute('width');
    $editor->Height = $this->get_attribute('height');

    $editor->Create() ;

  }
}

?>