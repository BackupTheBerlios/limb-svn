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
FilterDictionary::registerFilter(new FilterInfo('ip', 'LimbIpFilter', 0, 0), __FILE__);

class LimbIpFilter extends CompilerFilter
{
  function getValue()
  {
    include_once(LIMB_DIR . '/core/http/Ip.class.php');

    if ($this->isConstant())
      return Ip :: decode($this->base->getValue());
    else
      RaiseError('compiler', 'UNRESOLVED_BINDING');
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