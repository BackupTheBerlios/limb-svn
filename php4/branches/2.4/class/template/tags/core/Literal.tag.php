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
  var $tag = 'core:LITERAL';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_literal_tag';
}

registerTag(new CoreLiteralTagInfo());

/**
* Prevents a section of the template from being parsed, placing the contents
* directly into the compiled template
*/
class CoreLiteralTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('core_literal_tag'))
    {
      return throw(new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }

  function preParse()
  {
    return PARSER_FORBID_PARSING;
  }
}

?>