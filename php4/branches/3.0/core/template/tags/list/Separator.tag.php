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
$taginfo =& new TagInfo('limb:list:SEPARATOR', 'LimbListSeparatorTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

// Limb list separator works different than WACT generic list separator.
// It's service depends on it's position.
// You MUST place separator at the END of the list item tag content.
// Step attribute is 1 by default

class LimbListSeparatorTag extends CompilerDirectiveTag
{
  var $step;
  var $counter_var;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbListSeparatorTag'))
    {
      $this->raiseCompilerError('BADSELFNESTING');
    }

    if (!$this->findParentByClass('ListItemTag'))
    {
      $this->raiseCompilerError('MISSINGENCLOSURE');
    }
  }

  function preParse()
  {
    if (!$step = $this->getAttribute('step'))
      $this->step = 1;
    else
      $this->step = $step;

    return PARSER_REQUIRE_PARSING;
  }

  function generateConstructor(&$code)
  {
    $code->writePhp($this->counter_var . ' = 0;' . "\n");

    parent :: generateConstructor(&$code);
  }

  function preGenerate(&$code)
  {
    parent::preGenerate($code);

    $this->counter_var =& $code->getTempVarRef();
    $total_var =& $code->getTempVarRef();

    $code->writePhp('if(empty(' . $this->counter_var . '))'. "\n");
    $code->writePhp($this->counter_var .' = 0; ' . "\n");

    $code->writePhp('if(empty(' . $total_var . '))'. "\n");
    $code->writePhp($total_var .' = ' . $this->getComponentRefCode() . '->dataSet->getTotalRowCount();' . "\n");

    $code->writePhp($this->counter_var . '++;'. "\n");

    $code->writePhp(
        'if (	(' . $this->counter_var . ' > 0) && (' . $this->getComponentRefCode() . '->dataSet->valid()) '.
              '&& ' . $this->counter_var . '< ' . $total_var .
              '&& (' . $this->counter_var . ' % ' . $this->step . ' == 0)) {'. "\n");
  }

  function postGenerate(&$code)
  {
    parent::postGenerate($code);

    $code->writePhp('}'. "\n");
  }
}

?>