<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class admin_list_cell_tag_info
{
  var $tag = 'admin:list:cell';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_cell_tag';
}

register_tag(new  admin_list_cell_tag_info());

class  admin_list_cell_tag extends server_tag_component_tag 
{
  var $runtime_component_path = '/core/template/tag_component';

  function get_rendered_tag()
  {
    return 'td';
  }
  
  function pre_generate(&$code)
  {
    parent :: pre_generate($code);
    if(isset($this->attributes['is_first']))
      $code->write_html("<span behavior=\"CRow\" target=\"-2\"></span>");
  }

  function post_generate(&$code)
  {
    parent :: post_generate($code);
    $code->write_html("<td class=\"sep\"></td>");
  }
}
?>