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

class admin_list_tag_info
{
  var $tag = 'admin:list';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_tag';
}

register_tag(new  admin_list_tag_info());

class  admin_list_tag extends compiler_directive_tag
{
  function pre_generate(&$code)
  {
    $code->write_html("
      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"data-table\" width=\"100%\">
    ");
    $this->_write_header($code);
    $code->write_html("
      <tr>
        <td class=\"body\">
           <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"data\" width=\"100%\">
    ");
    
    if($first_cell =& $this->find_child_by_class('admin_list_cell_tag'))
      $first_cell->set_attributes(array('is_first' => '1'));
  }

  function post_generate(&$code)
  {
    $code->write_html("
            </table>
          </td>
        </tr>");
    $this->_write_footer($code);
    $code->write_html("
      </table>
    ");
  }
  function _write_header(&$code)
  {
    $code->write_html("
      <tr>
        <td class=\"head\">
          <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
          <tr>
            <td><img src='/shared/images/icon/common/24/table.gif'></td>
            <td class=\"title\" nowrap>[
    ");
    
    if($title =& $this->find_child_by_class('admin_list_title_tag'))
      $title->generate_now($code);
    else
      $code->write_html('&nbsp;');
    
    $code->write_html("
            ]</td>
            <td align=\"right\" width=\"100%\">
            </td>
          </tr>
          </table>
        </td>
      </tr>
    ");

  }

  function _write_footer(&$code)
  {
    if(!$footer =& $this->find_child_by_class('admin_list_footer_tag'))
      return;
    $code->write_html("
      <tr>
        <td class=\"footer\">
          <!--BEGIN:[ footer ]-->
    ");

    $footer->generate_now($code);
    $code->write_html("
          <!--END:[ footer ]-->
        </td>
      </tr>
    ");

  }

}

?>