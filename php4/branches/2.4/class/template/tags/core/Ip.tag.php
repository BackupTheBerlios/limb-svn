<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class IpTagInfo
{
  var $tag = 'core:IP';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'ip_tag';
}

registerTag(new IpTagInfo());

class IpTag extends CompilerDirectiveTag
{
  function generateContents($code)
  {
    if(isset($this->attributes['hash_id']))
    {
      $code->writePhp(
        'echo ip :: decode_ip(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['hash_id'] . '"));');
    }
    else
      $code->writePhp('echo sys :: client_ip();');
  }
}

?>