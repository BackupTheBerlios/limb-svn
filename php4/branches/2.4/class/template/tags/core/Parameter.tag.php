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
class CoreParameterTagInfo
{
  var $tag = 'core:PARAMETER';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_parameter_tag';
}

registerTag(new CoreParameterTagInfo());

class CoreParameterTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!isset($this->attributes['value']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'value',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_FORBID_PARSING;
  }

  function checkNestingLevel()
  {
    if (!$this->parent instanceof ServerComponentTag)
    {
      throw new WactException('wrong parent tag',
          array('tag' => $this->tag,
          'parent_class' => get_class($this->parent),
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preGenerate($code)
  {
    if(!isset($this->attributes['type']))
      $this->attributes['type'] = 'string';

    parent::preGenerate($code);
  }

  function generateContents($code)
  {
    $value = $this->_typecastValue();

    $code->writePhp($this->parent->getComponentRefCode()
      . '->set_parameter("' . $this->attributes['name'] . '", '
      . var_export($value, true) . ')');

    parent::generateContents($code);
  }

  function _typecastValue()
  {
    $value = $this->attributes['value'];
    switch(strtolower($this->attributes['type']))
    {
      case 'numeric':
        return $value*1;
      break;
      case 'boolean':
        return (bool)$value;
      break;
      case 'string':
        return $value;
      break;
      default:
        return $value;
    }
  }
}

?>