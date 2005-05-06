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
class ip_tag_info
{
  var $tag = 'core:IP';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'ip_tag';
}

register_tag(new ip_tag_info());

class ip_tag extends compiler_directive_tag
{
  function generate_contents(&$code)
  {
    if(isset($this->attributes['hash_id']))
    {
      $code->register_include(LIMB_DIR . '/core/lib/http/ip.class.php');
      $code->write_php(
        'echo ip :: decode_ip(' . $this->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '"));');
    }
    else
    {
      $code->register_include(LIMB_DIR . '/core/lib/system/sys.class.php');
      $code->write_php('echo sys :: client_ip();');
    }
  }
}

?>