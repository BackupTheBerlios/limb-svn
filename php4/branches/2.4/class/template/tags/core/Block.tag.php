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
class CoreBlockTagInfo
{
  var $tag = 'core:BLOCK';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_block_tag';
}

registerTag(new CoreBlockTagInfo());

class CoreBlockTag extends ServerComponentTag
{
  function CoreBlockTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/block_component';
  }

  function generateConstructor($code)
  {
    parent::generateConstructor($code);
    if (array_key_exists('hide', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->visible = false;');
    }
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);
    $code->writePhp('if (' . $this->getComponentRefCode() . '->is_visible()) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>