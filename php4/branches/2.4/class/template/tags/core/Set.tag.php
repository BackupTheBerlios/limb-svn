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
class CoreSetTagInfo
{
  var $tag = 'core:SET';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_set_tag';
}

registerTag(new CoreSetTagInfo());

/**
* Sets a variable in the runtime dataspace, according the attributes of this
* tag at compile time.
*/
class CoreSetTag extends SilentCompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('core_set_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preParse()
  {
    $dataspace = $this->getDataspace();
    $dataspace->vars += $this->attributes;
    return PARSER_FORBID_PARSING;
  }
}

?>