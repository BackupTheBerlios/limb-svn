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
class UserAttributeTagInfo
{
  var $tag = 'user:ATTRIBUTE';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'user_attribute_tag';
}

registerTag(new UserAttributeTagInfo());

class UserAttributeTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (!isset($this->attributes['name']) ||  !$this->attributes['name'])
    {
      return new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents($code)
  {
    $code->writePhp("\$toolkit =& Limb :: toolkit();\$user =& \$toolkit->getUser(); echo \$user->get('{$this->attributes['name']}');");

    parent :: generateContents($code);
  }
}

?>