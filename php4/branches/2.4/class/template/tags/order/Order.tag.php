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
  var $tag = 'order';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'order_tag';
}

registerTag(new OrderTagInfo());

class OrderTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/order_component';
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . "->import(" . $this->parent->getDataspaceRefCode() . "->export());\n");

    $code->writePhp($this->getComponentRefCode() . '->prepare();'."\n");
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>