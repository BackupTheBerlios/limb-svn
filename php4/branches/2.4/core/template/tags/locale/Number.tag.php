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
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/locale/LocaleNumberComponent.class.php';
  var $runtimeComponentName = 'LocaleNumberComponent';

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