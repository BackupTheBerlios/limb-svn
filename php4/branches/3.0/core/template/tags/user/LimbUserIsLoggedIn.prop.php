<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RowNumber.prop.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
PropertyDictionary::registerProperty(
    new PropertyInfo('UserIsLoggedIn', 'limb:USER', 'LimbUserIsLoggedInProperty'), __FILE__);

// Limb list row number property takes into account that DataSet can be paged with pager
// and begins counter from pager->getStartingItem()

class LimbUserIsLoggedInProperty extends CompilerProperty
{
  var $tempvar;

  function generateScopeEntry(&$code)
  {
    $this->tempvar = $code->getTempVarRef();

    $toolkit_var = $code->getTempVarRef();
    $user_var = $code->getTempVarRef();

    $code->writePHP($toolkit_var . " =& Limb :: toolkit();\n");
    $code->writePHP($user_var . " =& ". $toolkit_var . "->getUser();\n");

    $code->writePHP('if ('. $user_var . "->isLoggedIn())\n");
    $code->writePHP($this->tempvar . " = true;\n");
    $code->writePHP("else\n");
    $code->writePHP($this->tempvar . " = false;\n");
  }

  function generateExpression(&$code)
  {
    $code->writePHP($this->tempvar);
  }
}

?>