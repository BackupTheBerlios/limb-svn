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
$taginfo =& new TagInfo('limb:locale:NUMBER', 'LimbLocaleNumberTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

require_once(LIMB_DIR . '/core/i18n/Locale.class.php');

class LimbLocaleNumberTag extends CompilerDirectiveTag
{
	function preParse() 
  {
		return PARSER_FORBID_PARSING;
	}

	function CheckNestingLevel() 
  {
		if ($this->findParentByClass('LocaleNumberFormatTag')) 
		  $this->raiseCompilerError('BADSELFNESTING');
	}
  
  function generateContents(&$code)
  {
    $locale =& $this->_getLocale();
    
    if(!$fract_digits = $this->getAttribute('fract_digits'))
      $fract_digits = $locale->fract_digits;

    if(!$decimal_symbol = $this->getAttribute('decimal_symbol'))
      $decimal_symbol = $locale->decimal_symbol;

    if(!$thousand_separator = $this->getAttribute('thousand_separator'))
    {
      $thousand_separator = $locale->thousand_separator;
    }  
      
    if($text_node = $this->findChildByClass('TextNode'))
    {
      $content =& $text_node->contents; 
      
      $code->writeHTML( number_format($content, $fract_digits, $decimal_symbol, $thousand_separator));
    }
    elseif($value = $this->getAttribute('value'))
    {
      $code->writeHTML( number_format($value, $fract_digits, $decimal_symbol, $thousand_separator));
    }
    
    $this->removeChildren();
  }
  
  function & _getLocale()
  {
    $toolkit =& Limb :: toolkit();
    
    if($locale = $this->getAttribute('locale'))
      return $toolkit->getLocale($locale);
    
    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';

    return $toolkit->getLocale(constant($locale_constant));
  }

	function removeChildren() 
  {
		foreach(array_keys($this->children) as $key) 
			unset($this->children[$key]);
	}  
}

?>