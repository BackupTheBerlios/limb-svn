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
require_once(LIMB_DIR . '/class/template/compiler/ServerComponentTag.class.php');

class SysParamTagInfo
{
  var $tag = 'core:SYS_PARAM';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'sys_param_tag';
}

registerTag(new SysParamTagInfo());

class SysParamTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/sys_param_component';
  }

  function generateContents($code)
  {
    if(isset($this->attributes['name']) &&  isset($this->attributes['type']))
    {
      $code->writePhp(
        $this->getComponentRefCode() . '->get_param("' . $this->attributes['name'] . '","' . $this->attributes['type'] . '");');
    }

    parent :: generateContents($code);
  }
}

?>