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
  public $tag = 'selector';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'selector_tag';
}

registerTag(new SelectorTagInfo());

class SelectorTag extends ControlTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/input_checkbox_component';
  }

  public function prepare()
  {
    $this->attributes['type'] = 'checkbox';

    if(!isset($this->attributes['selector_name']))
      $this->attributes['name'] = 'selector_name';
    else
      $this->attributes['name'] = $this->attributes['selector_name'];

  unset($this->attributes['selector_name']);
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
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