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

class const_optional_tag_info
{
  public $tag = 'const:OPTIONAL';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'const_optional_tag';
}

register_tag(new const_optional_tag_info());

class const_optional_tag extends compiler_directive_tag
{
  protected $const;

  public function pre_parse()
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

  public function pre_generate($code)
  {
    $value = 'true';
    if (isset($this->attributes['value']) && !(boolean)$this->attributes['value'])
      $value = 'false';

    $code->write_php('if (defined("' . $this->const . '") && (constant("' . $this->const . '")) === ' . $value . ') {');

    parent::pre_generate($code);
  }

  public function post_generate($code)
  {
    parent::post_generate($code);
    $code->write_php('}');
  }
}

?>