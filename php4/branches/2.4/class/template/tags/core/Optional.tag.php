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
class CoreOptionalTagInfo
{
  public $tag = 'core:OPTIONAL';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_optional_tag';
}

registerTag(new CoreOptionalTagInfo());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class CoreOptionalTag extends CompilerDirectiveTag
{
  public function preParse()
  {
    if (!isset($this->attributes['for']) ||  !$this->attributes['for'])
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'for',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $tempvar = $code->getTempVariable();
    $code->writePhp('$' . $tempvar . ' = trim(' . $this->getDataspaceRefCode() . '->get(\'' . $this->attributes['for'] . '\'));');
    $code->writePhp('if (!empty($' . $tempvar . ')) {');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>