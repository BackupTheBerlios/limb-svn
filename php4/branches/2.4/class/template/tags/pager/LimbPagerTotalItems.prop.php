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
    new PropertyInfo('TotalItems', 'limb:pager:NAVIGATOR', 'LimbPagerTotalItemsProperty'), __FILE__);

class LimbPagerTotalItemsProperty extends CompilerProperty
{
  var $context;

  function LimbPagerTotalItemsProperty(&$context)
  {
    $this->context =& $context;
  }

  function generateExpression(&$code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getTotalItems()');
  }
}

?>