<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: richedit_component.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/components/form/container_form_element.class.php');

define('RICHEDIT_DEFAULT_WIDTH', '600px');
define('RICHEDIT_DEFAULT_HEIGHT', '400px');
define('RICHEDIT_DEFAULT_ROWS', '30');
define('RICHEDIT_DEFAULT_COLS', '60');

class richedit_component extends container_form_element
{
  function render_contents()
  {
    echo '<textarea';
    $this->render_attributes();
    echo '>';
    echo htmlspecialchars($this->get_value(), ENT_QUOTES);
    echo '</textarea>';
  }
  
  function init_richedit()
  {
    if(!$this->get_attribute('rows'))
      $this->set_attribute('rows', RICHEDIT_DEFAULT_ROWS);

    if(!$this->get_attribute('cols'))
      $this->set_attribute('cols', RICHEDIT_DEFAULT_COLS);
      
    if(!$width = $this->get_attribute('width'))
      $this->set_attribute('width', RICHEDIT_DEFAULT_WIDTH);

    if(!$height = $this->get_attribute('height'))
      $this->set_attribute('height', RICHEDIT_DEFAULT_HEIGHT);
  }
}

?>