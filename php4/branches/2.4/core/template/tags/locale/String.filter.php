<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: String.tag.php 1043 2005-01-21 08:57:12Z seregalimb $
*
***********************************************************************************/

FilterDictionary::registerFilter(new FilterInfo('LimbI18NString', 'LimbLocaleStringFilter', 1, 3), __FILE__);

class LimbLocaleStringFilter extends CompilerFilter
{
  function getValue()
  {
    require_once(LIMB_DIR . '/core/i18n/Strings.class.php');

    $locale_value = $this->_getLocale();

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $file = $this->parameters[0]->getValue();
    else
      $file = 'common';

    $value = $this->base->getValue();

    if ($this->isConstant())
      return  Strings :: get($value, $file, $locale_value);
    else
      RaiseError('compiler', 'UNRESOLVED_BINDING');
  }

  function _getLocale()
  {
    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      return $this->parameters[2]->getValue();
    elseif(isset($this->parameters[1]) && $this->parameters[1]->getValue())
    {
      $locale_type = $this->parameters[1]->getValue();

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
    $code->registerInclude(LIMB_DIR . '/core/i18n/Strings.class.php');

    $code->writePHP('Strings :: get(');
    $this->base->generateExpression($code);
    $code->writePHP(',');

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $this->parameters[0]->generateExpression($code);
    }
    else
      $code->writePHP('"common"');

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
    {
      $code->writePHP(',');
      $this->parameters[2]->generateExpression($code);
    }
    elseif(isset($this->parameters[1]) && $this->parameters[1]->getValue())
    {
      $locale_type = $this->parameters[1]->getValue();

      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';

      $code->writePHP(', constant("' . $locale_constant . '")');
    }
    else
    {
      $locale_constant = 'CONTENT_LOCALE_ID';
      $code->writePHP(', constant("' . $locale_constant . '")');
    }

    $code->writePHP(')');
  }
}

?>