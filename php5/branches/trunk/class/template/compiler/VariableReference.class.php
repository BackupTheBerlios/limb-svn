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

/**
* Used to allow PHP variable references to be copied from straight from the source
* to the compiled template file.
*/
class VariableReference extends CompilerComponent
{
  /**
  * Reference of variable
  */
  public $reference;
  /**
  * Scope of variable
  */
  public $scope;

  /**
  * Generate the code
  */
  public function generate($code)
  {
    // This has to find parental namespaces, not parental components
    switch ($this->scope)
    {
      case '#':
        $context = $this->getRootDataspace();
        break;
      case '^':
        $context = $this->getParentDataspace();
        break;
      case '$':
        $context = $this->getDataspace();
        break;
    }
    if ($context != null)
    {
      if (array_key_exists($this->reference, $context->vars))
      {
        $code->writeHtml($context->vars[$this->reference]);
      }
      elseif(strpos($this->reference, '[') !== false)
      {
        $index = addslashes(preg_replace('/^([^\[\]]+)(\[.*\])+$/', "['\\1']\\2", $this->reference));
        $code->writePhp('echo ' . $context->getDataspaceRefCode() . '->get_by_index_string(\'' . $index . '\');');
      }
      else
      {
        $code->writePhp('echo ' . $context->getDataspaceRefCode() . '->get(\'' . $this->reference . '\');');
      }
    }
  }
}

?>