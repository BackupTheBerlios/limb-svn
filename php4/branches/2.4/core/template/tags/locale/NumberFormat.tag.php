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
class LocaleNumberFormatTagInfo
{
  var $tag = 'locale:NUMBER_FORMAT';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'locale_number_format_tag';
}

registerTag(new LocaleNumberFormatTagInfo());

class LocaleNumberFormatTag extends ServerComponentTag
{
  var $field;

  function LocaleNumberFormatTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/locale_number_format_component';
  }

  function preParse()
  {
    if (!isset($this->attributes['hash_id']) ||  !$this->attributes['hash_id'])
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'hash_id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents($code)
  {
    $code->writePhp(
      'echo ' . $this->getComponentRefCode() . '->format(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['field'] . '"));');
  }
}

?>