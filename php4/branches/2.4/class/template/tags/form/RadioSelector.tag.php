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
class RadioSelectorTagInfo
{
  var $tag = 'radio_selector';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'radio_selector_tag';
}

registerTag(new RadioSelectorTagInfo());

class RadioSelectorTag extends CompilerDirectiveTag
{
  function preGenerate($code)
  {
    $value = '$' . $code->getTempVariable();
    $parent = $this->getDataspaceRefCode();

    $radio_child = $this->findChildByClass('input_tag');
    $label_child = $this->findChildByClass('label_tag');

    $radio = $radio_child->getComponentRefCode();
    $label = $label_child->getComponentRefCode();


    $code->writePhp("
    if ({$value} = {$parent}->get('id'))
    {
      {$radio}->set_attribute('value', {$value});
      {$radio}->set_attribute('id', {$value});
      {$label}->set_attribute('for', {$value});
    }
    ");

    parent :: preGenerate($code);
  }
}

?>