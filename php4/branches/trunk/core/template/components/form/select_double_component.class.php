<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: select_multiple_component.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/components/form/select_multiple_component.class.php');

class select_double_component extends select_multiple_component 
{
  function _render_js()
  {
    if(defined('DOUBLE_SELECT_JS'))
      return ;
    define('DOUBLE_SELECT_JS',true);
    echo "<script type='text/javascript' src='/shared/js/selector.js'></script>";
  }

  function render_control()
  {
    $this->_render_js();
    $selector_name = 'doubleselect_' . $this->get_attribute('id');
    $field_name = $this->_process_name_attribute($this->get_field_name());
    
    echo "
      <script>
      var {$selector_name} = new DoubleSelect('{$field_name}');
      {$selector_name}.replaceSelect();
      </script>    
    ";
  }
}
?>