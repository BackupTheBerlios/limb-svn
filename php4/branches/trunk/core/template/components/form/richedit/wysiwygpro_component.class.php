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

class wysiwygpro_component extends richedit_component
{
  function render_contents()
  {
    if(!defined('wysiwygPro_DIR'))
    {
      echo 'Constant wysiwygPro_DIR is not defined<br/>';
      parent :: render_contents();
    }
    else
    {
      $this->render_editor();
    }
  }

  function render_editor()
  {
    include_once(wysiwygPro_DIR. 'config.php');
    include_once(wysiwygPro_DIR. 'editor_class.php');

    $editor = new wysiwygPro();

    $editor->set_name($this->_process_name_attribute($this->get_attribute('name')));
    $editor->set_code($this->get_value());
    if(!$width = $this->get_attribute('width'))
      $width = RICHEDIT_DEFAULT_WIDTH;

    if(!$height = $this->get_attribute('height'))
      $height = RICHEDIT_DEFAULT_HEIGHT;

    if (($editor->is_ie55) || ($editor->is_ie50))
    {
      echo "<script type='text/javascript' src='". WP_WEB_DIRECTORY ."/js/LimbScriptIE.js'></script>";
      $editor->addbutton('Insert Image from Repository',
                         'after:image',
                         'open_limb_image_window(##name##, this)',
                         '##directory##/images/limb_image.gif', 22, 22, 'limb_image');
      $editor->addbutton('Insert File from Repository',
                         'after:link',
                         'open_limb_file_window(##name##, this)',
                         '##directory##/images/limb_file.gif', 22, 22, 'limb_file');

    }
    elseif ($editor->is_gecko)
    {
      echo "<script type='text/javascript' src='". WP_WEB_DIRECTORY ."/js/LimbScriptMozilla.js'></script>";
      $editor->addbutton('Insert Image from Repository',
                         'after:image',
                         'open_limb_image_window(##name##, this)',
                         '##directory##/images/limb_image.gif', 22, 22, 'limb_image');
      $editor->addbutton('Insert File from Repository',
                         'after:link',
                         'open_limb_file_window(##name##, this)',
                         '##directory##/images/limb_file.gif', 22, 22, 'limb_file');

    }

    $editor->set_stylesheet('/design/main/styles/main.css');
    $editor->usep(true);
    $editor->print_editor($width, $height);
  }
}

?>