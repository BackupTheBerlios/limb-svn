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
class CoreDefaultTagInfo
{
  public $tag = 'core:DEFAULT';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_default_tag';
}

registerTag(new CoreDefaultTagInfo());

/**
* Allows a default action to take place at runtime, should a
* dataspace variable have failed to be populated
*/
class CoreDefaultTag extends CompilerDirectiveTag
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
    $code->writePhp('if (empty($' . $tempvar . ')) {');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>