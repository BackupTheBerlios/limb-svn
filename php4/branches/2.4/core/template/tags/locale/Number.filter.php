<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbIp.filter.php 1028 2005-01-18 11:06:55Z pachanga $
*
***********************************************************************************/
FilterDictionary::registerFilter(new FilterInfo('LimbI18NNumber', 'LimbLocaleNumberFilter', 0, 5), __FILE__);

class LimbLocaleNumberFilter extends CompilerFilter
{
  function getValue()
  {
    $value = $this->base->getValue();

    $locale_value = $this->_getLocale();

    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($locale_value);

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $fract_digits = $this->parameters[2]->getValue();
    else
      $fract_digits = $locale->fract_digits;

    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      $decimal_symbol = $this->parameters[3]->getValue();
    else
      $decimal_symbol = $locale->decimal_symbol;

    if(isset($this->parameters[4]) && $this->parameters[4]->getValue())
      $thousand_separator = $this->parameters[4]->getValue();
    else
      $thousand_separator = $locale->thousand_separator;

    if ($this->isConstant())
      return number_format($value, $fract_digits, $decimal_symbol, $thousand_separator);
    else
      RaiseError('compiler', 'UNRESOLVED_BINDING');
  }

  function _getLocale()
  {
    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      return $this->parameters[1]->getValue();
    elseif(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $locale_type = $this->parameters[0]->getValue();

      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';

    return constant($locale_constant);
  }

  function generateExpression(&$code)
  {
    $code->registerInclude(LIMB_DIR . '/core/http/Ip.class.php');

    $code->writePHP('Ip :: decode(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }


}

?>