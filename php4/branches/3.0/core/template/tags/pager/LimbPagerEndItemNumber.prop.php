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

PropertyDictionary::registerProperty(
    new PropertyInfo('EndItemNumber', 'limb:pager:NAVIGATOR', 'LimbPagerEndItemNumberProperty'), __FILE__);

class LimbPagerEndItemNumberProperty extends CompilerProperty
{
  var $context;

  function LimbPagerEndItemNumberProperty(&$context)
  {
    $this->context =& $context;
  }

  function generateExpression(&$code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getDisplayedPageEndItem()');
  }
}

?>