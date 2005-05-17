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

class admin_list_item_tag_info
{
  var $tag = 'admin:list:item';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_item_tag';
}

register_tag(new  admin_list_item_tag_info());

class  admin_list_item_tag extends compiler_directive_tag
{
  function pre_generate(&$code)
  {
    $code->write_php('if (!(' . $this->get_dataspace_ref_code() . '->get_counter()%2)) {');
    $code->write_html("<tr class=\"row2\">");
    $code->write_php('}else{');
    $code->write_html("<tr class=\"row2\">");
    $code->write_php('}');
  }

  function post_generate(&$code)
  {
    $code->write_html("
      </tr>
    ");
  }
}
?>