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
class HtmlspecialcharsTagInfo
{
  var $tag = 'core:HTMLSPECIALCHARS';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'htmlspecialchars_tag';
}

registerTag(new HtmlspecialcharsTagInfo());

class HtmlspecialcharsTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (! array_key_exists('hash_id', $this->attributes) ||
        empty($this->attributes['hash_id']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'hash_id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    return PARSER_FORBID_PARSING;
  }

  function generateContents($code)
  {
    if(isset($this->attributes['hash_id']))
    {
      $code->writePhp(
        'echo htmlspecialchars(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['hash_id'] . '"), ENT_QUOTES);');
    }
  }
}

?>