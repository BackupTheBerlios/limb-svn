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
$taginfo =& new TagInfo('limb:REPEAT', 'LimbRepeatTag');
TagDictionary::registerTag($taginfo, __FILE__);

class LimbRepeatTag extends CompilerDirectiveTag
{
  function generateContents(&$code)
  {
    $counter = '$' . $code->getTempVariable();

    $value = $this->getAttribute('value');

    $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>