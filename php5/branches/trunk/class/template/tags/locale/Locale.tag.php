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
class LocaleLocaleTagInfo
{
  public $tag = 'locale:LOCALE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'locale_locale_tag';
}

registerTag(new LocaleLocaleTagInfo());

class LocaleLocaleTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (!isset($this->attributes['name']) ||  !$this->attributes['name'])
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    if(isset($this->attributes['locale_type']))
    {
      if(strtolower($this->attributes['locale_type']) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
        $locale_constant = 'CONTENT_LOCALE_ID';

    $code->writePhp('if ("' . $this->attributes['name']. '" == constant("'. $locale_constant .'")) {');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>