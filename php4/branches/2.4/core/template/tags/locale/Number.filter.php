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
  var $locale_var;

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

  function generatePreStatement(&$code)
  {
    $toolkit_var = $code->getTempVarRef();
    $locale_value_var = $code->getTempVarRef();
    $this->locale_var = $code->getTempVarRef();

    $code->writePHP($toolkit_var . ' =& Limb :: toolkit();' . "\n");
    $code->writePHP($locale_value_var . ' = ');

    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $this->parameters[1]->generateExpression($code);
    elseif(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $locale_type = $this->parameters[0]->getValue();

      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';

      $code->writePHP(' constant("' . $locale_constant . '")');
    }
    else
    {
      $locale_constant = 'CONTENT_LOCALE_ID';
      $code->writePHP(' constant("' . $locale_constant . '")');
    }

    $code->writePHP(';');

    $code->writePHP($this->locale_var . ' =& ' . $toolkit_var . '->getLocale(' . $locale_value_var .' );' . "\n");
  }

  function generateExpression(&$code)
  {
    $code->writePHP('number_format(');
    $this->base->generateExpression($code);
    $code->writePHP(',');

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $this->parameters[2]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->fract_digits');

    $code->writePHP(',');

    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      $this->parameters[3]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->decimal_symbol');

    $code->writePHP(',');

    if(isset($this->parameters[4]) && $this->parameters[4]->getValue())
      $this->parameters[4]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->thousand_separator');

    $code->writePHP(')');
  }
}

?>