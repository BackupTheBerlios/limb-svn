<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: action.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

class admin_list_header_tag_info
{
  var $tag = 'admin:list:header';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_header_tag';
}

register_tag(new  admin_list_header_tag_info());

class  admin_list_header_tag extends compiler_directive_tag
{
  function generate_contents(&$code)
  {
    $title =& $this->find_child_by_class('admin_list_title_tag');
    $filter =& $this->find_child_by_class('admin_list_filter_tag');
    $code->write_html("<span");
    if($filter)
      $code->write_html(" behavior='CShowHide'");
    $code->write_html(">");
    $code->write_html("
          <table border='0' cellspacing='0' cellpadding='0' class='datatable'>
          <tr>
            <td>
              <table width='100%' border='0' cellspacing='0' cellpadding='0' class='header'>
              <tr>
                <td class='title' width='100%'>
    ");
    if($title)
      $title->generate($code);
    else
      $code->write_html('&nbsp;');

    $code->write_html("</td>");
    if($filter)
    {
      $code->write_html("
               <td style='padding:4px 4px 0 4px' class='bgr' valign='bottom'>
                  <span id='minus'>
                  <table width='100%' border='0' cellspacing='0' cellpadding='0' class='filter'>
                  <tr>
                    <td style='padding:2px 1px 2px 5px'><img src='/shared/images/marker/minus.gif'></td>
                    <td style='padding:2px 5px 2px 4px'>" . strings :: get('filter'). "</td>
                  </tr>
                  </table>
                  </span>
                </td>
      ");
    }

    $code->write_html("
              </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style='padding:5px'>
    ");
    if($filter)
    {
      $code->write_html("<span id='body'>");
      $filter->generate($code);
      $code->write_html("</span>");
    }
  }

}

?>