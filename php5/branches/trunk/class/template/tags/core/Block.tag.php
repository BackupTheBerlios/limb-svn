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
  public $tag = 'core:BLOCK';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_block_tag';
}

registerTag(new CoreBlockTagInfo());

class CoreBlockTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/block_component';
  }

  public function generateConstructor($code)
  {
    parent::generateConstructor($code);
    if (array_key_exists('hide', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->visible = false;');
    }
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);
    $code->writePhp('if (' . $this->getComponentRefCode() . '->is_visible()) {');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>