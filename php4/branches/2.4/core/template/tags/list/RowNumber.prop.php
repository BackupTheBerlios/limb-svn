<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbPagerTotalPages.prop.php 1021 2005-01-15 10:51:25Z pachanga $
*
***********************************************************************************/
PropertyDictionary::registerProperty(
    new PropertyInfo('LimbListRowNumber', 'list:ITEM', 'LimbListRowNumberProperty'), __FILE__);

// Limb list row number property takes into account that DataSet can be paged with pager
// and begins counter from pager->getStartingItem()

class LimbListRowNumberProperty extends CompilerProperty
{
  var $tempvar;
  var $hasIncrement = FALSE;
  var $context;

  function LimbListRowNumberProperty(&$context)
  {
    $this->context =& $context;
  }

  function generateScopeEntry(&$code)
  {
    $this->tempvar = $code->getTempVarRef();

    $pager_var = $code->getTempVariable();

    $code->writePHP('if (!empty(' . $this->context->getComponentRefCode() . '->dataSet->pager))' . "\n");
    $code->writePHP($this->tempvar . ' = ' .
                    $this->context->getComponentRefCode() . '->dataSet->pager->getStartingItem() - 1;' . "\n");
    $code->writePHP('else' . "\n");
    $code->writePHP($this->tempvar . ' = 0;');
  }

  function generateExpression(&$code)
  {
    if (!$this->hasIncrement)
    {
      $code->writePHP('++');
      $this->hasIncrement = TRUE;
    }
    $code->writePHP($this->tempvar);
  }
}

?>