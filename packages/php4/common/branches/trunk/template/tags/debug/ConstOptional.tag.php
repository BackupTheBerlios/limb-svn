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

class ConstOptionalTagInfo
{
  public $tag = 'const:OPTIONAL';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'const_optional_tag';
}

registerTag(new ConstOptionalTagInfo());

class ConstOptionalTag extends CompilerDirectiveTag
{
  var $const;

  function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      return new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->const = $this->attributes['name'];

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    $value = 'true';
    if (isset($this->attributes['value']) &&  !(boolean)$this->attributes['value'])
      $value = 'false';

    $code->writePhp('if (defined("' . $this->const . '") && (constant("' . $this->const . '")) === ' . $value . ') {');

    parent::preGenerate($code);
  }

  function postGenerate($code)
  {
    parent::postGenerate($code);
    $code->writePhp('}');
  }
}

?>