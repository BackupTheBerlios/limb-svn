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
  public $tag = 'locale:NUMBER_FORMAT';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'locale_number_format_tag';
}

registerTag(new LocaleNumberFormatTagInfo());

class LocaleNumberFormatTag extends ServerComponentTag
{
  protected $field;

  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/locale_number_format_component';
  }

  public function preParse()
  {
    if (!isset($this->attributes['hash_id']) ||  !$this->attributes['hash_id'])
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'hash_id',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  public function generateContents($code)
  {
    $code->writePhp(
      'echo ' . $this->getComponentRefCode() . '->format(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['field'] . '"));');
  }
}

?>