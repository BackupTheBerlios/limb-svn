<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: selector.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class JsSelectorTagInfo
{
  var $tag = 'js_selector';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'js_selector_tag';
}

registerTag(new JsSelectorTagInfo());

class JsSelectorTag extends ControlTag
{
  function JsSelectorTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/js_checkbox_component';
  }

  function prepare()
  {
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
    $this->attributes['type'] = 'hidden';

    $name = '$' . $code->getTempVariable();
    $parent = $this->getDataspaceRefCode();
    $ref = $this->getComponentRefCode();

    $code->writePhp("

    if ({$name} = {$parent}->get('" . $this->attributes['name']. "'))
      {$ref}->set_attribute('name', {$name});
    ");

    parent :: preGenerate($code);
  }

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_js_checkbox();');
  }
}

?>