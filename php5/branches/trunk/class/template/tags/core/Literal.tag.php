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
class CoreLiteralTagInfo
{
  public $tag = 'core:LITERAL';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_literal_tag';
}

registerTag(new CoreLiteralTagInfo());

/**
* Prevents a section of the template from being parsed, placing the contents
* directly into the compiled template
*/
class CoreLiteralTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if ($this->findParentByClass('core_literal_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preParse()
  {
    return PARSER_FORBID_PARSING;
  }
}

?>