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
    new PropertyInfo('TotalPages', 'limb:pager:NAVIGATOR', 'LimbPagerTotalPagesProperty'), __FILE__);

class LimbPagerTotalPagesProperty extends CompilerProperty
{
  var $context;

  function LimbPagerTotalPagesProperty(&$context)
  {
    $this->context =& $context;
  }

  function generateExpression(&$code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getTotalPages()');
  }
}

?>