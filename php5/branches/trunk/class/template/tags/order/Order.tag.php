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
class OrderTagInfo
{
  public $tag = 'order';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'order_tag';
}

registerTag(new OrderTagInfo());

class OrderTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/order_component';
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . "->import(" . $this->parent->getDataspaceRefCode() . "->export());\n");

    $code->writePhp($this->getComponentRefCode() . '->prepare();'."\n");
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>