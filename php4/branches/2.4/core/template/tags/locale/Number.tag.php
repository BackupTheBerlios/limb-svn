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

class LimbLocaleNumberTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/locale/LocaleNumberFormatComponent.class.php';
  var $runtimeComponentName = 'LocaleNumberFormatComponent';

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
    if($fract_digits = $this->getAttribute('fract_digits'))
      $code->writePhp($this->getComponentRefCode() . '->setFractDigits("'. $fract_digits .'");');

    if($thousand_separator = $this->getAttribute('thousand_separator'))
      $code->writePhp($this->getComponentRefCode() . '->setThousandSeparator("'. $thousand_separator .'");');

    if($decimal_symbol = $this->getAttribute('decimal_symbol'))
      $code->writePhp($this->getComponentRefCode() . '->setDecimalSymbol("'. $decimal_symbol .'");');

    $locale = $this->_getLocale();
    $code->writePhp($this->getComponentRefCode() . '->setLocale("'. $locale .'");');

    $value = $this->_getValue();

    $code->writePhp('echo' . $this->getComponentRefCode() . '->format("'. $value .'");');

    /*
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
    }*/

    $this->removeChildren();
  }

  function _getValue()
  {
    if($text_node = $this->findChildByClass('TextNode'))
      return $text_node->contents;
    elseif($value = $this->getAttribute('value'))
      return $value;
    else
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'value'));

  }

  function _getLocale()
  {
    if($locale = $this->getAttribute('locale'))
      return $locale;

    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';

    return constant($locale_constant);
  }

  function removeChildren()
  {
    foreach(array_keys($this->children) as $key)
      unset($this->children[$key]);
  }
}

?>