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

class admin_list_footer_tag_info
{
  var $tag = 'admin:list:footer';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_footer_tag';
}

register_tag(new  admin_list_footer_tag_info());

class  admin_list_footer_tag extends compiler_directive_tag
{
  function pre_generate(& $code)
  {
    $code->write_html("
            </td>
          </tr>
          <tr>
            <td>
    ");
  }

  function post_generate(& $code)
  {
    $code->write_html("
            </td>
          </tr>
          </table>
          </span>
    ");
  }


}

?>