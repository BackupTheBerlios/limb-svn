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
$taginfo =& new TagInfo('limb:LOCALE', 'LimbLocaleTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);
 
class LimbLocaleTag extends CompilerDirectiveTag
{
  function preParse()
  {
    $name = $this->getAttribute('name');
    if (empty($name))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'name'));
    }

    return PARSER_REQUIRE_PARSING;
  }
  
  function preGenerate(&$code)
  {
    parent::preGenerate($code);

    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
        $locale_constant = 'CONTENT_LOCALE_ID';

    $name = $this->getAttribute('name');
    $code->writePhp('if ("' . $name. '" == constant("'. $locale_constant .'")) {');
  }

  function postGenerate(&$code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>