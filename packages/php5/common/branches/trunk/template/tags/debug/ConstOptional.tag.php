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
  protected $const;

  public function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->const = $this->attributes['name'];

    return PARSER_REQUIRE_PARSING;
  }

  public function preGenerate($code)
  {
    $value = 'true';
    if (isset($this->attributes['value']) &&  !(boolean)$this->attributes['value'])
      $value = 'false';

    $code->writePhp('if (defined("' . $this->const . '") && (constant("' . $this->const . '")) === ' . $value . ') {');

    parent::preGenerate($code);
  }

  public function postGenerate($code)
  {
    parent::postGenerate($code);
    $code->writePhp('}');
  }
}

?>