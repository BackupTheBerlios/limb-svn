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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class SelectorTagInfo
{
  var $tag = 'selector';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'selector_tag';
}

registerTag(new SelectorTagInfo());

class SelectorTag extends ControlTag
{
  function SelectorTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_checkbox_component';
  }

  function prepare()
  {
    $this->attributes['type'] = 'checkbox';

    if(!isset($this->attributes['selector_name']))
      $this->attributes['name'] = 'selector_name';
    else
      $this->attributes['name'] = $this->attributes['selector_name'];

  unset($this->attributes['selector_name']);
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function preGenerate($code)
  {
    $name = '$' . $code->getTempVariable();
    $parent = $this->getDataspaceRefCode();
    $ref = $this->getComponentRefCode();

    $code->writePhp("
    if ({$name} = {$parent}->get('" . $this->attributes['name']. "'))
      {$ref}->set_attribute('name', {$name});
    ");

    parent :: preGenerate($code);
  }
}

?>