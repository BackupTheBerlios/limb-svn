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
class LabelTagInfo
{
  var $tag = 'label';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'label_tag';
}

registerTag(new LabelTagInfo());

/**
* Compile time component for building runtime form labels
*/
class LabelTag extends ServerTagComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/label_component';
  }

  function checkNestingLevel()
  {
    if ($this->findParentByClass('label_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!$this->findParentByClass('form_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generateConstructor($code)
  {
    parent::generateConstructor($code);
    if (array_key_exists('error_class', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->error_class = \'' . $this->attributes['error_class'] . '\';');
    unset($this->attributes['error_class']);
    }
    if (array_key_exists('error_style', $this->attributes))
    {
      $code->writePhp($this->getComponentRefCode() . '->error_style = \'' . $this->attributes['error_style'] . '\';');
    unset($this->attributes['error_style']);
    }
  }
}

?>