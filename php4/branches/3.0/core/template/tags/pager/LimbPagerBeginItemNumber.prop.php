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
    new PropertyInfo('BeginItemNumber', 'limb:pager:NAVIGATOR', 'LimbPagerBeginItemNumberProperty'), __FILE__);

class LimbPagerBeginItemNumberProperty extends CompilerProperty
{
  var $context;

  function LimbPagerBeginItemNumberProperty(&$context)
  {
    $this->context =& $context;
  }

  function generateExpression(&$code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getDisplayedPageBeginItem()');
  }
}

?>